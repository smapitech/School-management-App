<?php require __DIR__ . '/nav.php'; ?>
<?php
    $filters = (array) ($filters ?? []);
    $selectedClass = trim((string) ($filters['class_name'] ?? ''));
    $selectedSection = trim((string) ($filters['section'] ?? ''));
    $selectedSubjectId = (int) ($filters['subject_id'] ?? 0);
    $selectedTerm = trim((string) ($filters['school_term'] ?? ''));
    $selectedSession = trim((string) ($filters['school_session'] ?? ''));

    $subjects = array_values((array) ($subjects ?? []));
    $students = array_values((array) ($students ?? []));
    $exemptions = array_values((array) ($exemptions ?? []));
    $errors = array_values((array) ($errors ?? []));

    $subjectMap = [];
    foreach ($subjects as $subject) {
        $subjectId = (int) ($subject['subject_id'] ?? ($subject['id'] ?? 0));
        if ($subjectId > 0) {
            $subjectMap[$subjectId] = $subject;
        }
    }
    $selectedSubject = $subjectMap[$selectedSubjectId] ?? null;

    $exemptedStudentIds = [];
    foreach ($exemptions as $exemption) {
        $studentId = (int) ($exemption['student_id'] ?? 0);
        if ($studentId > 0) {
            $exemptedStudentIds[$studentId] = true;
        }
    }

    $studentDisplayName = static function (array $student): string {
        $name = trim((string) (($student['first_name'] ?? '') . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['last_name'] ?? '')));
        if ($name === '') {
            $name = trim((string) ($student['applicant'] ?? ($student['name'] ?? 'Student')));
        }
        return $name !== '' ? $name : 'Student';
    };
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Student-level subjects</p>
        <h2>Subject Exemptions</h2>
        <p>Keep a subject assigned to the class, but remove it from selected students only. This is useful for CRS, Arabic, French, Yoruba, Music, or any optional subject.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/classes/subject-assign">Class Subject Assignment</a>
    </div>
</section>

<?php if (!empty($success)): ?>
    <section class="alert-success" role="status"><p><?= e((string) $success) ?></p></section>
<?php endif; ?>

<?php if ($errors): ?>
    <section class="alert-error" role="alert">
        <?php foreach ($errors as $error): ?>
            <p><?= e((string) $error) ?></p>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Select class, section, subject, term, and session</h3>
            <p class="muted">Load students first, then tick only the students who should not take the selected subject.</p>
        </div>
    </div>
    <form class="marksheet-filter-form" method="get" action="/classes/subject-exemptions">
        <label>
            <span>Class</span>
            <select name="class_name" required onchange="this.form.subject_id.value=''; this.form.submit()">
                <option value="">Select class</option>
                <?php foreach ((array) ($classOptions ?? []) as $class): ?>
                    <?php $classValue = is_array($class) ? (string) ($class['name'] ?? $class['class_name'] ?? '') : (string) $class; ?>
                    <?php if ($classValue === '') { continue; } ?>
                    <option value="<?= e($classValue) ?>" <?= $selectedClass === $classValue ? 'selected' : '' ?>><?= e($classValue) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Section</span>
            <select name="section" onchange="this.form.subject_id.value=''; this.form.submit()">
                <option value="">All / No section</option>
                <?php foreach ((array) ($sections ?? []) as $section): ?>
                    <?php $sectionValue = is_array($section) ? (string) ($section['name'] ?? $section['section'] ?? '') : (string) $section; ?>
                    <?php if ($sectionValue === '') { continue; } ?>
                    <option value="<?= e($sectionValue) ?>" <?= $selectedSection === $sectionValue ? 'selected' : '' ?>><?= e($sectionValue) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Term</span>
            <select name="school_term" required>
                <?php foreach ((array) ($schoolTerms ?? []) as $term): ?>
                    <?php $termValue = (string) $term; ?>
                    <option value="<?= e($termValue) ?>" <?= $selectedTerm === $termValue ? 'selected' : '' ?>><?= e($termValue) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Session</span>
            <select name="school_session" required>
                <?php foreach ((array) ($schoolSessions ?? []) as $session): ?>
                    <?php $sessionValue = (string) $session; ?>
                    <option value="<?= e($sessionValue) ?>" <?= $selectedSession === $sessionValue ? 'selected' : '' ?>><?= e($sessionValue) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Subject</span>
            <select name="subject_id" required>
                <option value="">Select subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <?php
                        $subjectId = (int) ($subject['subject_id'] ?? ($subject['id'] ?? 0));
                        if ($subjectId <= 0) { continue; }
                        $subjectName = trim((string) ($subject['subject_name'] ?? ($subject['name'] ?? 'Subject')));
                        $subjectCode = trim((string) ($subject['subject_code'] ?? ''));
                    ?>
                    <option value="<?= e((string) $subjectId) ?>" <?= $selectedSubjectId === $subjectId ? 'selected' : '' ?>>
                        <?= e($subjectName) ?><?= $subjectCode !== '' ? ' — ' . e($subjectCode) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="primary-action">Load Students</button>
    </form>
</section>

<?php if ($selectedClass === ''): ?>
    <section class="panel empty-state"><h3>Select a class to begin</h3><p>After loading a class, choose the subject and tick only students who should not take that subject.</p></section>
