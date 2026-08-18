<section class="module-hero">
    <div>
        <p class="eyebrow">Portal access</p>
        <h2>Student and Parent Login Accounts</h2>
        <p>Create, update, delete, and preview portal logins for admitted students and their parents.</p>
    </div>
    <div class="hero-actions">
        <a class="secondary-action" href="/students">Student List</a>
        <a class="primary-action" href="/admissions/create">Create Admission</a>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Find account</p>
            <h3>Filter by class, student name, or register number</h3>
        </div>
    </div>

    <form class="student-search-form" method="get" action="/students/accounts">
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
            <input name="name" value="<?= e($filters['name']) ?>" placeholder="Student name">
        </label>
        <label>
            <span>Register No</span>
            <input name="registration_no" value="<?= e($filters['registration_no']) ?>" placeholder="ADM-2026">
        </label>
        <button type="submit">Search</button>
        <a class="secondary-action" href="/students/accounts">Reset</a>
    </form>
</section>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Accounts</p>
            <h3><?= e(count($rows)) ?> admitted record<?= count($rows) === 1 ? '' : 's' ?></h3>
        </div>
    </div>

    <div class="table-wrap">
        <table class="student-table portal-account-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Student Login</th>
                    <th>Parent Login</th>
                    <th>Backend Preview</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php
                        $fullName = trim(($row['first_name'] ?: $row['applicant']) . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
                        $guardianName = $row['guardian_full_name'] ?: $row['guardian'];
                    ?>
                    <tr>
                        <td>
                            <strong><?= e($fullName) ?></strong>
                            <small><?= e($row['registration_no']) ?></small>
                            <small>Guardian: <?= e($guardianName) ?></small>
                        </td>
                        <td>
                            <?= e(trim($row['class_name'] . ' ' . $row['section'])) ?>
                            <small><?= e($row['school_term'] ?? '') ?></small>
                        </td>
                        <td>
                            <form class="portal-account-form" method="post" action="/students/accounts/student-update">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <label>
                                    <span>Username</span>
                                    <input name="username" value="<?= e($row['username'] ?? '') ?>" placeholder="student username">
                                </label>
                                <label>
                                    <span>New password</span>
                                    <input type="password" name="password" placeholder="Leave blank to keep">
                                </label>
                                <label>
                                    <span>Retype</span>
                                    <input type="password" name="password_confirmation" placeholder="Retype password">
                                </label>
                                <div class="inline-actions">
                                    <button type="submit">Save Student Login</button>
                                </div>
                            </form>
                            <form method="post" action="/students/accounts/student-delete" onsubmit="return confirm('Delete this student portal login?');">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <button class="danger-action" type="submit">Delete Student Login</button>
                            </form>
                        </td>
                        <td>
                            <form class="portal-account-form" method="post" action="/students/accounts/parent-update">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <label>
                                    <span>Username</span>
                                    <input name="username" value="<?= e($row['parent_username'] ?? '') ?>" placeholder="parent username or mobile">
                                </label>
                                <label>
                                    <span>New password</span>
                                    <input type="password" name="password" placeholder="Leave blank to keep">
                                </label>
                                <label>
                                    <span>Retype</span>
                                    <input type="password" name="password_confirmation" placeholder="Retype password">
                                </label>
                                <div class="inline-actions">
                                    <button type="submit">Save Parent Login</button>
                                </div>
                            </form>
                            <form method="post" action="/students/accounts/parent-delete" onsubmit="return confirm('Delete this parent portal login?');">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <button class="danger-action" type="submit">Delete Parent Login</button>
                            </form>
                        </td>
                        <td>
                            <div class="preview-links">
                                <a class="secondary-action" href="/student_portal?student_id=<?= e($row['id']) ?>">Student Portal</a>
                                <a class="secondary-action" href="/student_portal/report-card?student_id=<?= e($row['id']) ?>">Student Report</a>
                                <a class="secondary-action" href="/parent_portal?student_id=<?= e($row['id']) ?>">Parent Portal</a>
                                <a class="secondary-action" href="/parent_portal/report-card?student_id=<?= e($row['id']) ?>">Parent Report</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="5">No admitted students match this search.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
