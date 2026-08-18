<?php $currentStaffPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; ?>
<section class="role-tabs">
    <?php if (can('payroll', 'view_own_payslips')): ?><a class="<?= $currentStaffPath === '/teacher/payroll' ? 'is-active' : '' ?>" href="/teacher/payroll">Payroll Overview</a><?php endif; ?>
    <?php if (can('payroll', 'view_own_payslips')): ?><a class="<?= str_starts_with($currentStaffPath, '/teacher/payslips') ? 'is-active' : '' ?>" href="/teacher/payslips">My Payslips</a><?php endif; ?>
    <?php if (can('payroll', 'view_own_allowances')): ?><a class="<?= $currentStaffPath === '/teacher/allowances' ? 'is-active' : '' ?>" href="/teacher/allowances">Allowances</a><?php endif; ?>
    <?php if (can('payroll', 'view_own_deductions')): ?><a class="<?= $currentStaffPath === '/teacher/deductions' ? 'is-active' : '' ?>" href="/teacher/deductions">Deductions</a><?php endif; ?>
    <?php if (can('payroll', 'view_own_increments')): ?><a class="<?= $currentStaffPath === '/teacher/increments' ? 'is-active' : '' ?>" href="/teacher/increments">Salary Increments</a><?php endif; ?>
</section>
