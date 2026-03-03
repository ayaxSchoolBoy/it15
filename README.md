# Gym Membership QR Code Verification System

## 1) Requirements
- PHP 7.4+
- MySQL/MariaDB
- Composer
- Camera access via HTTPS or localhost

## 2) Setup
1. **Create database**
   - Import: `database/schema.sql`

2. **Install QR library**
   - Run: `composer install`

3. **Configure API**
   - Edit: `api/config.php`
   - Set `base_url` (example: `http://localhost/it15`)
   - Set DB credentials if needed
   - Set `mail_enabled` to `true` once mail is configured

4. **Start server**
   - Use XAMPP/Apache and open:
     - Subscriber page: `/subscribe.html`
     - Admin login: `/admin/login.php`

## 3) Default Admin Login
- Username: `admin`
- Password: `admin123`

Change these in `admin/login.php` for production.

## 4) Notes
- Camera access requires HTTPS or localhost.
- Email sending uses PHP `mail()`; configure SMTP if required by your host.
- Scan history is stored in `scan_logs`.
