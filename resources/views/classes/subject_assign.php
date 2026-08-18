<?php
    $selectedClassName = trim((string) ($selectedClassName ?? ''));
    $subjects = $subjects ?? [];
    $currentAssignments = $currentAssignments ?? [];
    $selectedSubjectIds = array_values(array_unique(array_filter(array_map(
        'intval',
        (array) ($selectedSubjectIds ?? [])
    ), static fn (int $subjectId): bool => $subjectId > 0)));
    $selectedSubjectLookup = array_fill_keys($selectedSubjectIds, true);
    $currentAssignmentCount = count($currentAssignments);
    $subjectCount = count($subjects);
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Subjects</p>
        <h2>Assign Subjects to Class</h2>
        <p>Select a class and choose all subjects offered by that class.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/classes/subjects">Subject Form</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($success)): ?>
    <section class="alert-success" role="status">
        <p><?= e($success) ?></p>
    </section>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <section class="alert-error" role="alert">
        <?php foreach ($errors as $error): ?>
            <p><?= e($error) ?></p>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Class</p>
            <h3>Load a class to edit its subject list</h3>
        </div>
    </div>

    <form class="subject-load-form" method="get" action="/classes/subject-assign">
        <label>
            <span>Class</span>
            <select name="class_name" id="subject-class-select">
                <option value="">Select a class</option>
                <?php foreach ($classOptions as $class): ?>
                    <option value="<?= e($class) ?>" <?= $selectedClassName === $class ? 'selected' : '' ?>>
                        <?= e($class) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="primary-action">Load Subjects</button>
    </form>
</section>

<?php if ($selectedClassName === ''): ?>
    <section class="panel empty-state">
        <h3>Select a class to begin</h3>
        <p>Choose a class above, then we will load every created subject for bulk assignment.</p>
    </section>
<?php elseif (empty($subjects)): ?>
    <section class="panel empty-state">
        <h3>No subjects available yet</h3>
        <p>Create subjects first so they can be assigned to classes.</p>
        <a class="primary-action" href="/classes/subjects">Create Subject</a>
    </section>
<?php else: ?>
    <section class="stat-grid">
        <article class="stat-card">
            <span>selected class</span>
            <strong><?= e($selectedClassName) ?></strong>
            <p>subject assignments will be saved for this class</p>
        </article>
        <article class="stat-card">
            <span>currently assigned</span>
            <strong data-selected-count><?= e($currentAssignmentCount) ?></strong>
            <p>subjects already linked to this class</p>
        </article>
        <article class="stat-card">
            <span>available subjects</span>
            <strong><?= e($subjectCount) ?></strong>
            <p>created subjects ready for assignment</p>
        </article>
        <article class="stat-card">
            <span>scope</span>
            <strong>Class-wide</strong>
            <p>bulk updates apply to the selected class relationship</p>
        </article>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Bulk edit</p>
                <h3><?= e($selectedClassName) ?> Subject Assignment</h3>
            </div>
        </div>

        <div class="subject-toolbar">
            <label class="subject-search-field">
                <span>Search subjects</span>
                <input
                    type="search"
                    id="subject-search"
                    placeholder="Search by name, code, or type"
                    autocomplete="off"
                >
            </label>
            <div class="subject-toolbar-actions">
                <button type="button" class="secondary-action" data-select-all>Select all</button>
                <button type="button" class="secondary-action" data-clear-all>Clear all</button>
            </div>
        </div>

        <form class="subject-assign-form" method="post" action="/classes/subject-assign/save" id="subject-assign-form">
            <?= csrf_field() ?>
            <input type="hidden" name="class_name" value="<?= e($selectedClassName) ?>">

            <div class="subject-grid" data-subject-grid>
                <?php foreach ($subjects as $subject): ?>
                    <?php
                        $subjectId = (int) ($subject['id'] ?? 0);
                        $subjectName = trim((string) ($subject['subject_name'] ?? ''));
                        $subjectCode = trim((string) ($subject['subject_code'] ?? ''));
                        $subjectType = trim((string) ($subject['subject_type'] ?? ''));
                        $searchValue = strtolower(trim($subjectName . ' ' . $subjectCode . ' ' . $subjectType));
                    ?>
                    <label class="subject-card" data-subject-card data-subject-search="<?= e($searchValue) ?>">
                        <input
                            type="checkbox"
                            name="subject_ids[]"
                            value="<?= e($subjectId) ?>"
                            <?= !empty($selectedSubjectLookup[$subjectId]) ? 'checked' : '' ?>
                        >
                        <span class="subject-card-body">
                            <strong><?= e($subjectName) ?></strong>
                            <span class="subject-card-meta">
                                <?php if ($subjectCode !== ''): ?>
                                    <span class="subject-pill"><?= e($subjectCode) ?></span>
                                <?php endif; ?>
                                <?php if ($subjectType !== ''): ?>
                                    <span class="subject-pill"><?= e($subjectType) ?></span>
                                <?php endif; ?>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="subject-selection-footer">
                <p>
                    <strong data-selected-count><?= e(count($selectedSubjectIds)) ?></strong>
                    of
                    <strong><?= e($subjectCount) ?></strong>
                    subjects selected for <?= e($selectedClassName) ?>
                </p>
                <button type="submit" class="primary-action">Save Assignments</button>
            </div>
        </form>
    </section>

    <script>
        (() => {
            const classSelect = document.getElementById('subject-class-select');
            const searchInput = document.getElementById('subject-search');
            const cards = Array.from(document.querySelectorAll('[data-subject-card]'));
            const checkboxes = cards
                .map((card) => card.querySelector('input[type="checkbox"]'))
                .filter(Boolean);
            const selectedCountNodes = Array.from(document.querySelectorAll('[data-selected-count]'));

            const updateSelectedCount = () => {
                if (!selectedCountNodes.length) {
                    return;
                }

                const selectedCount = String(
                    checkboxes.filter((checkbox) => checkbox.checked).length
                );
                selectedCountNodes.forEach((node) => {
                    node.textContent = selectedCount;
                });
            };

            const applySearch = () => {
                const needle = (searchInput?.value || '').trim().toLowerCase();
                cards.forEach((card) => {
                    const haystack = String(card.dataset.subjectSearch || '');
                    card.hidden = needle !== '' && !haystack.includes(needle);
                });
            };

            classSelect?.addEventListener('change', () => {
                classSelect.form?.submit();
            });

            searchInput?.addEventListener('input', () => {
                applySearch();
            });

            document.querySelector('[data-select-all]')?.addEventListener('click', () => {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                });
                updateSelectedCount();
            });

            document.querySelector('[data-clear-all]')?.addEventListener('click', () => {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                updateSelectedCount();
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            applySearch();
            updateSelectedCount();
        })();
    </script>
<?php endif; ?>
