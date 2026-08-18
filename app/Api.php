<?php

declare(strict_types=1);

namespace App;

final class Api
{
    private ApiAuth $auth;

    public function __construct(private Repository $repository)
    {
        $this->auth = new ApiAuth($repository);
    }

    public function handle(string $method, string $path): void
    {
        try {
            $route = trim(substr($path, 4), '/') ?: '';

            if ($method === 'POST' && $route === 'login') {
                $this->login();
                return;
            }
            if ($method === 'POST' && $route === 'logout') {
                $this->logout();
                return;
            }
            if ($method === 'GET' && $route === 'me') {
                ApiResponse::success(['user' => $this->auth->requireUser()]);
                return;
            }
            if ($method === 'GET' && $route === 'dashboard') {
                $this->dashboard();
                return;
            }

            if ($this->notifications($method, $route)) {
                return;
            }
            if ($this->student($method, $route)) {
                return;
            }
            if ($this->parent($method, $route)) {
                return;
            }
            if ($this->teacher($method, $route)) {
                return;
            }
            if ($this->admin($method, $route)) {
                return;
            }

            ApiResponse::error('API route not found.', 404);
        } catch (\Throwable $exception) {
            error_log('[Smapis API] ' . $exception->getMessage());
            ApiResponse::error('A server error occurred while processing the API request.', 500);
        }
    }

    private function login(): void
    {
        $input = $this->input();
        $email = trim($input['email'] ?? '');
        $password = (string) ($input['password'] ?? '');
        $user = $this->repository->findUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            ApiResponse::error('Invalid email or password.', 401);
            return;
        }

        $token = $this->repository->createApiToken(
            (int) $user['id'],
            (string) $user['role'],
            trim($input['device_name'] ?? '') ?: null,
            trim($input['device_type'] ?? '') ?: null
        );

