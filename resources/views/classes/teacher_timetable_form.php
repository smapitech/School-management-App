<?php
    $jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
    $selectedClassId = (int) ($formValues['class_id'] ?? 0);
    $selectedSection = (string) ($formValues['section'] ?? '');
    $selectedSubjectId = (int) ($formValues['subject_id'] ?? 0);
?>

<section class="module-hero">
    <div>
        <p class="eyebrow">Teacher access</p>
        <h2><?= !empty($isEdit) ? 'Edit Timetable' : 'Create Timetable' ?></h2>
        <p>Build timetable entries only for your assigned classes and subjects.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/teacher/classes/timetable">Class Timetable</a>
        <a class="primary-action" href="/teacher/classes">My Assigned Classes</a>
    </div>
</section>

<?php require __DIR__ . '/nav.php'; ?>

<?php if (!empty($workspaceWarning)): ?>
    <section class="alert-error"><p><?= e($workspaceWarning) ?></p></section>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <section class="alert-error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></section>
<?php endif; ?>

<?php if (empty($classOptions)): ?>
    <section class="alert-error"><p>No assigned classes are available for timetable creation yet.</p></section>
<?php else: ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow"><?= !empty($isEdit) ? 'Update row' : 'New row' ?></p>
                <h3><?= !empty($isEdit) ? 'Update Class Timetable Row' : 'Create Class Timetable Row' ?></h3>
            </div>
        </div>

        <form class="payroll-form" method="post" action="/teacher/classes/timetable/save">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= e($formValues['id'] ?? '') ?>">

            <label>
                <span>Class</span>
                <select name="class_id" id="teacher-timetable-class" required>
                    <option value="">Select assigned class</option>
                    <?php foreach ($classOptions as $class): ?>
                        <option value="<?= e($class['class_id']) ?>" <?= $selectedClassId === (int) $class['class_id'] ? 'selected' : '' ?>>
                            <?= e($class['class_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                <span>Section / Arm</span>
                <select name="section" id="teacher-timetable-section" required></select>
            </label>

            <label>
                <span>Subject</span>
                <select name="subject_id" id="teacher-timetable-subject" required></select>
            </label>

            <label>
                <span>Day of Week</span>
                <select name="day_of_week" required>
                    <option value="">Select day</option>
                    <?php foreach ($days as $day): ?>
                        <option value="<?= e($day) ?>" <?= ($formValues['day_of_week'] ?? '') === $day ? 'selected' : '' ?>><?= e($day) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                <span>Start Time</span>
                <input type="time" name="start_time" value="<?= e($formValues['start_time'] ?? '') ?>" required>
            </label>

            <label>
                <span>End Time</span>
                <input type="time" name="end_time" value="<?= e($formValues['end_time'] ?? '') ?>" required>
            </label>

            <label>
                <span>Room</span>
                <input name="room" value="<?= e($formValues['room'] ?? '') ?>" placeholder="Block B-104">
            </label>

            <label class="form-wide">
                <span>Note</span>
                <textarea name="note" rows="4" placeholder="Optional timetable note"><?= e($formValues['note'] ?? '') ?></textarea>
            </label>

            <button type="submit"><?= !empty($isEdit) ? 'Save Timetable Changes' : 'Create Timetable' ?></button>
        </form>
    </section>

    <script>
        (() => {
            const classField = document.getElementById('teacher-timetable-class');
            const sectionField = document.getElementById('teacher-timetable-section');
            const subjectField = document.getElementById('teacher-timetable-subject');
            const sectionOptionsByClass = <?= json_encode($sectionOptionsByClass, $jsonFlags) ?>;
            const subjectOptionsByClassSection = <?= json_encode($subjectOptionsByClassSection, $jsonFlags) ?>;
            let selectedSection = <?= json_encode($selectedSection, $jsonFlags) ?>;
            let selectedSubjectId = <?= json_encode((string) $selectedSubjectId, $jsonFlags) ?>;

            const fillSections = () => {
                const classId = classField.value;
                const options = sectionOptionsByClass[classId] || [];
                const fallbackSection = selectedSection;
                sectionField.innerHTML = '';

                if (!options.length) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No assigned section';
                    sectionField.appendChild(option);
                    return;
                }

                if (!(options.length === 1 && options[0] === '')) {
                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = 'Select section';
                    sectionField.appendChild(placeholder);
                }

                options.forEach((section) => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section === '' ? 'General' : section;
                    if (section === fallbackSection) {
                        option.selected = true;
                    }
                    sectionField.appendChild(option);
                });

                if (!options.includes(sectionField.value)) {
                    sectionField.value = options[0] || '';
                }
            };

            const fillSubjects = () => {
                const key = `${classField.value}|${sectionField.value}`;
                const options = subjectOptionsByClassSection[key] || [];
                const fallbackSubjectId = selectedSubjectId;
                subjectField.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = options.length ? 'Select subject' : 'No assigned subject';
                subjectField.appendChild(placeholder);

                options.forEach((subject) => {
                    const option = document.createElement('option');
                    option.value = String(subject.subject_id);
                    option.textContent = subject.subject_code ? `${subject.subject_name} (${subject.subject_code})` : subject.subject_name;
                    if (option.value === fallbackSubjectId) {
                        option.selected = true;
                    }
                    subjectField.appendChild(option);
                });
            };

            classField.addEventListener('change', () => {
                selectedSection = '';
                selectedSubjectId = '';
                fillSections();
                fillSubjects();
            });

            sectionField.addEventListener('change', () => {
                selectedSubjectId = '';
                fillSubjects();
            });

            fillSections();
            fillSubjects();
        })();
    </script>
<?php endif; ?>
