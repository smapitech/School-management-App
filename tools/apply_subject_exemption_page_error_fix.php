<?php
// In-place patcher for subject exemption page loading errors.
// It preserves unrelated live edits and replaces only subject-exemption-specific blocks.
$root = rtrim($argv[1] ?? getcwd(), '/');
$appPath = $root . '/app/App.php';
$repoPath = $root . '/app/Repository.php';
$modulesPath = $root . '/app/Modules.php';
$navPath = $root . '/resources/views/classes/nav.php';

function patch_file(string $path, callable $patcher): void {
    if (!is_file($path)) {
        echo "Skip missing {$path}\n";
        return;
    }
    $code = file_get_contents($path);
    $new = $patcher($code);
    if ($new !== $code) {
        file_put_contents($path, $new);
        echo "Patched {$path}\n";
    } else {
        echo "No change {$path}\n";
    }
}

$subjectExemptionsMethod = <<<'PHP_CODE'
    private function subjectExemptions(): void
    {
        $user = Auth::user() ?? [];
        if (($user['role'] ?? '') === 'teacher') {
            $this->forbidden();
            return;
        }
        $this->requirePermission('classes', 'edit');

        $classOptions = [];
        try {
            $classOptions = $this->classOptions();
        } catch (\Throwable $exception) {
            error_log('[Subject exemption class options failed] ' . $exception->getMessage());
        }

        $filters = [
            'class_name' => trim((string) ($_GET['class_name'] ?? '')),
            'section' => trim((string) ($_GET['section'] ?? '')),
            'subject_id' => (int) ($_GET['subject_id'] ?? 0),
            'school_term' => trim((string) ($_GET['school_term'] ?? '')),
            'school_session' => trim((string) ($_GET['school_session'] ?? '')),
        ];

        if ($filters['school_term'] === '') {
            $filters['school_term'] = trim((string) ($this->schoolTerms()[0] ?? ''));
        }
        if ($filters['school_session'] === '') {
            $filters['school_session'] = trim((string) ($this->academicYears()[0] ?? ''));
        }

        $errors = $_SESSION['subject_exemption_errors'] ?? [];
        $success = $_SESSION['subject_exemption_success'] ?? '';
        unset($_SESSION['subject_exemption_errors'], $_SESSION['subject_exemption_success']);

        if ($filters['class_name'] !== '' && $classOptions && !in_array($filters['class_name'], $classOptions, true)) {
            $errors[] = 'Please select a valid class.';
            $filters['class_name'] = '';
        }

        $subjects = [];
        $students = [];
        $exemptions = [];

        if ($filters['class_name'] !== '') {
            try {
                $subjects = $this->repository->subjectsForClass($filters['class_name'], $filters['section']);
            } catch (\Throwable $exception) {
                error_log('[Subject exemption subjects load failed] ' . $exception->getMessage());
                $errors[] = 'Subjects could not be loaded for the selected class. Please check class subject assignment.';
            }

            try {
                $studentFilters = ['class_name' => $filters['class_name']];
                if ($filters['section'] !== '') {
                    $studentFilters['section'] = $filters['section'];
                }
                $students = $this->repository->admittedStudents($studentFilters);
            } catch (\Throwable $exception) {
                error_log('[Subject exemption students load failed] ' . $exception->getMessage());
                $errors[] = 'Students could not be loaded for the selected class.';
            }
        }

        if ($filters['class_name'] !== '' && $filters['subject_id'] > 0) {
            try {
                $exemptions = $this->repository->studentSubjectExemptions($filters);
            } catch (\Throwable $exception) {
                error_log('[Subject exemption records load failed] ' . $exception->getMessage());
                $errors[] = 'Saved exemptions could not be loaded. The page is still available so you can save again.';
                $exemptions = [];
            }
        }

        $this->render('classes/subject_exemptions', [
            'title' => 'Student Subject Exemptions',
            'classOptions' => $classOptions,
            'sections' => $this->sections(),
            'schoolTerms' => $this->schoolTerms(),
            'schoolSessions' => $this->academicYears(),
            'filters' => $filters,
            'subjects' => $subjects,
            'students' => $students,
            'exemptions' => $exemptions,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    private function saveSubjectExemptions(): void
    {
        $user = Auth::user() ?? [];
        if (($user['role'] ?? '') === 'teacher') {
            $this->forbidden();
            return;
        }
        $this->requirePermission('classes', 'edit');
        $this->verifyCsrfOrForbidden();

        $filters = [
            'class_name' => trim((string) ($_POST['class_name'] ?? '')),
            'section' => trim((string) ($_POST['section'] ?? '')),
            'subject_id' => (int) ($_POST['subject_id'] ?? 0),
            'school_term' => trim((string) ($_POST['school_term'] ?? '')),
            'school_session' => trim((string) ($_POST['school_session'] ?? '')),
        ];
        $studentIds = array_values(array_unique(array_filter(array_map('intval', (array) ($_POST['student_ids'] ?? [])), static fn (int $id): bool => $id > 0)));
        $reason = trim((string) ($_POST['reason'] ?? '')) ?: 'Subject exemption';
        $errors = [];

        if ($filters['class_name'] === '' || !in_array($filters['class_name'], $this->classOptions(), true)) {
            $errors[] = 'Please select a valid class.';
        }
        if ($filters['subject_id'] <= 0 || !$this->repository->subjectById($filters['subject_id'])) {
            $errors[] = 'Please select a valid subject.';
        }
        if ($filters['school_term'] === '') {
            $errors[] = 'Please select a term.';
        }
        if ($filters['school_session'] === '') {
            $errors[] = 'Please select a session.';
        }

        if ($errors) {
            $_SESSION['subject_exemption_errors'] = $errors;
            $this->redirect('/classes/subject-exemptions?' . http_build_query($filters));
        }

        try {
            $saved = $this->repository->syncStudentSubjectExemptions($filters, $studentIds, $reason, (int) ($user['id'] ?? 0));
            $_SESSION['subject_exemption_success'] = 'Subject exemptions saved successfully for ' . $saved . ' student(s).';
        } catch (\Throwable $exception) {
            error_log('[Subject exemption save failed] ' . $exception->getMessage());
            $_SESSION['subject_exemption_errors'] = ['Unable to save subject exemptions right now. Please check that the exemption table exists and try again.'];
        }

        $this->redirect('/classes/subject-exemptions?' . http_build_query($filters));
    }

PHP_CODE;

$repoMethods = <<<'PHP_CODE'

    /**
     * @return array<int, array<string, mixed>>
     */
    public function studentSubjectExemptions(array $filters = []): array
    {
        if (!$this->tableHasColumn('student_subject_exemptions', 'student_id')) {
            return [];
        }

        $where = [];
        $params = [];
        foreach (['class_name', 'section', 'school_term', 'school_session'] as $field) {
            $value = trim((string) ($filters[$field] ?? ''));
            if ($value !== '') {
                $where[] = 'sse.' . $field . ' = ?';
                $params[] = $value;
            }
        }
        $subjectId = (int) ($filters['subject_id'] ?? 0);
        if ($subjectId > 0) {
            $where[] = 'sse.subject_id = ?';
            $params[] = $subjectId;
        }
        $studentId = (int) ($filters['student_id'] ?? 0);
        if ($studentId > 0) {
            $where[] = 'sse.student_id = ?';
            $params[] = $studentId;
        }

        $sql = "SELECT sse.*, a.applicant, a.first_name, a.middle_name, a.last_name, a.registration_no, sub.subject_name, sub.subject_code
            FROM student_subject_exemptions sse
            LEFT JOIN admissions a ON a.id = sse.student_id
            LEFT JOIN subjects sub ON sub.id = sse.subject_id";
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY a.applicant ASC, a.first_name ASC, a.last_name ASC, sub.subject_name ASC';

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($params);
            return $statement->fetchAll();
        } catch (\Throwable $exception) {
            error_log('[Subject exemptions load failed] ' . $exception->getMessage());
            return [];
        }
    }

    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function studentSubjectExemptionMap(string $className, string $section, string $schoolTerm, string $schoolSession): array
    {
        $map = [];
        foreach ($this->studentSubjectExemptions([
            'class_name' => $className,
            'section' => $section,
            'school_term' => $schoolTerm,
            'school_session' => $schoolSession,
        ]) as $row) {
            $studentId = (int) ($row['student_id'] ?? 0);
            $subjectId = (int) ($row['subject_id'] ?? 0);
            if ($studentId <= 0 || $subjectId <= 0) {
                continue;
            }
            $map[$studentId][$subjectId] = $row;
        }
        return $map;
    }

    public function isStudentSubjectExempted(int $studentId, string $className, string $section, int $subjectId, string $schoolTerm, string $schoolSession): bool
    {
        if ($studentId <= 0 || $subjectId <= 0 || !$this->tableHasColumn('student_subject_exemptions', 'student_id')) {
            return false;
        }
        try {
            $statement = $this->pdo->prepare(
                'SELECT 1 FROM student_subject_exemptions
                WHERE student_id = ? AND class_name = ? AND section = ? AND subject_id = ? AND school_term = ? AND school_session = ?
                LIMIT 1'
            );
            $statement->execute([$studentId, $className, $section, $subjectId, $schoolTerm, $schoolSession]);
            return (bool) $statement->fetchColumn();
        } catch (\Throwable $exception) {
            error_log('[Subject exemption check failed] ' . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param array<int, int> $studentIds
     */
    public function syncStudentSubjectExemptions(array $filters, array $studentIds, string $reason, int $createdBy = 0): int
    {
        if (!$this->tableHasColumn('student_subject_exemptions', 'student_id')) {
            throw new \RuntimeException('Subject exemption table is not available. Run the subject exemption installer as superadmin.');
        }

        $className = trim((string) ($filters['class_name'] ?? ''));
        $section = trim((string) ($filters['section'] ?? ''));
        $subjectId = (int) ($filters['subject_id'] ?? 0);
        $schoolTerm = trim((string) ($filters['school_term'] ?? ''));
        $schoolSession = trim((string) ($filters['school_session'] ?? ''));
        if ($className === '' || $subjectId <= 0 || $schoolTerm === '' || $schoolSession === '') {
            throw new \InvalidArgumentException('Class, subject, term, and session are required.');
        }

        $studentFilters = ['class_name' => $className];
        if ($section !== '') {
            $studentFilters['section'] = $section;
        }

        $validStudents = [];
        foreach ($this->admittedStudents($studentFilters) as $student) {
            $id = (int) ($student['id'] ?? 0);
            if ($id > 0) {
                $validStudents[$id] = true;
            }
        }
        $studentIds = array_values(array_filter(array_unique(array_map('intval', $studentIds)), static fn (int $id): bool => isset($validStudents[$id])));

        $now = date('Y-m-d H:i:s');
        $ownsTransaction = !$this->pdo->inTransaction();
        if ($ownsTransaction) {
            $this->pdo->beginTransaction();
        }
        try {
            $delete = $this->pdo->prepare(
                'DELETE FROM student_subject_exemptions
                WHERE class_name = ? AND section = ? AND subject_id = ? AND school_term = ? AND school_session = ?'
            );
            $delete->execute([$className, $section, $subjectId, $schoolTerm, $schoolSession]);

            $insert = $this->pdo->prepare(
                'INSERT INTO student_subject_exemptions
                (student_id, class_name, section, subject_id, school_term, school_session, reason, created_by, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            foreach ($studentIds as $studentId) {
                $insert->execute([$studentId, $className, $section, $subjectId, $schoolTerm, $schoolSession, $reason, $createdBy, $now, $now]);
            }
            if ($ownsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->commit();
            }
        } catch (\Throwable $exception) {
            if ($ownsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }

        return count($studentIds);
    }

PHP_CODE;

patch_file($appPath, function (string $code) use ($subjectExemptionsMethod): string {
    if (!str_contains($code, "'/classes/subject-exemptions'")) {
        $needle = "            '/classes/subject-assign' => \$this->subjectAssign(),";
        if (str_contains($code, $needle)) {
            $code = str_replace($needle, $needle . "\n            '/classes/subject-exemptions' => \$this->subjectExemptions(),", $code);
        } else {
            $code = preg_replace('/match \(\$path\) \{/', "match (\$path) {\n            '/classes/subject-exemptions' => \$this->subjectExemptions(),", $code, 1) ?? $code;
        }
    }
    if (!str_contains($code, "'/classes/subject-exemptions/save'")) {
        $needle = "            '/classes/subject-assign/save' => \$this->saveSubjectAssignment(),";
        if (str_contains($code, $needle)) {
            $code = str_replace($needle, $needle . "\n            '/classes/subject-exemptions/save' => \$this->saveSubjectExemptions(),", $code);
        } else {
            $code = preg_replace('/match \(\$path\) \{/', "match (\$path) {\n            '/classes/subject-exemptions/save' => \$this->saveSubjectExemptions(),", $code, 2) ?? $code;
        }
    }

    if (preg_match('/\n    private function subjectExemptions\(\): void\n    \{.*?\n    private function saveSubjectAssignment\(\): void/s', $code)) {
        $code = preg_replace('/\n    private function subjectExemptions\(\): void\n    \{.*?\n    private function saveSubjectAssignment\(\): void/s', "\n" . rtrim($subjectExemptionsMethod) . "\n    private function saveSubjectAssignment(): void", $code, 1) ?? $code;
    } elseif (str_contains($code, 'private function saveSubjectAssignment(): void')) {
        $code = str_replace('    private function saveSubjectAssignment(): void', rtrim($subjectExemptionsMethod) . "\n    private function saveSubjectAssignment(): void", $code);
    }
    return $code;
});

patch_file($repoPath, function (string $code) use ($repoMethods): string {
    if (preg_match('/\n    \/\*\*\n     \* @return array<int, array<string, mixed>>\n     \*\/\n    public function studentSubjectExemptions\(array \$filters = \[\]\): array.*?\n    public function subjectsForClass\(string \$className = \'\', string \$section = \'\'\): array/s', $code)) {
        $code = preg_replace('/\n    \/\*\*\n     \* @return array<int, array<string, mixed>>\n     \*\/\n    public function studentSubjectExemptions\(array \$filters = \[\]\): array.*?\n    public function subjectsForClass\(string \$className = \'\', string \$section = \'\'\): array/s', $repoMethods . "\n    public function subjectsForClass(string \$className = '', string \$section = ''): array", $code, 1) ?? $code;
    } elseif (str_contains($code, "public function subjectsForClass(string \$className = '', string \$section = ''): array")) {
        $code = str_replace("    public function subjectsForClass(string \$className = '', string \$section = ''): array", $repoMethods . "\n    public function subjectsForClass(string \$className = '', string \$section = ''): array", $code);
    }

    if (!str_contains($code, 'function normalizeSubjectDuplicateKey')) {
        $helper = <<<'PHP_CODE'

    private function normalizeSubjectDuplicateKey(string $subjectName): string
    {
        $key = strtolower(trim($subjectName));
        $key = preg_replace('/[^a-z0-9]+/', '', $key) ?? $key;
        return $key;
    }

PHP_CODE;
        $code = str_replace("    public function subjectsForClass(string \$className = '', string \$section = ''): array", $helper . "    public function subjectsForClass(string \$className = '', string \$section = ''): array", $code);
    }

    // Make subjectsForClass display unique subject names in case duplicate subject IDs exist for the same name.
    $old = <<<'OLD_CODE'
        foreach ($rows as $row) {
            $subjectId = (int) ($row['subject_id'] ?? 0);
            if ($subjectId <= 0 || isset($uniqueRows[$subjectId])) {
                continue;
            }

            $uniqueRows[$subjectId] = $row;
        }
OLD_CODE;
    $new = <<<'NEW_CODE'
        foreach ($rows as $row) {
            $subjectId = (int) ($row['subject_id'] ?? 0);
            $subjectName = (string) ($row['subject_name'] ?? '');
            $subjectKey = method_exists($this, 'normalizeSubjectDuplicateKey') ? $this->normalizeSubjectDuplicateKey($subjectName) : strtolower(preg_replace('/[^a-z0-9]+/', '', $subjectName));
            if ($subjectKey === '') {
                $subjectKey = 'id:' . $subjectId;
            }
            if ($subjectId <= 0 || isset($uniqueRows[$subjectKey])) {
                continue;
            }

            $uniqueRows[$subjectKey] = $row;
        }
NEW_CODE;
    if (str_contains($code, $old)) {
        $code = str_replace($old, $new, $code);
    }
    return $code;
});

patch_file($modulesPath, function (string $code): string {
    if (!str_contains($code, "Subject Exemptions")) {
        $needle = "['title' => 'Assign Subjects to Class', 'path' => '/classes/subject-assign', 'permission_key' => 'subject_assignment'],";
        if (str_contains($code, $needle)) {
            $code = str_replace($needle, $needle . "\n                    ['title' => 'Subject Exemptions', 'path' => '/classes/subject-exemptions', 'permission_key' => 'subject_assignment'],", $code);
        }
    }
    return $code;
});

patch_file($navPath, function (string $code): string {
    if (!str_contains($code, '/classes/subject-exemptions')) {
        $insert = "        <a class=\"<?= is_active('/classes/subject-exemptions') ?>\" href=\"/classes/subject-exemptions\">Subject Exemptions</a>\n";
        $line = "        <a class=\"<?= is_active('/classes/subject-assign') ?>\" href=\"/classes/subject-assign\">Assign Subjects to Class</a>";
        if (str_contains($code, $line)) {
            $code = str_replace($line, $line . "\n" . rtrim($insert), $code);
        } else {
            $code .= "\n" . $insert;
        }
    }
    return $code;
});

echo "Subject exemption page code patch completed.\n";
