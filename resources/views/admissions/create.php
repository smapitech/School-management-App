<?php $isEditing = !empty($edit); ?>

<section class="module-hero">
    <div>
        <p class="eyebrow"><?= $isEditing ? 'Edit admission' : 'Create admission' ?></p>
        <h2><?= $isEditing ? 'Edit Student Admission' : 'New Student Admission' ?></h2>
        <p>Register student details, guardian information, login credentials, and upload admission pictures.</p>
    </div>
    <div class="action-dropdown">
        <button type="button">Admission Actions</button>
        <div>
            <a href="/admissions">Admission Overview</a>
            <a href="/admissions/admitted">Admitted Students</a>
        </div>
    </div>
</section>

<?php if (!empty($errors)): ?>
    <section class="alert-error" style="margin-bottom: 1rem;">
        <?php foreach ($errors as $error): ?>
            <p><?= e($error) ?></p>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<form class="admission-form" method="post" action="/admissions/create" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e($form['id'] ?? 0) ?>">
    <?php if (!empty($returnTo)): ?>
        <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
    <?php endif; ?>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Academic information</p>
                <h3>Admission Details</h3>
            </div>
        </div>
        <div class="form-grid">
            <label>
                <span>Academic Year</span>
                <select name="academic_year" required>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= e($year) ?>" <?= ($form['academic_year'] ?? '') === $year ? 'selected' : '' ?>><?= e($year) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>School Term</span>
                <select name="school_term" required>
                    <?php foreach ($schoolTerms as $term): ?>
                        <option value="<?= e($term) ?>" <?= ($form['school_term'] ?? '') === $term ? 'selected' : '' ?>><?= e($term) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>Admission Number</span>
                <input name="registration_no" value="<?= e($form['registration_no'] ?? '') ?>" placeholder="Enter admission number" required>
            </label>
            <label>
                <span>Admission Date</span>
                <input type="date" name="admission_date" value="<?= e($form['admission_date'] ?? date('Y-m-d')) ?>" required>
            </label>
            <label>
                <span>Class</span>
                <select name="class_name" required>
                    <?php foreach ($classOptions as $class): ?>
                        <option value="<?= e($class) ?>" <?= ($form['class_name'] ?? '') === $class ? 'selected' : '' ?>><?= e($class) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>Section</span>
                <select name="section" required>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?= e($section) ?>" <?= ($form['section'] ?? '') === $section ? 'selected' : '' ?>><?= e($section) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Student information</p>
                <h3>Personal Details</h3>
            </div>
        </div>
        <div class="form-grid">
            <label><span>First Name</span><input name="first_name" value="<?= e($form['first_name'] ?? '') ?>" required></label>
            <label><span>Middle Name</span><input name="middle_name" value="<?= e($form['middle_name'] ?? '') ?>"></label>
            <label><span>Last Name</span><input name="last_name" value="<?= e($form['last_name'] ?? '') ?>" required></label>
            <label><span>Date of Birth</span><input type="date" name="date_of_birth" value="<?= e($form['date_of_birth'] ?? '') ?>" required></label>
            <label>
                <span>Gender</span>
                <select name="gender" required>
                    <option value="Male" <?= ($form['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($form['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </label>
            <label><span>Religion</span><input name="religion" value="<?= e($form['religion'] ?? '') ?>"></label>
            <label><span>Mother Tongue</span><input name="mother_tongue" value="<?= e($form['mother_tongue'] ?? '') ?>"></label>
            <label><span>Email</span><input type="email" name="email" value="<?= e($form['email'] ?? '') ?>"></label>
            <label><span>City</span><input name="city" value="<?= e($form['city'] ?? '') ?>"></label>
            <label><span>State</span><input name="state" value="<?= e($form['state'] ?? '') ?>"></label>
            <label class="form-wide"><span>Address</span><input name="address" value="<?= e($form['address'] ?? '') ?>"></label>
            <label class="form-wide">
                <span>Profile Picture Upload</span>
                <input type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.gif,.webp">
                <?php if (!empty($form['profile_picture'])): ?><small>Current file: <?= e(basename((string) $form['profile_picture'])) ?></small><?php endif; ?>
            </label>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Student login details</p>
                <h3>Portal Access</h3>
            </div>
        </div>
        <div class="form-grid">
            <label><span>Username</span><input name="username" value="<?= e($form['username'] ?? '') ?>" required></label>
            <label><span><?= $isEditing ? 'New Password' : 'Password' ?></span><input type="password" name="password" <?= $isEditing ? '' : 'required' ?>></label>
            <label><span>Retype Password</span><input type="password" name="password_confirmation" <?= $isEditing ? '' : 'required' ?>></label>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Guardian details</p>
                <h3>Parent / Guardian Information</h3>
            </div>
        </div>
        <div class="form-grid">
            <label><span>Full Name</span><input name="guardian_full_name" value="<?= e($form['guardian_full_name'] ?? '') ?>" required></label>
            <label><span>Relationship</span><input name="guardian_relationship" value="<?= e($form['guardian_relationship'] ?? '') ?>"></label>
            <label><span>Father Name</span><input name="father_name" value="<?= e($form['father_name'] ?? '') ?>"></label>
            <label><span>Mother Name</span><input name="mother_name" value="<?= e($form['mother_name'] ?? '') ?>"></label>
            <label><span>Occupation</span><input name="guardian_occupation" value="<?= e($form['guardian_occupation'] ?? '') ?>"></label>
            <label><span>Mobile Number</span><input name="guardian_mobile" value="<?= e($form['guardian_mobile'] ?? '') ?>"></label>
            <label><span>City</span><input name="guardian_city" value="<?= e($form['guardian_city'] ?? '') ?>"></label>
            <label><span>State</span><input name="guardian_state" value="<?= e($form['guardian_state'] ?? '') ?>"></label>
            <label class="form-wide"><span>Address</span><input name="guardian_address" value="<?= e($form['guardian_address'] ?? '') ?>"></label>
            <label class="form-wide">
                <span>Guardian Picture Upload</span>
                <input type="file" name="guardian_picture" accept=".jpg,.jpeg,.png,.gif,.webp">
                <?php if (!empty($form['guardian_picture'])): ?><small>Current file: <?= e(basename((string) $form['guardian_picture'])) ?></small><?php endif; ?>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <a class="secondary-action" href="<?= e($returnTo ?: '/admissions') ?>">Cancel</a>
        <button type="submit"><?= $isEditing ? 'Update Admission' : 'Save Admission' ?></button>
    </div>
</form>
