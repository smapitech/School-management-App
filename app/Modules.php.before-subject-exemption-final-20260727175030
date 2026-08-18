<?php

declare(strict_types=1);

namespace App;

final class Modules
{
    public static function all(): array
    {
        return [
            'students' => [
                'title' => 'Students',
                'icon' => 'users',
                'description' => 'Admitted student registry generated from the admission module.',
                'columns' => [],
            ],
            'admissions' => [
                'title' => 'Admissions',
                'icon' => 'clipboard-list',
                'description' => 'Online applications, screening, interviews, and acceptance tracking.',
                'columns' => ['applicant' => 'Applicant', 'class_name' => 'Class', 'guardian' => 'Guardian', 'stage' => 'Stage', 'created_at' => 'Date'],
                'create' => '/admissions/create',
                'fields' => ['applicant' => 'Applicant', 'class_name' => 'Class', 'guardian' => 'Guardian', 'stage' => 'Stage'],
            ],
            'staff' => [
                'title' => 'Staff',
                'icon' => 'id-card',
                'description' => 'Employee profiles, departments, roles, payroll readiness.',
                'columns' => ['employee_no' => 'Employee No', 'name' => 'Name', 'role' => 'Role', 'department' => 'Department', 'status' => 'Status'],
            ],
            'human_resource' => [
                'title' => 'Human Resource',
                'icon' => 'briefcase',
                'path' => '/human-resource',
                'description' => 'Staff payroll, salary templates, salary assignment, payment status, and payslips.',
                'columns' => [],
                'role_paths' => [
                    'teacher' => '/teacher/payroll',
                ],
                'role_children' => [
                    'teacher' => [
                        ['title' => 'Payroll Overview', 'path' => '/teacher/payroll'],
                        ['title' => 'My Payslips', 'path' => '/teacher/payslips'],
                        ['title' => 'Allowances', 'path' => '/teacher/allowances'],
                        ['title' => 'Deductions', 'path' => '/teacher/deductions'],
                        ['title' => 'Salary Increments', 'path' => '/teacher/increments'],
                    ],
                ],
                'role_titles' => [
                    'teacher' => 'Payroll',
                ],
                'overview_labels' => [
                    'teacher' => 'Payroll Overview',
                ],
            ],
            'classes' => [
                'title' => 'Classes',
                'icon' => 'school',
                'path' => '/classes',
                'description' => 'Class sections, teachers, rooms, and capacity planning.',
                'columns' => ['name' => 'Class', 'teacher' => 'Teacher', 'room' => 'Room', 'capacity' => 'Capacity'],
                'children' => [
                    ['title' => 'Class Assign', 'path' => '/classes/assign', 'permission_key' => 'class_assign'],
                    ['title' => 'Subject Form', 'path' => '/classes/subjects', 'permission_key' => 'subject_form'],
                    ['title' => 'Assign Subjects to Class', 'path' => '/classes/subject-assign', 'permission_key' => 'subject_assignment'],
                    ['title' => 'Subject Exemptions', 'path' => '/classes/subject-exemptions', 'permission_key' => 'subject_assignment'],
                    ['title' => 'Timetable', 'path' => '/classes/timetable', 'permission_key' => 'timetable'],
                ],
                'role_paths' => [
                    'teacher' => '/teacher/classes',
                ],
                'role_children' => [
                    'teacher' => [
                        ['title' => 'My Assigned Subjects', 'path' => '/teacher/classes/subjects', 'permission_key' => 'teacher_subjects'],
                        ['title' => 'My Students', 'path' => '/teacher/classes/students', 'permission_key' => 'teacher_students'],
                        ['title' => 'Class Timetable', 'path' => '/teacher/classes/timetable', 'permission_key' => 'teacher_timetable'],
                        ['title' => 'Create Timetable', 'path' => '/teacher/classes/timetable/create', 'permission_key' => 'teacher_timetable'],
                    ],
                ],
                'overview_labels' => [
                    'teacher' => 'My Assigned Classes',
                ],
            ],
            'attendance' => [
                'title' => 'Attendance',
                'icon' => 'calendar-check',
                'path' => '/attendance',
                'description' => 'Daily attendance summaries by class and date.',
                'columns' => [],
                'children' => [
                    ['title' => 'Student Attendance', 'path' => '/attendance/students', 'permission_key' => 'attendance'],
                    ['title' => 'Staff Attendance', 'path' => '/attendance/staff', 'permission_key' => 'teacher_attendance'],
                ],
                'role_paths' => [
                    'teacher' => '/teacher/attendance',
                ],
                'role_children' => [
                    'teacher' => [
                        ['title' => 'Attendance History', 'path' => '/teacher/attendance/history', 'permission_key' => 'teacher_attendance'],
                        ['title' => 'My Staff Attendance', 'path' => '/teacher/my-attendance', 'permission_key' => 'teacher_attendance'],
                    ],
                ],
                'overview_labels' => [
                    'teacher' => 'Mark Student Attendance',
                ],
            ],
            'exams' => [
                'title' => 'Exams',
                'icon' => 'file-chart',
                'description' => 'Configured exam records, mark distribution, teacher mark entry, grades, and exam schedules.',
                'columns' => [],
                'children' => [
                    ['title' => 'Mark Distribution', 'path' => '/exams/distribution', 'permission_key' => 'mark_distribution'],
                    ['title' => 'Exam Mark', 'path' => '/exams/marks', 'permission_key' => 'exam_mark'],
                    ['title' => 'Result Preview', 'path' => '/exams/result-preview', 'permission_key' => 'result_preview'],
                    ['title' => 'Exam Schedule', 'path' => '/exams/schedule', 'permission_key' => 'exam_schedule'],
                    ['title' => 'Marksheet Template', 'path' => '/exams/marksheet-templates', 'permission_key' => 'marksheet_templates'],
                ],
                'role_children' => [
                    'teacher' => [
                        ['title' => 'Exam Mark', 'path' => '/exams/marks', 'permission_key' => 'exam_mark'],
                        ['title' => 'Result Preview', 'path' => '/exams/result-preview', 'permission_key' => 'result_preview'],
                    ],
                ],
            ],
            'fees' => [
                'title' => 'Fees',
                'icon' => 'wallet',
                'path' => '/fees',
                'description' => 'Student fee collection, invoice view, reminders, and payment tracking.',
                'columns' => [],
            ],
            'homework' => [
                'title' => 'Homework',
                'icon' => 'book-open-check',
                'description' => 'Assignments, submissions, teacher feedback, and student school work tracking.',
                'columns' => [],
            ],
            'notifications' => [
                'title' => 'Notifications',
                'icon' => 'bell',
                'path' => '/notifications',
                'description' => 'School updates, alerts, reminders, and user notifications.',
                'columns' => [],
            ],
            'notification_manager' => [
                'title' => 'Notification Manager',
                'icon' => 'bell',
                'path' => '/notification-manager',
                'description' => 'Send role, user, and global notifications across the school portal.',
                'columns' => [],
            ],
            'website' => [
                'title' => 'Website Manager',
                'icon' => 'globe',
                'path' => '/website-manager',
                'description' => 'Select frontend templates, edit website content, manage gallery, teachers, and contact messages.',
                'columns' => [],
                'children' => [
                    ['title' => 'Website Editor', 'path' => '/website-manager'],
                    ['title' => 'Edit Website Content', 'path' => '/website-manager/edit'],
                    ['title' => 'Student Gallery', 'path' => '/website-manager/gallery'],
                    ['title' => 'Teachers', 'path' => '/website-manager/teachers'],
                    ['title' => 'Parent Reviews', 'path' => '/website-manager/reviews'],
                    ['title' => 'Contact Messages', 'path' => '/website-manager/contact-messages'],
                ],
            ],
            'library' => [
                'title' => 'Library',
                'icon' => 'library',
                'description' => 'Book inventory, copies, borrowing readiness, and stock status.',
                'columns' => ['title' => 'Book', 'author' => 'Author', 'copies' => 'Copies', 'status' => 'Status'],
            ],
            'communication' => [
                'title' => 'Communication',
                'icon' => 'messages',
                'description' => 'Announcements, circulars, SMS/email/WhatsApp campaign planning.',
                'columns' => [],
                'children' => [
                    ['title' => 'Internal Messaging', 'path' => '/communication/internal', 'permission_key' => 'communication'],
                    ['title' => 'Invoice Messages', 'path' => '/communication/invoices', 'permission_key' => 'communication'],
                    ['title' => 'Reminder Messages', 'path' => '/communication/reminders', 'permission_key' => 'communication'],
                    ['title' => 'API Setup', 'path' => '/communication/setup', 'permission_key' => 'communication'],
                ],
                'role_paths' => [
                    'teacher' => '/teacher/messages',
                ],
                'role_children' => [
                    'teacher' => [
                        ['title' => 'Parent Messages', 'path' => '/teacher/messages/parents', 'permission_key' => 'communication'],
                        ['title' => 'Teacher Messages', 'path' => '/teacher/messages/teachers', 'permission_key' => 'communication'],
                    ],
                ],
                'overview_labels' => [
                    'teacher' => 'Conversations',
                ],
            ],
            'accounting' => [
                'title' => 'Accounting',
                'icon' => 'wallet',
                'description' => 'Student fee collections, office deposits, expenses, balances, and finance reports.',
                'columns' => [],
                'children' => [
                    ['title' => 'Student Accounting', 'path' => '/accounting/student', 'permission_key' => 'accounting'],
                    ['title' => 'Office Accounting', 'path' => '/accounting/office', 'permission_key' => 'accounting'],
                ],
            ],
            'parent_portal' => [
                'title' => 'Parent Portal',
                'icon' => 'user-check',
                'description' => 'Monitor children, attendance, invoices, exam results, homework, and notices.',
                'columns' => [],
            ],
            'student_portal' => [
                'title' => 'Student Portal',
                'icon' => 'graduation-cap',
                'description' => 'View school work, personal details, attendance, fee status, results, and library records.',
                'columns' => [],
            ],
            'reports' => [
                'title' => 'Reports',
                'icon' => 'chart',
                'description' => 'Operational reports for leadership, academics, finance, and attendance.',
                'columns' => [],
            ],
            'settings' => [
                'title' => 'Settings',
                'icon' => 'settings',
                'description' => 'School profile, sessions, terms, grading, branches, and permissions.',
                'columns' => [],
            ],
        ];
    }
}
