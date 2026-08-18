# Tarah Foods payment-method visibility fix

This patch fixes the checkout message:

> Online checkout is temporarily unavailable. Please contact the store while payment settings are completed.

## What changed

- Paystack and Stripe credentials are now read directly from the `settings` table.
- Payment enable switches are also read directly instead of through the five-minute general settings cache.
- Leading/trailing spaces and accidental surrounding quotes are removed from copied API keys.
- Matching live/test modes are required for each public and secret key pair.
- Stripe restricted live/test keys are recognised when they are configured with the permissions required to create and retrieve Checkout Sessions.
- Masked, shortened, placeholder, and whitespace-containing values remain rejected.

## Install on tarahfoods.com

Upload this ZIP into:

`/www/wwwroot/tarahfoods.com`

Then run:

```bash
cd /www/wwwroot/tarahfoods.com
unzip -o tarahfoods-payment-method-visibility-fix-2026-07-26.zip
/www/server/php/82/bin/php artisan optimize:clear
/www/server/php/82/bin/php artisan payments:diagnose
```

No database migration is required for this visibility patch.

After installation, open **Admin → Settings → Payment gateway**, confirm the
three payment switches are enabled, and save once. Then reload `/checkout`.