        ApiResponse::success([
            'token' => $token,
            'user' => $this->repository->publicUser($user),
        ], 'Login successful');
    }

    private function logout(): void
    {
        $this->repository->revokeApiToken($this->auth->bearerToken());
        ApiResponse::success([], 'Logged out successfully.');
    }

    private function dashboard(): void
    {
        $user = $this->auth->requireUser();
        $role = $user['role'] ?? '';
        $data = match ($role) {
            'student' => $this->studentDashboard($user),
            'parent' => $this->parentDashboard($user),
            'teacher' => $this->teacherDashboard($user, $this->repository->getCurrentTeacherIdFromUser((int) ($user['id'] ?? 0))),
            'admin', 'superadmin' => $this->adminDashboard($user),
            default => ['user' => $user, 'message' => 'Dashboard is not available for this role yet.'],
        };

        ApiResponse::success($this->clean($data));
    }

    private function notifications(string $method, string $route): bool
    {
        if ($method === 'GET' && $route === 'notifications') {
            $user = $this->auth->requireUser();
            ApiResponse::success([
                'notifications' => $this->repository->getUserNotifications((int) $user['id'], $user['role'], 50),
                'unread_count' => $this->repository->getUnreadNotificationCount((int) $user['id'], $user['role']),
            ]);
            return true;
        }

        if ($method === 'POST' && $route === 'notifications/read') {
            $user = $this->auth->requireUser();
            $this->repository->markNotificationAsRead((int) ($this->input()['id'] ?? 0), (int) $user['id'], $user['role']);
            ApiResponse::success([], 'Notification marked as read.');
            return true;
        }

        if ($method === 'POST' && $route === 'notifications/read-all') {
            $user = $this->auth->requireUser();
            $this->repository->markAllNotificationsAsRead((int) $user['id'], $user['role']);
            ApiResponse::success([], 'All notifications marked as read.');
            return true;
        }

        return false;
    }

    private function student(string $method, string $route): bool
    {
        if (!str_starts_with($route, 'student/')) {
            return false;
        }

        $user = $this->auth->requireRole(['student']);
        $student = $this->repository->studentForPortal($user);
        if (!$student) {
            ApiResponse::error('Student profile was not found.', 404);
            return true;
        }

        if ($method === 'GET' && $route === 'student/profile') {
            ApiResponse::success(['student' => $this->clean($student)]);
            return true;
        }
        if ($method === 'GET' && $route === 'student/homework') {
            ApiResponse::success(['homework' => $this->clean($this->repository->studentPortalAssignments($student))]);
            return true;
        }
        if ($method === 'GET' && preg_match('#^student/homework/(\d+)$#', $route, $m)) {
            $homework = $this->studentHomeworkById($student, (int) $m[1]);
            $homework ? ApiResponse::success(['homework' => $this->clean($homework)]) : ApiResponse::error('Homework not found.', 404);
            return true;
        }
        if ($method === 'POST' && preg_match('#^student/homework/(\d+)/submit$#', $route, $m)) {
            $this->submitHomework($student, (int) $m[1]);
            return true;
        }
        if ($method === 'GET' && $route === 'student/results') {
            ApiResponse::success(['results' => $this->clean($this->repository->studentExamResults((int) $student['id'], [], $user))]);
            return true;
        }
        if ($method === 'GET' && $route === 'student/attendance') {
            $month = trim($_GET['month'] ?? '');
            $rows = $month !== '' ? $this->repository->studentPortalAttendanceByMonth((int) $student['id'], $month) : $this->repository->studentPortalAttendance((int) $student['id']);
            ApiResponse::success(['attendance' => $this->clean($rows)]);
            return true;
        }
        if ($method === 'GET' && $route === 'student/fees') {
            ApiResponse::success([
                'fees' => $this->clean($this->repository->studentPortalFees($student)),
                'summary' => $this->repository->studentPortalFeeSummary($student),
            ]);
            return true;
        }
        if ($method === 'GET' && $route === 'student/announcements') {
            ApiResponse::success(['announcements' => $this->clean($this->repository->announcements())]);
            return true;
        }

        return false;
    }

    private function parent(string $method, string $route): bool
    {
        if (!str_starts_with($route, 'parent/')) {
            return false;
        }

        $user = $this->auth->requireRole(['parent']);
        if ($method === 'GET' && $route === 'parent/children') {
            ApiResponse::success(['children' => $this->clean($this->repository->parentPortalChildren($user))]);
            return true;
        }
        if ($method === 'GET' && $route === 'parent/announcements') {
            ApiResponse::success(['announcements' => $this->clean($this->repository->announcements())]);
            return true;
        }
        if ($method === 'GET' && $route === 'parent/notifications') {
            ApiResponse::success(['notifications' => $this->repository->getUserNotifications((int) $user['id'], $user['role'], 50)]);
            return true;
        }
        if (preg_match('#^parent/children/(\d+)(?:/(homework|results|attendance|fees))?$#', $route, $m)) {
            $child = $this->repository->parentPortalChildById($user, (int) $m[1]);
            if (!$child) {
                ApiResponse::error('Child record not found or not linked to this parent.', 404);
                return true;
            }
            $section = $m[2] ?? '';
            $data = match ($section) {
                'homework' => ['homework' => $this->repository->studentPortalAssignments($child)],
                'results' => ['results' => $this->repository->studentExamResults((int) $child['id'], [], ['role' => 'admin', 'id' => 0, 'email' => ''])],
                'attendance' => ['attendance' => $this->repository->studentPortalAttendance((int) $child['id'])],
                'fees' => ['fees' => $this->repository->studentPortalFees($child), 'summary' => $this->repository->studentPortalFeeSummary($child)],
                default => ['child' => $child],
            };
            ApiResponse::success($this->clean($data));
            return true;
        }

        return false;
    }

    private function teacher(string $method, string $route): bool
    {
        if (!str_starts_with($route, 'teacher/')) {
            return false;
        }

        $user = $this->auth->requireRole(['teacher']);
        $teacherId = $this->repository->getCurrentTeacherIdFromUser((int) ($user['id'] ?? 0));
        if ($method === 'GET' && $route === 'teacher/dashboard') {
            ApiResponse::success($this->clean($this->teacherDashboard($user, $teacherId)));
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/classes') {
            ApiResponse::success(['classes' => $this->clean($this->repository->teacherAssignmentsForUser($user))]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/subjects') {
            ApiResponse::success(['subjects' => $this->clean($teacherId > 0 ? $this->repository->getTeacherAssignedSubjects($teacherId) : [])]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/students') {
            ApiResponse::success(['students' => $this->clean($teacherId > 0 ? $this->repository->getTeacherAssignedStudents($teacherId) : [])]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/homework') {
            ApiResponse::success(['homework' => $this->clean($this->repository->homeworksForUser($user))]);
            return true;
        }
        if ($method === 'POST' && $route === 'teacher/homework/create') {
            $this->createTeacherHomework($user);
            return true;
        }
        if ($method === 'GET' && preg_match('#^teacher/homework/(\d+)/submissions$#', $route, $m)) {
            $homework = $this->repository->homeworkById((int) $m[1]);
            if (!$homework || !$this->teacherCanAccessClass($user, $homework['class_name'] ?? '', $homework['section'] ?? '')) {
                ApiResponse::error('Homework not found for your assigned class.', 404);
                return true;
            }
            ApiResponse::success(['submissions' => $this->clean($this->repository->homeworkSubmissions((int) $m[1]))]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/timetable') {
            $filters = [];
            foreach (['id', 'class_id', 'subject_id'] as $field) {
                if (!empty($_GET[$field])) {
                    $filters[$field] = (int) $_GET[$field];
                }
            }
            foreach (['class_name', 'section', 'day_of_week'] as $field) {
                if (!empty($_GET[$field])) {
                    $filters[$field] = trim((string) $_GET[$field]);
                }
            }
            ApiResponse::success([
                'timetable' => $this->clean($teacherId > 0 ? $this->repository->getTeacherTimetable($teacherId, $filters) : []),
            ]);
            return true;
        }
        if ($method === 'POST' && $route === 'teacher/timetable/create') {
            $this->createTeacherTimetable($user, $teacherId);
            return true;
        }
        if ($method === 'POST' && preg_match('#^teacher/homework/submissions/(\d+)/mark$#', $route, $m)) {
            $input = $this->input();
            $this->repository->markHomeworkSubmission([
                'submission_id' => (int) $m[1],
                'score' => (float) ($input['score'] ?? 0),
                'feedback' => trim($input['feedback'] ?? ''),
                'status' => trim($input['status'] ?? 'marked'),
                'private_note' => trim($input['private_note'] ?? ''),
                'correction_file_path' => '',
                'correction_file_name' => '',
                'marked_by' => (int) $user['id'],
            ]);
            ApiResponse::success([], 'Submission marked.');
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/attendance') {
            ApiResponse::success(['students' => $this->clean($teacherId > 0 ? $this->repository->getTeacherAssignedStudents($teacherId) : [])]);
            return true;
        }
        if ($method === 'POST' && $route === 'teacher/attendance/mark') {
            $this->markStudentAttendance($user, $teacherId);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/messages') {
            ApiResponse::success(['messages' => $this->clean($this->repository->getTeacherConversations($teacherId))]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/messages/parents') {
            $conversations = $teacherId > 0 ? $this->repository->getTeacherConversations($teacherId) : [];
            $messages = [];
            foreach ($conversations as $conversation) {
                if (($conversation['conversation_type'] ?? '') === 'teacher_parent') {
                    $messages[] = $conversation;
                }
            }
            ApiResponse::success(['messages' => $this->clean($messages)]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/messages/teachers') {
            $conversations = $teacherId > 0 ? $this->repository->getTeacherConversations($teacherId) : [];
            $messages = [];
            foreach ($conversations as $conversation) {
                if (($conversation['conversation_type'] ?? '') === 'teacher_teacher') {
                    $messages[] = $conversation;
                }
            }
            ApiResponse::success(['messages' => $this->clean($messages)]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/payslips') {
            ApiResponse::success(['payslips' => $this->clean($teacherId > 0 ? $this->repository->getTeacherPayslips($teacherId) : [])]);
            return true;
        }
        if ($method === 'GET' && $route === 'teacher/my-attendance') {
            $filters = [
                'month' => trim($_GET['month'] ?? date('Y-m')),
                'status' => trim($_GET['status'] ?? ''),
            ];
            ApiResponse::success([
                'attendance' => $this->clean($teacherId > 0 ? $this->repository->getTeacherStaffAttendance($teacherId, $filters) : []),
                'summary' => $this->clean($teacherId > 0 ? $this->repository->getTeacherAttendanceSummary($teacherId) : []),
            ]);
            return true;
        }

        return false;
    }

    private function admin(string $method, string $route): bool
    {
        if (!str_starts_with($route, 'admin/')) {
            return false;
        }

        $user = $this->auth->requireRole(['admin', 'superadmin']);
        if ($method === 'GET' && $route === 'admin/dashboard') {
            ApiResponse::success($this->clean($this->adminDashboard($user)));
            return true;
        }
        if ($method === 'GET' && $route === 'admin/students') {
            ApiResponse::success(['students' => $this->clean($this->repository->admittedStudents())]);
            return true;
        }
        if ($method === 'GET' && $route === 'admin/teachers') {
            ApiResponse::success(['teachers' => $this->clean($this->repository->teachers())]);
            return true;
        }
        if ($method === 'GET' && $route === 'admin/parents') {
            ApiResponse::success(['parents' => $this->clean($this->repository->parentsList())]);
            return true;
        }
        if ($method === 'GET' && $route === 'admin/classes') {
            ApiResponse::success(['classes' => $this->clean($this->repository->classesList())]);
            return true;
        }
        if ($method === 'GET' && $route === 'admin/announcements') {
            ApiResponse::success(['announcements' => $this->clean($this->repository->announcements())]);
            return true;
        }
        if ($method === 'POST' && $route === 'admin/announcements/create') {
            $this->repository->createAnnouncement($this->input());
            $this->repository->createNotificationForRole('student', 'New announcement', trim($this->input()['title'] ?? 'School announcement'), 'announcement', '/notifications', 'normal', (int) $user['id']);
            ApiResponse::success([], 'Announcement created.');
            return true;
        }

        return false;
    }

    private function studentDashboard(array $user): array
    {
        $student = $this->repository->studentForPortal($user);
        if (!$student) {
            return ['student' => null, 'message' => 'Student profile was not found.'];
        }

        return [
            'student' => $student,
            'homework' => array_slice($this->repository->studentPortalAssignments($student), 0, 5),
            'attendance' => $this->repository->studentPortalAttendance((int) $student['id']),
            'fees_summary' => $this->repository->studentPortalFeeSummary($student),
            'announcements' => $this->repository->announcements(),
        ];
    }

    private function parentDashboard(array $user): array
    {
        return [
            'children' => $this->repository->parentPortalChildren($user),
            'messages' => $this->repository->parentPortalMessages($user),
            'announcements' => $this->repository->announcements(),
        ];
    }

    private function teacherDashboard(array $user, int $teacherId): array
    {
        $summary = $this->repository->getTeacherDashboardSummary($teacherId);

        return [
            'summary' => $this->clean($summary),
            'assigned_classes_count' => (int) ($summary['assigned_classes_count'] ?? 0),
            'assigned_subjects_count' => (int) ($summary['assigned_subjects_count'] ?? 0),
            'assigned_students_count' => (int) ($summary['assigned_students_count'] ?? 0),
            'pending_attendance_count' => (int) ($summary['pending_attendance_count'] ?? 0),
            'today_date' => $summary['today_date'] ?? date('Y-m-d'),
            'today_label' => $summary['today_label'] ?? date('l, d F Y'),
            'classes' => $this->clean($summary['classes'] ?? []),
            'subjects' => $this->clean($summary['subjects'] ?? []),
            'students' => $this->clean($summary['students'] ?? []),
            'today_timetable' => $this->clean($summary['today_timetable'] ?? []),
            'recent_parent_messages' => $this->clean($summary['recent_parent_messages'] ?? []),
            'recent_teacher_messages' => $this->clean($summary['recent_teacher_messages'] ?? []),
            'latest_payslip' => $this->clean($summary['latest_payslip'] ?? []),
            'attendance_summary' => $this->clean($summary['attendance_summary'] ?? []),
        ];
    }

    private function adminDashboard(array $user): array
    {
        return [
            'stats' => $this->repository->stats(),
            'fees' => $this->repository->feeSummary(),
            'attendance' => $this->repository->attendanceSummary(),
            'recent_students' => $this->repository->admittedStudents(),
            'announcements' => $this->repository->announcements(),
        ];
    }

    private function studentHomeworkById(array $student, int $id): ?array
    {
        foreach ($this->repository->studentPortalAssignments($student) as $homework) {
            if ((int) $homework['id'] === $id) {
                return $homework;
            }
        }

        return null;
    }

    private function submitHomework(array $student, int $homeworkId): void
    {
        $homework = $this->studentHomeworkById($student, $homeworkId);
        if (!$homework) {
            ApiResponse::error('Homework not found for this student.', 404);
            return;
        }

        $input = $this->input();
        $upload = $this->storeHomeworkUpload();
        $this->repository->submitHomework([
            'assignment_id' => $homeworkId,
            'student_id' => (int) $student['id'],
            'submission_text' => trim($input['submission_text'] ?? $input['answer_text'] ?? ''),
            'submission_link' => trim($input['submission_link'] ?? ''),
            'attachment_path' => $upload['path'],
            'attachment_name' => $upload['name'],
        ]);
        ApiResponse::success([], 'Homework submitted.');
    }

    private function createTeacherHomework(array $user): void
    {
        $input = $this->input();
        $staff = $this->repository->staffByEmail($user['email'] ?? '');
        if (!$staff || !$this->teacherCanAccessClass($user, $input['class_name'] ?? '', $input['section'] ?? '')) {
            ApiResponse::error('You can only create homework for your assigned class.', 403);
            return;
        }

        $subjectId = (int) ($input['subject_id'] ?? 0);
        $subject = trim($input['subject'] ?? '');
        $this->repository->saveHomework([
            'teacher_id' => (int) $staff['id'],
            'class_name' => trim($input['class_name'] ?? ''),
            'section' => trim($input['section'] ?? ''),
            'subject_id' => $subjectId,
            'subject' => $subject,
            'title' => trim($input['title'] ?? ''),
            'topic' => trim($input['topic'] ?? ''),
            'description' => trim($input['description'] ?? ''),
            'due_date' => substr(trim($input['due_at'] ?? $input['due_date'] ?? date('Y-m-d')), 0, 10),
            'due_at' => trim($input['due_at'] ?? date('Y-m-d H:i:s')),
            'total_marks' => max(1, (int) ($input['total_marks'] ?? 100)),
            'submission_type' => trim($input['submission_type'] ?? 'both'),
            'attachment_path' => '',
            'attachment_name' => '',
            'resource_link' => trim($input['resource_link'] ?? ''),
            'allow_late_submission' => !empty($input['allow_late_submission']) ? 1 : 0,
            'status' => in_array($input['status'] ?? 'published', ['draft', 'published', 'closed'], true) ? $input['status'] : 'published',
            'created_by' => (int) $user['id'],
            'updated_by' => (int) $user['id'],
        ]);
        ApiResponse::success([], 'Homework created.');
    }

    private function createTeacherTimetable(array $user, int $teacherId): void
    {
        $input = $this->input();
        if ($teacherId <= 0) {
            ApiResponse::error('Teacher profile was not found.', 404);
            return;
        }

        $classId = (int) ($input['class_id'] ?? 0);
        $subjectId = (int) ($input['subject_id'] ?? 0);
        $dayOfWeek = trim($input['day_of_week'] ?? '');
        $startTime = trim($input['start_time'] ?? '');
        $endTime = trim($input['end_time'] ?? '');
        $section = trim($input['section'] ?? '');

        if ($classId <= 0 || !$this->repository->teacherCanAccessClass($teacherId, $classId)) {
            ApiResponse::error('You can only create timetable rows for your assigned class.', 403);
            return;
        }
        if ($subjectId <= 0 || !$this->repository->teacherCanAccessSubject($teacherId, $subjectId)) {
            ApiResponse::error('You can only use subjects assigned to you.', 403);
            return;
        }
        if ($dayOfWeek === '' || $startTime === '' || $endTime === '') {
            ApiResponse::error('Class, subject, day, start time, and end time are required.', 422);
            return;
        }

        try {
            $start = strtotime($startTime);
            $end = strtotime($endTime);
            if ($start === false || $end === false || $start >= $end) {
                throw new \InvalidArgumentException('Start time must be before end time.');
            }

            if ($this->repository->teacherHasTimetableClash($teacherId, $dayOfWeek, $startTime, $endTime)) {
                throw new \RuntimeException('You already have a timetable clash for that day and time.');
            }

            if ($this->repository->classHasTimetableClash($classId, $section, $dayOfWeek, $startTime, $endTime)) {
                throw new \RuntimeException('The selected class already has a timetable clash for that day and time.');
            }

            $this->repository->createTeacherTimetable($teacherId, [
                'class_id' => $classId,
                'section' => $section,
                'subject_id' => $subjectId,
                'day_of_week' => $dayOfWeek,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'room' => trim($input['room'] ?? ''),
                'note' => trim($input['note'] ?? ''),
                'created_by' => (int) ($user['id'] ?? 0),
            ]);

            ApiResponse::success([], 'Timetable created.');
        } catch (\Throwable $exception) {
            $code = $exception instanceof \InvalidArgumentException ? 422 : 403;
            ApiResponse::error($exception->getMessage(), $code);
        }
    }

    private function markStudentAttendance(array $user, int $teacherId): void
    {
        $input = $this->input();
        $classId = (int) ($input['class_id'] ?? 0);
        $className = trim($input['class_name'] ?? '');
        $section = trim($input['section'] ?? '');

        if ($teacherId <= 0) {
            ApiResponse::error('Teacher access could not be resolved.', 403);
            return;
        }

        if ($classId <= 0 && $className !== '') {
            foreach ($this->repository->getTeacherAssignedClasses($teacherId) as $assignment) {
                if (($assignment['class_name'] ?? '') !== $className) {
                    continue;
                }
                if ($section !== '' && ($assignment['section'] ?? '') !== '' && ($assignment['section'] ?? '') !== $section) {
                    continue;
                }
                $classId = (int) ($assignment['class_id'] ?? 0);
                break;
            }
        }

        if ($classId <= 0 || !$this->repository->teacherCanAccessClass($teacherId, $classId)) {
            ApiResponse::error('You can only mark attendance for your assigned class.', 403);
            return;
        }

        $attendanceRows = [];
        if (!empty($input['attendance']) && is_array($input['attendance'])) {
            $attendanceRows = $input['attendance'];
        } elseif (!empty($input['student_id'])) {
            $studentId = (int) ($input['student_id'] ?? 0);
            if ($studentId > 0) {
                $attendanceRows[$studentId] = [
                    'status' => trim($input['status'] ?? 'Present'),
                    'remark' => trim($input['remark'] ?? ''),
                ];
            }
        }

        if (!$attendanceRows) {
            ApiResponse::error('Attendance rows are required.', 422);
            return;
        }

        try {
            $students = [];
            foreach ($this->repository->getStudentsForTeacherAttendance($teacherId, $classId) as $student) {
                $students[(int) ($student['id'] ?? 0)] = $student;
            }

            foreach ($attendanceRows as $studentId => $row) {
                $studentId = (int) $studentId;
                if ($studentId <= 0 || !$this->repository->teacherCanMarkStudentAttendance($teacherId, $studentId) || !isset($students[$studentId])) {
                    throw new \RuntimeException('You can only mark attendance for assigned students.');
                }
                $status = trim((string) ($row['status'] ?? 'Present'));
                if (!in_array($status, ['Present', 'Absent', 'Late', 'Excused'], true)) {
                    throw new \InvalidArgumentException('Attendance status is invalid.');
                }
            }

            $this->repository->saveTeacherStudentAttendance(
                $teacherId,
                $classId,
                trim($input['attendance_date'] ?? date('Y-m-d')),
                $attendanceRows
            );

            foreach ($attendanceRows as $studentId => $row) {
                $student = $students[(int) $studentId] ?? null;
                if (!$student) {
                    continue;
                }
                $status = trim((string) ($row['status'] ?? 'Present'));
                if (!in_array($status, ['Absent', 'Late'], true)) {
                    continue;
                }
                $this->notifyAttendanceParentForStudent(
                    $student,
                    $status,
                    trim($input['attendance_date'] ?? date('Y-m-d')),
                    trim((string) ($row['remark'] ?? '')),
                    (int) ($user['id'] ?? 0)
                );
            }

            ApiResponse::success([], 'Attendance saved.');
        } catch (\Throwable $exception) {
            $code = $exception instanceof \RuntimeException ? 403 : 422;
            ApiResponse::error($exception->getMessage(), $code);
        }
    }

    private function notifyAttendanceParentForStudent(array $student, string $status, string $date, string $remark, int $createdBy): void
    {
        $recipientLogin = trim((string) ($student['parent_username'] ?? ''));
        if ($recipientLogin === '') {
            $recipientLogin = trim((string) ($student['guardian_mobile'] ?? ''));
        }

        if ($recipientLogin === '') {
            return;
        }

        $parent = $this->repository->findUserByEmail($recipientLogin);
        if (!$parent || ($parent['role'] ?? '') !== 'parent') {
            return;
        }

        $firstName = trim((string) ($student['first_name'] ?? ''));
        if ($firstName === '') {
            $firstName = trim((string) ($student['applicant'] ?? ''));
        }

        $studentName = trim($firstName . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
        $classLabel = trim((string) (($student['class_name'] ?? '') . ' ' . ($student['section'] ?? '')));
        $message = $studentName . ' was marked ' . strtolower($status) . ' on ' . $date . '.';
        if ($classLabel !== '') {
            $message .= ' Class: ' . $classLabel . '.';
        }
        if ($remark !== '') {
            $message .= ' Note: ' . $remark;
        }

        $this->repository->createNotificationForUser(
            (int) $parent['id'],
            'Attendance notice',
            $message,
            'attendance',
            '/parent_portal',
            $status === 'Absent' ? 'high' : 'normal',
            $createdBy
        );
    }

    private function teacherCanAccessClass(array $user, string $className, string $section): bool
    {
        foreach ($this->repository->teacherAssignmentsForUser($user) as $assignment) {
            if (($assignment['class_name'] ?? '') === $className && ($assignment['section'] ?? '') === $section) {
                return true;
            }
        }

        return false;
    }

    private function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input') ?: '';
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return $_POST;
    }

    private function storeHomeworkUpload(): array
    {
        $empty = ['path' => '', 'name' => ''];
        $file = $_FILES['file'] ?? $_FILES['attachment'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $empty;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || (int) ($file['size'] ?? 0) > 10 * 1024 * 1024) {
            return $empty;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], true)) {
            return $empty;
        }

        $uploadDir = dirname(__DIR__) . '/public/uploads/homework';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $name = 'api-homework-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
        move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $name);

        return ['path' => '/uploads/homework/' . $name, 'name' => $file['name']];
    }

    private function clean(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $blocked = ['password', 'parent_password', 'private_note', 'token_hash'];
        $clean = [];
        foreach ($value as $key => $item) {
            if (is_string($key) && in_array($key, $blocked, true)) {
                continue;
            }
            $clean[$key] = $this->clean($item);
        }

        return $clean;
    }
}
