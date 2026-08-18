# Custom School Management System

A fresh custom school management starter inspired by the legacy Ramom feature set, built separately from the original folder.

## Features Included

- Modern admin dashboard
- Students, admissions, staff, classes, attendance, fees, exams, library, transport, hostel, communication, reports, and settings modules
- SQLite for local development and MySQL for production
- Clean PHP structure with separate public entry point, app layer, views, assets, and storage
- Responsive frontend with no external CDN dependency
- First-run installation wizard for automatic MySQL setup on aaPanel or cPanel

## Requirements

- PHP 8.1 or newer
- `pdo_sqlite` extension enabled
- `pdo_mysql` extension enabled for production

## Run Locally

From this folder:

```bash
php -S localhost:8088 -t public
```

Then open:

```text
http://localhost:8088
```

The SQLite database is created automatically at:

```text
storage/database.sqlite
```

For production, copy `.env.example` to `.env`, set `APP_ENV=production`, and then visit `/install` to let the wizard create the database and import the schema automatically.

## cPanel Upload

Use the full guide here:

```text
CPANEL_DEPLOYMENT.md
```

Important files for hosting:

- Web entry point: `public/index.php`
- Database config: `config/database.php`
- Environment template: `.env.example`
- MySQL import file: `database/schema_mysql.sql`
- Login levels and privileges: `ROLE_PRIVILEGES.md`
- Installer wizard: `/install`

## Demo Login Levels

All demo users use password:

```text
password
```

- Superadmin: `superadmin@school.test`
- Admin: `admin@school.test`
- Teacher: `teacher@school.test`
- Accountant: `accountant@school.test`
- Receptionist: `receptionist@school.test`
- Parent: `parent@school.test`
- Student: `student@school.test`

These sample credentials are hidden on the login screen in production unless `APP_SHOW_SAMPLE_ACCOUNTS=true`.
For local demos, you can leave that flag enabled or rely on the default local environment setting.

## Suggested Next Work

- Add real authentication and role permissions
- Add create/edit/delete flows for every module
- Add import/export for students, fees, exams, and attendance
- Add parent/student portals
- Add notification delivery through email/SMS/WhatsApp providers
- Add audit logs and automated tests
