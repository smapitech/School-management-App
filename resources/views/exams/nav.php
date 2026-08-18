<nav class="role-tabs">
    <?php if (($user['role'] ?? '') === 'teacher'): ?>
        <a class="<?= is_active('/exams/marks') ?>" href="/exams/marks">Exam Mark</a>
        <a class="<?= is_active('/exams/result-preview') ?>" href="/exams/result-preview">Result Preview</a>
    <?php else: ?>
        <a class="<?= is_active('/exams') ?>" href="/exams">Overview</a>
        <a class="<?= is_active('/exams/distribution') ?>" href="/exams/distribution">Mark Distribution</a>
        <a class="<?= is_active('/exams/marks') ?>" href="/exams/marks">Exam Mark</a>
        <a class="<?= is_active('/exams/result-preview') ?>" href="/exams/result-preview">Result Preview</a>
        <a class="<?= is_active('/exams/schedule') ?>" href="/exams/schedule">Exam Schedule</a>
        <a class="<?= is_active('/exams/marksheet-templates') ?>" href="/exams/marksheet-templates">Marksheet Template</a>
    <?php endif; ?>
</nav>
