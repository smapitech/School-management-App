<section class="module-hero">
    <div>
        <p class="eyebrow">Classes</p>
        <h2><?= !empty($isTeacher) ? 'My Classes' : 'Class Management' ?></h2>
        <p><?= !empty($isTeacher) ? 'View your assigned classes, students, subjects, and published timetables.' : 'Assign class teachers, create subjects, assign subjects, and manage class timetables.' ?></p>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="stat-grid">
    <?php if (!empty($isTeacher)): ?>
        <article class="stat-card"><span>assigned classes</span><strong><?= e(count($assignments)) ?></strong><p>classes under your responsibility</p></article>
        <article class="stat-card"><span>assigned subjects</span><strong><?= e(count($subjects)) ?></strong><p>subjects you can teach</p></article>
        <article class="stat-card"><span>students</span><strong><?= e(count($students ?? [])) ?></strong><p>students in your classes</p></article>
        <article class="stat-card"><span>timetable rows</span><strong><?= e(count($timetables)) ?></strong><p>scheduled lessons</p></article>
    <?php else: ?>
        <article class="stat-card"><span>class teachers</span><strong><?= e(count($assignments)) ?></strong><p>assigned classes</p></article>
        <article class="stat-card"><span>subjects</span><strong><?= e(count($subjects)) ?></strong><p>created subjects</p></article>
        <article class="stat-card"><span>subject assignments</span><strong><?= e(count($subjectAssignments)) ?></strong><p>class subject links</p></article>
        <article class="stat-card"><span>timetable rows</span><strong><?= e(count($timetables)) ?></strong><p>scheduled lessons</p></article>
    <?php endif; ?>
</section>
