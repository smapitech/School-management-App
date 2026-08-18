<?php include __DIR__ . '/nav.php'; ?>
<?php $printQuery = http_build_query(array_filter($filters, fn ($value) => $value !== '')); ?>

<section class="module-hero">
    <div>
        <p class="eyebrow">School fee payment</p>
        <h2>Student Accounting</h2>
        <p>Create school fee invoices by class, school term, and school session.</p>
    </div>
    <a class="secondary-action" href="/accounting/student/print<?= $printQuery ? '?' . e($printQuery) : '' ?>">Printable View</a>
</section>

<section class="mini-stat-grid">
    <?php foreach ($summary as $label => $value): ?>
        <article>
            <span><?= e($label) ?></span>
            <strong><?= money($value) ?></strong>
        </article>
    <?php endforeach; ?>
</section>

<?php if (can('accounting', 'create')): ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Class fee setup</p>
                <h3>Create School Fee By Class</h3>
            </div>
        </div>
        <form class="payroll-form accounting-form" method="post" action="/accounting/student/create">
            <label>
                <span>Invoice No</span>
                <input name="invoice_no" value="INV-<?= e(date('Y')) ?>-" required>
            </label>
            <label>
                <span>Class</span>
                <select name="class_name" required>
                    <option value="">Select class</option>
                    <?php foreach ($classOptions as $class): ?>
                        <option value="<?= e($class) ?>"><?= e($class) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>School Term</span>
                <select name="school_term" required>
                    <?php foreach ($schoolTerms as $term): ?>
                        <option value="<?= e($term) ?>"><?= e($term) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>School Session</span>
                <select name="school_session" required>
                    <?php foreach ($schoolSessions as $session): ?>
                        <option value="<?= e($session) ?>"><?= e($session) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>Amount</span>
                <input type="number" min="0" step="0.01" name="amount" required>
            </label>
            <label>
                <span>Status</span>
                <select name="status">
                    <option>Paid</option>
                    <option>Partial</option>
                    <option>Unpaid</option>
                </select>
            </label>
            <button type="submit">Save Class Fee</button>
        </form>
    </section>
<?php endif; ?>

<?php if (can('accounting', 'create')): ?>
    <section class="panel">
        <div class="panel-header">
            <div>
                <p class="eyebrow">Communication bridge</p>
                <h3>Fee Reminder</h3>
            </div>
            <a class="secondary-action" href="/communication">Open Communication</a>
        </div>
        <form class="payroll-form accounting-form" method="post" action="/accounting/student/reminder">
            <label>
                <span>Class</span>
                <select name="class_name" required>
                    <option value="">Select class</option>
                    <?php foreach ($classOptions as $class): ?>
                        <option value="<?= e($class) ?>"><?= e($class) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>School Term</span>
                <select name="school_term" required>
                    <?php foreach ($schoolTerms as $term): ?>
                        <option value="<?= e($term) ?>"><?= e($term) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>School Session</span>
                <select name="school_session" required>
                    <?php foreach ($schoolSessions as $session): ?>
                        <option value="<?= e($session) ?>"><?= e($session) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span>Due Date</span>
                <input type="date" name="due_date">
            </label>
            <label>
                <span>Audience</span>
                <select name="audience">
                    <option>Parents</option>
                    <option>Students</option>
                    <option>Parents and Students</option>
                </select>
            </label>
            <label class="form-wide">
                <span>Reminder Message</span>
                <input name="message" value="Kindly complete the school fee payment for the selected class, term, and session." required>
            </label>
            <button type="submit">Save Reminder Draft</button>
        </form>
        <?php if (!empty($reminders)): ?>
            <div class="table-wrap reminder-table">
                <table>
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Session</th>
                            <th>Due Date</th>
                            <th>Audience</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reminders as $reminder): ?>
                            <tr>
                                <td><?= e($reminder['class_name']) ?></td>
                                <td><?= e($reminder['school_term']) ?></td>
                                <td><?= e($reminder['school_session']) ?></td>
                                <td><?= e($reminder['due_date']) ?></td>
                                <td><?= e($reminder['audience']) ?></td>
                                <td><span class="status"><?= e($reminder['communication_status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="panel-header">
        <div>
            <p class="eyebrow">Filter</p>
            <h3>Student Fee Records</h3>
        </div>
    </div>
    <form class="student-search-form" method="get" action="/accounting/student">
        <label>
            <span>Class</span>
            <select name="class_name">
                <option value="">All classes</option>
                <?php foreach ($classOptions as $class): ?>
                    <option value="<?= e($class) ?>" <?= $filters['class_name'] === $class ? 'selected' : '' ?>><?= e($class) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>School Term</span>
            <select name="school_term">
                <option value="">All terms</option>
                <?php foreach ($schoolTerms as $term): ?>
                    <option value="<?= e($term) ?>" <?= $filters['school_term'] === $term ? 'selected' : '' ?>><?= e($term) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>School Session</span>
            <select name="school_session">
                <option value="">All sessions</option>
                <?php foreach ($schoolSessions as $session): ?>
                    <option value="<?= e($session) ?>" <?= $filters['school_session'] === $session ? 'selected' : '' ?>><?= e($session) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Status</span>
            <select name="status">
                <option value="">All status</option>
                <?php foreach (['Paid', 'Partial', 'Unpaid'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Search</button>
        <a class="secondary-action" href="/accounting/student">Reset</a>
    </form>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Class</th>
                    <th>School Term</th>
                    <th>School Session</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fees as $row): ?>
                    <tr>
                        <td><?= e($row['invoice_no']) ?></td>
                        <td><?= e($row['class_name'] ?: $row['student_name']) ?></td>
                        <td><?= e($row['school_term']) ?></td>
                        <td><?= e($row['school_session']) ?></td>
                        <td><?= money($row['amount']) ?></td>
                        <td><span class="status"><?= e($row['status']) ?></span></td>
                        <td><?= e($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="7">No class fee record matches this filter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
