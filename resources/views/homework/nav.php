<nav class="subnav">
    <a class="<?= is_active('/homework') ?>" href="/homework">Overview</a>
    <?php if (can('homework', 'create')): ?><a class="<?= is_active('/homework/create') ?>" href="/homework/create">Create Homework</a><?php endif; ?>
    <a class="<?= is_active('/homework/submissions') ?>" href="/homework/submissions">Submissions</a>
    <a class="<?= is_active('/homework/reports') ?>" href="/homework/reports">Reports</a>
</nav>