<?php elseif (!$subjects): ?>
    <section class="panel empty-state"><h3>No subjects found for this class</h3><p>Assign subjects to this class first, then return to subject exemptions.</p><p><a class="secondary-action" href="/classes/subject-assign">Open Subject Assignment</a></p></section>
<?php elseif ($selectedSubjectId <= 0): ?>
    <section class="panel empty-state"><h3>Select a subject</h3><p>Choose CRS or any optional subject from the filter above, then load students.</p></section>
<?php elseif (!$selectedSubject): ?>
    <section class="panel empty-state"><h3>Selected subject is not valid for this class</h3><p>The selected subject could not be matched to this class. Please choose the subject again from the dropdown.</p></section>
<?php else: ?>
    <section class="stat-grid">
        <article class="stat-card"><span>Class</span><strong><?= e(trim($selectedClass . ' ' . $selectedSection)) ?></strong><p>selected scope</p></article>
        <article class="stat-card"><span>Subject</span><strong><?= e((string) ($selectedSubject['subject_name'] ?? 'Selected Subject')) ?></strong><p>subject to exclude</p></article>
        <article class="stat-card"><span>Students</span><strong><?= e((string) count($students)) ?></strong><p>loaded records</p></article>
        <article class="stat-card"><span>Currently exempted</span><strong data-exemption-count><?= e((string) count($exemptedStudentIds)) ?></strong><p>saved exemptions</p></article>
    </section>

    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Exemption list</p><h3>Tick students who should not take this subject</h3><p class="muted">Exempted students will not show this subject on Exam Mark or the report sheet, and the subject will not count in their totals or average.</p></div></div>
        <form method="post" action="/classes/subject-exemptions/save" id="subject-exemption-form">
            <?= csrf_field() ?>
            <input type="hidden" name="class_name" value="<?= e($selectedClass) ?>">
            <input type="hidden" name="section" value="<?= e($selectedSection) ?>">
            <input type="hidden" name="subject_id" value="<?= e((string) $selectedSubjectId) ?>">
            <input type="hidden" name="school_term" value="<?= e($selectedTerm) ?>">
            <input type="hidden" name="school_session" value="<?= e($selectedSession) ?>">

            <div class="subject-toolbar">
                <label class="subject-search-field"><span>Search student</span><input type="search" id="student-exemption-search" placeholder="Search name or register number"></label>
                <label><span>Reason</span><input type="text" name="reason" value="Religious exemption" placeholder="Reason for exemption"></label>
                <div class="subject-toolbar-actions">
                    <button type="button" class="secondary-action" data-select-visible>Tick visible</button>
                    <button type="button" class="secondary-action" data-clear-visible>Clear visible</button>
                    <button type="submit" class="primary-action">Save Exemptions</button>
                </div>
            </div>

            <?php if (!$students): ?>
                <div class="empty-state"><h3>No students found</h3><p>No admitted students were found for the selected class and section.</p></div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th style="width:70px;">Exempt</th><th>Register No</th><th>Student Name</th><th>Class</th><th>Section</th></tr></thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <?php
                                    $student = (array) $student;
                                    $studentId = (int) ($student['id'] ?? 0);
                                    if ($studentId <= 0) { continue; }
                                    $studentName = $studentDisplayName($student);
                                    $registerNo = trim((string) ($student['registration_no'] ?? ($student['student_no'] ?? '')));
                                    $search = strtolower(trim($studentName . ' ' . $registerNo));
                                ?>
                                <tr data-student-row data-student-search="<?= e($search) ?>">
                                    <td><input type="checkbox" name="student_ids[]" value="<?= e((string) $studentId) ?>" <?= isset($exemptedStudentIds[$studentId]) ? 'checked' : '' ?>></td>
                                    <td><?= e($registerNo !== '' ? $registerNo : '-') ?></td>
                                    <td><?= e($studentName) ?></td>
                                    <td><?= e((string) ($student['class_name'] ?? $selectedClass)) ?></td>
                                    <td><?= e((string) ($student['section'] ?? $selectedSection)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-actions"><button type="submit" class="primary-action">Save Exemptions</button></div>
            <?php endif; ?>
        </form>
    </section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('student-exemption-search');
    const rows = Array.from(document.querySelectorAll('[data-student-row]'));
    const updateCount = () => {
        const target = document.querySelector('[data-exemption-count]');
        if (target) target.textContent = document.querySelectorAll('input[name="student_ids[]"]:checked').length.toString();
    };
    if (search) {
        search.addEventListener('input', () => {
            const term = search.value.trim().toLowerCase();
            rows.forEach(row => row.style.display = row.dataset.studentSearch.includes(term) ? '' : 'none');
        });
    }
    document.querySelector('[data-select-visible]')?.addEventListener('click', () => { rows.filter(r => r.style.display !== 'none').forEach(r => { const c = r.querySelector('input[type="checkbox"]'); if (c) c.checked = true; }); updateCount(); });
    document.querySelector('[data-clear-visible]')?.addEventListener('click', () => { rows.filter(r => r.style.display !== 'none').forEach(r => { const c = r.querySelector('input[type="checkbox"]'); if (c) c.checked = false; }); updateCount(); });
    document.querySelectorAll('input[name="student_ids[]"]').forEach(c => c.addEventListener('change', updateCount));
});
</script>
