# WanderDesk — Trip Registration Portal

A beginner-friendly PHP + MySQL project for registering trip participants. It includes a responsive frontend, client-side validation, server-side validation, MySQL storage, prepared statements, and CSRF protection.

## Tech stack
- HTML5
- CSS3
- JavaScript
- PHP 8+
- MySQL / MariaDB
- XAMPP (Apache + MySQL)

## Folder structure
```text
WanderDesk/
├── index.php
├── db.php
├── style.css
├── script.js
├── database.sql
└── README.md
```

## Run locally with XAMPP

1. Install and open XAMPP.
2. Start **Apache** and **MySQL**.
3. Copy the complete `WanderDesk` folder into:
   `C:\xampp\htdocs\`
4. Open phpMyAdmin:
   `http://localhost/phpmyadmin`
5. Select the **Import** tab and import `database.sql` from this folder.
   - Alternatively, open the SQL tab and run the contents of `database.sql`.
6. Check `db.php`. The default XAMPP settings are:
   - Host: `localhost`
   - Username: `root`
   - Password: empty
   - Database: `wanderdesk`
   Change these values if your MySQL setup uses different credentials.
7. Open:
   `http://localhost/WanderDesk/`

## Verify saved registrations

In phpMyAdmin, open `wanderdesk` → `registrations` → **Browse**. Submitted form records should appear there.

## Features
- Responsive trip registration form
- Destination dropdown
- Date, email, phone, age and traveller validation
- Server-side validation in PHP
- Prepared MySQL statements
- CSRF token check
- Output escaping to reduce cross-site scripting risk
- Success message after registration

## Troubleshooting

**Apache or MySQL will not start**
- Check whether another application is already using the required port.
- Read the XAMPP Control Panel logs for the exact error.

**Database connection failed**
- Confirm MySQL is running.
- Confirm `wanderdesk` database was imported.
- Confirm the credentials in `db.php`.

**404 Not Found**
- Confirm the folder is exactly `C:\xampp\htdocs\WanderDesk`.
- Open `http://localhost/WanderDesk/` (not by double-clicking `index.php`).

**PHP code is displayed as text**
- Open the project through `localhost` with Apache running. Do not open the PHP file directly.

## Important
This is a learning/portfolio project, not a production booking platform. Before public deployment, add authentication, rate limiting, privacy/retention rules, HTTPS, stronger operational logging, and appropriate server configuration.
