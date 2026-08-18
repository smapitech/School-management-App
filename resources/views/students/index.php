<section class="module-hero">
    <div>
        <p class="eyebrow">Student registry</p>
        <h2><?= !empty($isTeacher) ? 'My Students' : 'Admitted Students' ?></h2>
        <p><?= !empty($isTeacher) ? 'Students assigned to your classes and teaching responsibility.' : 'This list is generated from the Admission module. New students must be created through Create Admission.' ?></p>
    </div>
    <div class="hero-actions">
        <?php if (can('students', 'edit')): ?>
            <a class="secondary-action" href="/students/accounts">Portal Login Accounts</a>
        <?php endif; ?>
        <?php if (can('admissions', 'create')): ?>
            <a class="primary-action" href="/admissions/create">Create Admission</a>
        <?php endif; ?>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Search</p>
            <h3><?= !empty($isTeacher) ? 'Find your assigned students by class, name, or register number' : 'Find students by class, name, or register number' ?></h3>
        </div>
    </div>

    <form class="student-search-form" method="get" action="/students">
        <label>
            <span>Class</span>
            <select name="class_name">
                <option value="">All Classes</option>
                <?php foreach ($classOptions as $class): ?>
                    <option value="<?= e($class) ?>" <?= $filters['class_name'] === $class ? 'selected' : '' ?>><?= e($class) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Name</span>
            <input name="name" value="<?= e($filters['name']) ?>" placeholder="Search full name">
        </label>
        <label>
            <span>Register No</span>
            <input name="registration_no" value="<?= e($filters['registration_no']) ?>" placeholder="ADM-2026">
        </label>
        <button type="submit">Search</button>
        <a class="secondary-action" href="/students">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Registry</p>
            <h3><?= e(count($rows)) ?> <?= !empty($isTeacher) ? 'assigned student' : 'admitted student' ?><?= count($rows) === 1 ? '' : 's' ?></h3>
        </div>
    </div>

    <div class="table-wrap">
        <table class="student-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Full Name</th>
                    <th>Guardian Name</th>
                    <th>Class Section</th>
                    <th>Gender</th>
                    <th>Register No</th>
                    <th>Age</th>
                    <th>Class Admitted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php
                        $fullName = trim(($row['first_name'] ?: $row['applicant']) . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
                        $initials = strtoupper(substr($row['first_name'] ?: $row['applicant'], 0, 1) . substr($row['last_name'] ?: $row['applicant'], 0, 1));
                        $age = 'Not set';
                        if (!empty($row['date_of_birth'])) {
                            $birthDate = new DateTime($row['date_of_birth']);
                            $age = (string) $birthDate->diff(new DateTime('today'))->y;
                        }
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['profile_picture'])): ?>
                                <img class="student-photo" src="<?= e($row['profile_picture']) ?>" alt="<?= e($fullName) ?>">
                            <?php else: ?>
                                <span class="student-photo placeholder"><?= e($initials) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($fullName) ?></strong>
                            <small><?= e($row['email']) ?></small>
                        </td>
                        <td><?= e($row['guardian_full_name'] ?: $row['guardian']) ?></td>
                        <td><?= e(trim($row['class_name'] . ' ' . $row['section'])) ?></td>
                        <td><?= e($row['gender']) ?></td>
                        <td><?= e($row['registration_no']) ?></td>
                        <td><?= e($age) ?></td>
                        <td><?= e($row['class_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="8">No admitted students match this search.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
