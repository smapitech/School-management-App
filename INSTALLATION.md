# Public Website Mobile Friendliness Fix

This patch fixes the public school website mobile navigation, tap targets, hero slideshow controls, blocked buttons and responsive overflow.

## Install

Upload and extract this ZIP from the project root:

```bash
cd /www/wwwroot/rehobothkkids.com/custom_school_management
unzip -o smapis_school_public_mobile_fix.zip
chown -R www:www public/assets/css/school-website-mobile-fix.css public/assets/js/school-website-mobile-fix.js resources/views/website/modern/partials/header.php resources/views/website/modern/partials/footer.php
php -l resources/views/website/modern/partials/header.php
php -l resources/views/website/modern/partials/footer.php
```

Then hard refresh the website on mobile or clear browser cache.

## Files included

- `resources/views/website/modern/partials/header.php`
- `resources/views/website/modern/partials/footer.php`
- `public/assets/css/school-website-mobile-fix.css`
- `public/assets/js/school-website-mobile-fix.js`

No database, routes, authentication or server configuration are changed.
