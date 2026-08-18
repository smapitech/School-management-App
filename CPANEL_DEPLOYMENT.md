# aaPanel / cPanel Deployment Guide

This app has two important entry/database files:

- Web entry: `public/index.php`
- MySQL import file: `database/schema_mysql.sql`
- Environment template: `.env.example`

## Option A: Best hosting setup

Use this when your hosting panel lets you set the domain document root.

1. Upload the full `custom_school_management` folder to your hosting account.
2. Set the domain or subdomain document root to:

   ```text
   custom_school_management/public
   ```

3. In aaPanel or cPanel, open **MySQL Databases**.
4. Create a database, for example:

   ```text
   yourcpanel_school_sms
   ```

5. Create a database user and password.
6. Add the user to the database with **All Privileges**.
7. Copy `.env.example` to `.env` and edit the new file with your real production values.

8. Set:

   ```text
   APP_ENV=production
   APP_INSTALLED=false
   APP_DEBUG=false
   APP_TIMEZONE=Africa/Lagos
   APP_SESSION_SECURE=true
   APP_SHOW_SAMPLE_ACCOUNTS=false
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=yourcpanel_school_sms
   DB_USERNAME=yourcpanel_school_user
   DB_PASSWORD=your_password
   ```

9. Visit your domain and open:

   ```text
   /install
   ```

   The wizard will create the database if needed, import `database/schema_mysql.sql`, write the live `.env` file, and redirect you to login.

10. Visit your domain.

11. Make sure these folders are writable by PHP:

   ```text
   storage
   storage/sessions
   public/uploads
   ```

   On most cPanel servers, `755` works for folders. Some hosts may require `775`.

## Option B: If your panel only gives you `public_html`

Use this when you cannot change the document root.

### Recommended public_html layout

Upload the whole `custom_school_management` app contents into `public_html`.

The root `.htaccess` will make visitors see clean URLs like:

```text
https://mydomain.com
https://mydomain.com/students
```

while internally loading:

```text
public/index.php
```

### More secure layout

If your host allows files outside `public_html`, use this instead:

1. Upload these folders outside `public_html`:

   ```text
   app
   config
   database
   resources
   storage
   ```

2. Upload the contents of `public/` into `public_html/`.
3. Open `public_html/index.php`.
4. Change the bootstrap path from:

   ```php
   require dirname(__DIR__) . '/app/bootstrap.php';
   ```

   to the real server path where you uploaded the `app` folder.

   Example:

   ```php
   require '/home/yourcpanel/custom_school_management/app/bootstrap.php';
   ```

5. Import `database/schema_mysql.sql` in phpMyAdmin.
6. Update `.env` with your production database name, user, and password, or let the installer write it for you.

## Clean URL Rewrite Files

Two `.htaccess` files are included:

- `.htaccess` in the project root hides `/public` from the URL.
- `public/.htaccess` sends clean routes like `/students` to `public/index.php`.

If your domain document root is already set to `custom_school_management/public`, the `public/.htaccess` file is the one Apache will use.

If you uploaded the full app into `public_html`, the root `.htaccess` file is the one that hides the `public` folder.

## What Not To Upload Publicly

These should not be directly browsable by visitors:

- `app/`
- `config/`
- `database/`
- `resources/`
- `storage/`

Only `public/` should be the web root when possible.

## Local Development

For local testing, keep:

```php
'driver' => 'sqlite',
```

and run:

```bash
php -S 127.0.0.1:8088 -t public
```
