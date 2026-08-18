<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2>My Assigned Subjects</h2>
        <p>View only the subjects connected to the classes and sections you are allowed to teach.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/classes">My Assigned Classes</a>
        <a class="primary-action" href="/teacher/classes/students">My Students</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Limit the list to one assigned class</h3>
        </div>
    </div>

    <form class="student-search-form" method="get" action="/teacher/classes/subjects">
        <label>
            <span>Assigned Class</span>
            <select name="class_id">
                <option value="0">All Assigned Classes</option>
                <?php foreach ($classes as $class): ?>
                    <?php $label = trim($class['class_name'] . ' ' . ($class['section'] ?? '')); ?>
                    <option value="<?= e($class['class_id']) ?>" <?= (int) ($selectedClassId ?? 0) === (int) $class['class_id'] ? 'selected' : '' ?>>
                        <?= e($label !== '' ? $label : $class['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Filter</button>
        <a class="secondary-action" href="/teacher/classes/subjects">Reset</a>
    </form>
</section>

<section class="stat-grid">
    <article class="stat-card"><span>assigned classes</span><strong><?= e(count($classes)) ?></strong><p>teacher-visible class sections</p></article>
    <article class="stat-card"><span>assigned subjects</span><strong><?= e(count($subjects)) ?></strong><p>subject records after filtering</p></article>
    <article class="stat-card"><span>class teacher access</span><strong><?= e(count(array_filter($subjects, static fn (array $subject): bool => (int) ($subject['is_class_teacher'] ?? 0) === 1))) ?></strong><p>subjects inherited from class teacher roles</p></article>
    <article class="stat-card"><span>scope</span><strong><?= e((int) ($selectedClassId ?? 0) > 0 ? 'Filtered' : 'All') ?></strong><p>current subject view</p></article>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Subjects</p>
            <h3>Assigned Subject List</h3>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Subject</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Access</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?= e($subject['class_name']) ?></td>
                        <td><?= e($subject['section'] ?: 'All Sections') ?></td>
                        <td><strong><?= e($subject['subject_name']) ?></strong></td>
                        <td><?= e($subject['subject_code']) ?></td>
                        <td><?= e($subject['subject_type']) ?></td>
                        <td><?= e(!empty($subject['is_class_teacher']) ? 'Class Teacher Access' : 'Direct Subject Assignment') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($subjects)): ?>
                    <tr>
                        <td colspan="6">No assigned subjects were found for this teacher filter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
