<?php

declare(strict_types=1);

$projectRoot = $argv[1] ?? dirname(__DIR__);
$projectRoot = rtrim($projectRoot, '/');

if (!is_file($projectRoot . '/app/bootstrap.php')) {
    fwrite(STDERR, "Project root is invalid. Usage: php tools/repair_duplicate_subject_assignments.php /path/to/custom_school_management\n");
    exit(1);
}

require $projectRoot . '/app/bootstrap.php';

use App\Database;

$config = require $projectRoot . '/config/database.php';
$database = new Database($config);
$pdo = $database->pdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$driver = (string) $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

$normalize = static function (string $value): string {
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '', $value) ?? '';
    return $value;
};

$now = date('Y-m-d H:i:s');
$summary = [
    'duplicate_subject_assignment_rows_removed' => 0,
    'exam_setting_duplicates_merged' => 0,
    'exam_mark_rows_moved' => 0,
    'exam_comment_rows_moved' => 0,
];

$pdo->beginTransaction();
try {
    // 1) Remove repeated class-subject assignment rows caused by duplicate subject master ids.
    // This groups by class + subject name, ignoring section, because the subject list is class-level.
    $rows = $pdo->query(
        "SELECT sa.id, sa.class_name, sa.section, sa.subject_id, sa.created_at, sub.subject_name
        FROM subject_assignments sa
        INNER JOIN subjects sub ON sub.id = sa.subject_id
        ORDER BY sa.class_name ASC, sub.subject_name ASC,
            CASE WHEN sa.section = '' THEN 0 ELSE 1 END ASC,
            sa.id ASC"
    )->fetchAll();

    $groups = [];
    foreach ($rows as $row) {
        $classKey = $normalize((string) ($row['class_name'] ?? ''));
        $subjectKey = $normalize((string) ($row['subject_name'] ?? ''));
        if ($classKey === '' || $subjectKey === '') {
            continue;
        }
        $groups[$classKey . '|' . $subjectKey][] = $row;
    }

    $deleteAssignment = $pdo->prepare('DELETE FROM subject_assignments WHERE id = ?');
    $blankSection = $pdo->prepare("UPDATE subject_assignments SET section = '' WHERE id = ?");

    foreach ($groups as $groupRows) {
        if (count($groupRows) <= 1) {
            continue;
        }

        // Keep the first row after sorting; it prefers blank/all-section assignment then lowest id.
        $keeper = array_shift($groupRows);
        if (trim((string) ($keeper['section'] ?? '')) !== '') {
            $blankSection->execute([(int) $keeper['id']]);
        }

        foreach ($groupRows as $duplicate) {
            $deleteAssignment->execute([(int) $duplicate['id']]);
            $summary['duplicate_subject_assignment_rows_removed']++;
        }
    }

    // 2) Merge duplicate exam_settings for the same class/section/term/session/subject name.
    // This prevents duplicate subjects from appearing on report/result pages even when exam_settings
    // was created against duplicate subject ids.
    $examRows = $pdo->query(
        "SELECT es.id, es.class_name, es.section, es.school_term, es.school_session, es.subject_id, sub.subject_name,
            (SELECT COUNT(*) FROM exam_marks em WHERE em.exam_setting_id = es.id) AS marks_count
        FROM exam_settings es
        INNER JOIN subjects sub ON sub.id = es.subject_id
        ORDER BY es.class_name ASC, es.section ASC, es.school_term ASC, es.school_session ASC, sub.subject_name ASC, marks_count DESC, es.id ASC"
    )->fetchAll();

    $examGroups = [];
    foreach ($examRows as $row) {
        $key = implode('|', [
            $normalize((string) ($row['class_name'] ?? '')),
            $normalize((string) ($row['section'] ?? '')),
            $normalize((string) ($row['school_term'] ?? '')),
            $normalize((string) ($row['school_session'] ?? '')),
            $normalize((string) ($row['subject_name'] ?? '')),
        ]);
        if (trim(str_replace('|', '', $key)) === '') {
            continue;
        }
        $examGroups[$key][] = $row;
    }

    $selectMarks = $pdo->prepare('SELECT student_id, distribution_id, mark, teacher_id, created_at FROM exam_marks WHERE exam_setting_id = ?');
    $existsMark = $pdo->prepare('SELECT COUNT(*) FROM exam_marks WHERE exam_setting_id = ? AND student_id = ? AND distribution_id = ?');
    $insertMark = $pdo->prepare('INSERT INTO exam_marks (exam_setting_id, student_id, distribution_id, mark, teacher_id, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    $deleteDupMarks = $pdo->prepare('DELETE FROM exam_marks WHERE exam_setting_id = ?');
    $deleteExamSetting = $pdo->prepare('DELETE FROM exam_settings WHERE id = ?');

    $selectComments = $pdo->prepare('SELECT student_id, teacher_remark, teacher_comment, created_at FROM exam_comments WHERE exam_setting_id = ?');
    $existsComment = $pdo->prepare('SELECT COUNT(*) FROM exam_comments WHERE exam_setting_id = ? AND student_id = ?');
    $insertComment = $pdo->prepare('INSERT INTO exam_comments (exam_setting_id, student_id, teacher_remark, teacher_comment, created_at) VALUES (?, ?, ?, ?, ?)');
    $deleteDupComments = $pdo->prepare('DELETE FROM exam_comments WHERE exam_setting_id = ?');

    foreach ($examGroups as $groupRows) {
        if (count($groupRows) <= 1) {
            continue;
        }

        // Already sorted by most marks, then lowest id.
        $keeper = array_shift($groupRows);
        $keeperId = (int) ($keeper['id'] ?? 0);
        if ($keeperId <= 0) {
            continue;
        }

        foreach ($groupRows as $duplicate) {
            $duplicateId = (int) ($duplicate['id'] ?? 0);
            if ($duplicateId <= 0 || $duplicateId === $keeperId) {
                continue;
            }

            $selectMarks->execute([$duplicateId]);
            foreach ($selectMarks->fetchAll() as $markRow) {
                $studentId = (int) ($markRow['student_id'] ?? 0);
                $distributionId = (int) ($markRow['distribution_id'] ?? 0);
                if ($studentId <= 0 || $distributionId <= 0) {
                    continue;
                }
                $existsMark->execute([$keeperId, $studentId, $distributionId]);
                if ((int) $existsMark->fetchColumn() === 0) {
                    $insertMark->execute([
                        $keeperId,
                        $studentId,
                        $distributionId,
                        (float) ($markRow['mark'] ?? 0),
                        (int) ($markRow['teacher_id'] ?? 0),
                        trim((string) ($markRow['created_at'] ?? '')) ?: $now,
                    ]);
                    $summary['exam_mark_rows_moved']++;
                }
            }

            $selectComments->execute([$duplicateId]);
            foreach ($selectComments->fetchAll() as $commentRow) {
                $studentId = (int) ($commentRow['student_id'] ?? 0);
                if ($studentId <= 0) {
                    continue;
                }
                $existsComment->execute([$keeperId, $studentId]);
                if ((int) $existsComment->fetchColumn() === 0) {
                    $insertComment->execute([
                        $keeperId,
                        $studentId,
                        (string) ($commentRow['teacher_remark'] ?? ''),
                        (string) ($commentRow['teacher_comment'] ?? ''),
                        trim((string) ($commentRow['created_at'] ?? '')) ?: $now,
                    ]);
                    $summary['exam_comment_rows_moved']++;
                }
            }

            $deleteDupMarks->execute([$duplicateId]);
            $deleteDupComments->execute([$duplicateId]);
            $deleteExamSetting->execute([$duplicateId]);
            $summary['exam_setting_duplicates_merged']++;
        }
    }

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, "Repair failed: " . $exception->getMessage() . "\n");
    exit(1);
}

// Verification report for the exact subjects the user reported.
$check = $pdo->query(
    "SELECT sa.class_name, sub.subject_name, COUNT(*) AS total
    FROM subject_assignments sa
    INNER JOIN subjects sub ON sub.id = sa.subject_id
    GROUP BY LOWER(REPLACE(REPLACE(REPLACE(sa.class_name, ' ', ''), '-', ''), '_', '')),
             LOWER(REPLACE(REPLACE(REPLACE(sub.subject_name, ' ', ''), '-', ''), '_', ''))
    HAVING total > 1
    ORDER BY total DESC, sa.class_name ASC, sub.subject_name ASC"
)->fetchAll();

echo "Duplicate subject assignment repair completed.\n";
echo "Driver: {$driver}\n";
foreach ($summary as $label => $value) {
    echo str_replace('_', ' ', ucfirst($label)) . ": {$value}\n";
}

if ($check) {
    echo "\nRemaining grouped duplicates detected:\n";
    foreach ($check as $row) {
        echo '- ' . ($row['class_name'] ?? '') . ' / ' . ($row['subject_name'] ?? '') . ' = ' . ($row['total'] ?? 0) . "\n";
    }
} else {
    echo "\nNo grouped duplicate subject assignments remain.\n";
}
