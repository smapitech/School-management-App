<?php include __DIR__ . '/nav.php'; ?>

<?php
    $reportQuery = http_build_query([
        'class_name' => $setting['class_name'],
        'section' => $setting['section'],
        'school_term' => $setting['school_term'],
        'school_session' => $setting['school_session'],
        'student_id' => $student['id'] ?? 0,
    ]);
    $classQuery = http_build_query([
        'class_name' => $setting['class_name'],
        'section' => $setting['section'],
        'school_term' => $setting['school_term'],
        'school_session' => $setting['school_session'],
    ]);
?>

<section class="panel print-report">
    <div class="panel-header">
        <div>
            <p class="eyebrow"><?= e($setting['exam_name']) ?></p>
            <h3><?= e($student['applicant']) ?> - <?= e($setting['subject_name']) ?></h3>
        </div>
        <div class="preview-links no-print">
            <a class="secondary-action" href="/exams/result-preview/student?<?= e($reportQuery) ?>">View Full Report Sheet</a>
            <a class="secondary-action" href="/exams/result-preview?<?= e($classQuery) ?>">Preview Class Result</a>
            <button class="secondary-action" type="button" data-print>Print</button>
        </div>
    </div>
    <div class="print-meta">
        <span>Register No: <?= e($student['registration_no']) ?></span>
        <span>Class: <?= e($setting['class_name']) ?> <?= e($setting['section']) ?></span>
        <span>Term: <?= e($setting['school_term']) ?></span>
        <span>Session: <?= e($setting['school_session']) ?></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Mark Type</th><th>Mark Obtained</th><th>Mark Obtainable</th></tr></thead>
            <tbody>
                <?php $total = 0; $obtainable = 0; ?>
                <?php foreach ($distributions as $distribution): ?>
                    <?php
                        $score = (float) ($marks[(int) $distribution['id']] ?? 0);
                        $total += $score;
                        $obtainable += (float) $distribution['max_mark'];
                    ?>
                    <tr>
                        <td><?= e($distribution['name']) ?></td>
                        <td><?= e($score) ?></td>
                        <td><?= e($distribution['max_mark']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr><th>Total</th><th><?= e($total) ?></th><th><?= e($obtainable) ?></th></tr>
            </tbody>
        </table>
    </div>
</section>
