<section class="module-hero">
    <div>
        <p class="eyebrow">Staff profile</p>
        <h2><?= $staff ? 'Edit Staff' : 'Add Staff' ?></h2>
        <p>Create staff records with profile, login details, and salary account information.</p>
    </div>
    <div class="action-dropdown">
        <button type="button">Staff Actions</button>
        <div>
            <a href="/staff">Staff List</a>
            <a href="/staff/designations">Designation</a>
            <a href="/staff/accounts">Account Management</a>
        </div>
    </div>
</section>

<form class="admission-form" method="post" action="/staff/save" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= e($staff['id'] ?? '') ?>">
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Employment</p><h3>Staff Details</h3></div></div>
        <div class="form-grid">
            <label><span>Staff ID</span><input name="employee_no" value="<?= e($staff['employee_no'] ?? $nextStaffId) ?>" readonly></label>
            <label>
                <span>Role</span>
                <select name="role" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= e($role) ?>" <?= (($staff['role'] ?? '') === $role || (($staff['role'] ?? '') === 'Asst Teacher' && $role === 'Assistant Teacher') || (($staff['role'] ?? '') === 'Accountant' && $role === 'Account')) ? 'selected' : '' ?>><?= e($role) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label><span>Joining Date</span><input type="date" name="joining_date" value="<?= e($staff['joining_date'] ?? date('Y-m-d')) ?>"></label>
            <label>
                <span>Designation</span>
                <select name="designation" required>
                    <?php foreach ($designations as $designation): ?>
                        <option value="<?= e($designation['name']) ?>" <?= (($staff['designation'] ?? '') === $designation['name']) ? 'selected' : '' ?>><?= e($designation['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label><span>Qualification</span><input name="qualification" value="<?= e($staff['qualification'] ?? '') ?>"></label>
            <label class="form-wide"><span>Experience Details</span><input name="experience_details" value="<?= e($staff['experience_details'] ?? '') ?>"></label>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Employee</p><h3>Personal Details</h3></div></div>
        <div class="form-grid">
            <label><span>Name</span><input name="name" value="<?= e($staff['name'] ?? '') ?>" required></label>
            <label><span>Middle Name</span><input name="middle_name" value="<?= e($staff['middle_name'] ?? '') ?>"></label>
            <label><span>Surname</span><input name="surname" value="<?= e($staff['surname'] ?? '') ?>"></label>
            <label>
                <span>Gender</span>
                <select name="gender">
                    <option value="Male" <?= (($staff['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= (($staff['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                </select>
            </label>
            <label><span>Religion</span><input name="religion" value="<?= e($staff['religion'] ?? '') ?>"></label>
            <label><span>DOB</span><input type="date" name="date_of_birth" value="<?= e($staff['date_of_birth'] ?? '') ?>"></label>
            <label><span>Mobile No</span><input name="mobile_no" value="<?= e($staff['mobile_no'] ?? '') ?>"></label>
            <label><span>Email</span><input type="email" name="email" value="<?= e($staff['email'] ?? '') ?>"></label>
            <label class="form-wide"><span>Address</span><input name="address" value="<?= e($staff['address'] ?? '') ?>"></label>
            <label class="form-wide"><span>Profile Picture</span><input type="file" name="staff_photo" accept=".jpg,.jpeg,.png,.gif,.webp"></label>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Login</p><h3>Login Details</h3></div></div>
        <div class="form-grid">
            <label><span>Username</span><input name="username" value="<?= e($staff['username'] ?? '') ?>" <?= $staff ? '' : 'required' ?>></label>
            <label><span>Password</span><input type="password" name="password" <?= $staff ? '' : 'required' ?>></label>
            <label><span>Retype Password</span><input type="password" name="password_confirmation" <?= $staff ? '' : 'required' ?>></label>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Salary Account</p><h3>Bank Information</h3></div></div>
        <div class="form-grid">
            <label><span>Bank Name</span><input name="bank_name" value="<?= e($staff['bank_name'] ?? '') ?>"></label>
            <label><span>Account Name</span><input name="account_name" value="<?= e($staff['account_name'] ?? '') ?>"></label>
            <label><span>Account Number</span><input name="account_number" value="<?= e($staff['account_number'] ?? '') ?>"></label>
        </div>
    </section>

    <div class="form-actions">
        <a class="secondary-action" href="/staff">Cancel</a>
        <button type="submit">Save Staff</button>
    </div>
</form>
