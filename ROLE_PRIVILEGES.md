# Login Levels And Privileges

The new system follows the old LMS style where menus and actions are controlled by role permissions.

Default demo password for every account:

```text
password
```

## Superadmin

Login:

```text
superadmin@school.test
```

Privileges:

- Full access to every module.
- Can view, create, edit, delete, upload, and change settings.
- Can edit global school identity: logo, school name, short name, phone, email, address, and academic year.
- Owns system-level setup such as permissions, uploads, and future branch controls.

## Admin

Login:

```text
admin@school.test
```

Privileges:

- School and staff management.
- Students, admissions, staff, classes, attendance, communication, reception, reports, and settings.
- Can create/edit school operational records.
- Cannot delete system data by default.

## Teacher

Login:

```text
teacher@school.test
```

Privileges:

- Class management.
- Can view students.
- Can manage classes, attendance, exams, and homework.
- Can view communication notices.

## Accountant

Login:

```text
accountant@school.test
```

Privileges:

- Financial management.
- Can manage fees, payroll, accounting, and finance reports.
- Can view students for billing context.

## Receptionist

Login:

```text
receptionist@school.test
```

Privileges:

- Front desk operations.
- Can manage admissions, student intake, enquiries, visitor logs, call logs, complaints, communication, and reception workflows.

## Parent

Login:

```text
parent@school.test
```

Privileges:

- Monitoring of children.
- Can view child-related students, attendance, fees, exams, homework, notices, and parent portal.
- Read-only by default.

## Student

Login:

```text
student@school.test
```

Privileges:

- School work and personal details management.
- Can view class, attendance, fees, exams, homework, library, communication, and student portal.
- Can edit limited student-portal details and create homework submissions in future workflows.

## Main Feature Areas Mirrored From The Old LMS

- Dashboard
- Student information and admissions
- Staff and payroll
- Classes and sections
- Attendance
- Fees and accounting
- Exams and results
- Homework and school work
- Library
- Transport
- Hostel
- Reception: enquiries, visitors, calls, complaints, follow ups
- Communication: notices, circulars, SMS/email/WhatsApp planning
- Parent portal
- Student portal
- Reports
- Global settings

The permissions are defined in:

```text
app/Auth.php
```
