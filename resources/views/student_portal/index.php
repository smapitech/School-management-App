<?php
    $fullName = $student ? trim(($student['first_name'] ?: $student['applicant']) . ' ' . $student['middle_name'] . ' ' . $student['last_name']) : '';
    $initials = $student ? strtoupper(substr($student['first_name'] ?: $student['applicant'], 0, 1) . substr($student['last_name'] ?: $student['applicant'], 0, 1)) : '';
?>

    <section class="module-hero">
    <div>
        <p class="eyebrow">Student workspace</p>
        <h2>Student Portal</h2>
        <p>Check your report card, exam timetable, class timetable, attendance, fees, homework, and school messages.</p>
    </div>
    <?php if ($student): ?><a class="secondary-action" href="/student_portal/report-card">Print Report Card</a><?php endif; ?>
</section>

<?php if (!$student): ?>
    <section class="panel empty-state"><h3>No student profile linked</h3><p>Ask the admin to connect this login to an admitted student record.</p></section>
<?php else: ?>
    <section class="student-portal-hero">
        <div>
            <?php if (!empty($student['profile_picture'])): ?><img class="portal-avatar" src="<?= e($student['profile_picture']) ?>" alt="<?= e($fullName) ?>"><?php else: ?><span class="portal-avatar placeholder"><?= e($initials) ?></span><?php endif; ?>
            <div>
                <p class="eyebrow">Welcome back</p>
                <h2><?= e($fullName) ?></h2>
                <p><?= e($student['registration_no']) ?> | <?= e($student['class_name']) ?> <?= e($student['section']) ?></p>
            </div>
        </div>
    </section>

    <section class="stat-grid">
        <article class="stat-card"><span>results</span><strong><?= e(count($results)) ?></strong><p>subjects with recorded marks</p></article>
        <article class="stat-card"><span>exam timetable</span><strong><?= e(count($examSchedules)) ?></strong><p>scheduled exam papers</p></article>
        <article class="stat-card"><span>class timetable</span><strong><?= e(count($timetables)) ?></strong><p>weekly lesson periods</p></article>
        <article class="stat-card"><span>attendance</span><strong><?= e(count($attendance)) ?></strong><p>recent attendance records</p></article>
    </section>

    <section class="student-portal-grid">
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Results</p><h3>Exam Scores</h3></div><a href="/student_portal/report-card">Print</a></div>
            <div class="table-wrap"><table><thead><tr><th>Subject</th><th>Score</th><th>Obtainable</th><th>Grade</th></tr></thead><tbody>
                <?php foreach ($results as $row): ?><tr><td><?= e($row['subject_name']) ?></td><td><?= e($row['student_score']) ?></td><td><?= e($row['total_mark']) ?></td><td><?= e($row['grade']) ?></td></tr><?php endforeach; ?>
                <?php if (empty($results)): ?><tr><td colspan="4">No result has been published for your class yet.</td></tr><?php endif; ?>
            </tbody></table></div>
        </article>
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Attendance</p><h3>Recent Attendance</h3></div></div>
            <div class="table-wrap"><table><thead><tr><th>Date</th><th>Status</th><th>Remark</th></tr></thead><tbody>
                <?php foreach ($attendance as $row): ?><tr><td><?= e($row['attendance_date']) ?></td><td><span class="status"><?= e($row['status']) ?></span></td><td><?= e($row['remark']) ?></td></tr><?php endforeach; ?>
                <?php if (empty($attendance)): ?><tr><td colspan="3">No attendance record is available yet.</td></tr><?php endif; ?>
            </tbody></table></div>
        </article>
    </section>

    <section class="student-portal-grid">
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Exam timetable</p><h3>Scheduled Papers</h3></div></div>
            <div class="table-wrap"><table><thead><tr><th>Date</th><th>Subject</th><th>Term</th><th>Session</th><th>Time</th></tr></thead><tbody>
                <?php foreach ($examSchedules as $row): ?><tr><td><?= e($row['exam_date']) ?></td><td><?= e($row['subject_name']) ?></td><td><?= e($row['school_term']) ?></td><td><?= e($row['school_session']) ?></td><td><?= e($row['start_time']) ?> - <?= e($row['end_time']) ?></td></tr><?php endforeach; ?>
                <?php if (empty($examSchedules)): ?><tr><td colspan="5">No exam timetable has been published for your class yet.</td></tr><?php endif; ?>
            </tbody></table></div>
        </article>
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Class timetable</p><h3>Weekly Lessons</h3></div></div>
            <div class="table-wrap"><table><thead><tr><th>Day</th><th>Subject</th><th>Teacher</th><th>Time</th></tr></thead><tbody>
                <?php foreach ($timetables as $row): ?>
                    <?php $teacherName = trim(($row['name'] ?? '') . ' ' . ($row['middle_name'] ?? '') . ' ' . ($row['surname'] ?? '')); ?>
                    <tr><td><?= e($row['day_of_week']) ?></td><td><?= e($row['subject_name']) ?></td><td><?= e($teacherName) ?></td><td><?= e($row['start_time']) ?> - <?= e($row['end_time']) ?></td></tr>
                <?php endforeach; ?>
                <?php if (empty($timetables)): ?><tr><td colspan="4">No class timetable has been published for your class yet.</td></tr><?php endif; ?>
            </tbody></table></div>
        </article>
    </section>

    <section class="student-portal-grid">
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Assignments</p><h3>Class Work</h3></div></div>
            <?php foreach ($assignments as $assignment): ?>
                <form class="assignment-card" method="post" action="/student_portal/assignment/submit" enctype="multipart/form-data">
                    <input type="hidden" name="assignment_id" value="<?= e($assignment['id']) ?>">
                    <strong><?= e($assignment['title']) ?></strong>
                    <span><?= e($assignment['subject']) ?> | Due <?= e($assignment['due_date']) ?></span>
                    <p><?= e($assignment['description']) ?></p>
                    <textarea name="submission_text" rows="3" placeholder="Write your answer or note for the teacher"></textarea>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.txt">
                    <button type="submit"><?= $assignment['submission_status'] ? 'Submit Again' : 'Submit Assignment' ?></button>
                    <?php if ($assignment['submission_status']): ?><small>Last submission: <?= e($assignment['submitted_at']) ?> <?= e($assignment['attachment_name']) ?></small><?php endif; ?>
                </form>
            <?php endforeach; ?>
            <?php if (empty($assignments)): ?><p class="muted">No assignment has been posted for your class yet.</p><?php endif; ?>
        </article>
        <article class="panel">
            <div class="panel-header"><div><p class="eyebrow">Messages</p><h3>Communication</h3></div></div>
            <div class="message-list">
                <?php foreach ($messages as $message): ?><article><span><?= e($message['channel']) ?> | <?= e($message['created_at']) ?></span><strong><?= e($message['subject']) ?></strong><p><?= e(substr(strip_tags($message['message']), 0, 160)) ?></p></article><?php endforeach; ?>
                <?php if (empty($messages)): ?><p class="muted">No message is available yet.</p><?php endif; ?>
            </div>
        </article>
    </section>

    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">Accounting</p><h3>Fee Records</h3></div></div>
        <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Class</th><th>Term</th><th>Session</th><th>Amount</th><th>Status</th></tr></thead><tbody>
            <?php foreach ($fees as $fee): ?><tr><td><?= e($fee['invoice_no']) ?></td><td><?= e($fee['class_name']) ?></td><td><?= e($fee['school_term']) ?></td><td><?= e($fee['school_session']) ?></td><td><?= money($fee['amount']) ?></td><td><span class="status"><?= e($fee['status']) ?></span></td></tr><?php endforeach; ?>
            <?php if (empty($fees)): ?><tr><td colspan="6">No fee record has been created for your class.</td></tr><?php endif; ?>
        </tbody></table></div>
    </section>
<?php endif; ?>
