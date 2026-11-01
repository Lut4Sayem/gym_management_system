# 🏋️ Gym Management System

A web-based gym management system built with **PHP** and **MySQL/MariaDB**. Four roles, each with its own dashboard:

| Role | What they can do |
|------|------------------|
| **Admin** | See gym stats (members, trainers, equipment, revenue), manage equipment, manage membership plans, edit own profile |
| **Receptionist** | Register members (and assign a trainer), edit/delete members, generate payment receipts, view receipt history |
| **Trainer** | See assigned members with their latest stats, record and delete body measurements, track weight change and BMI |
| **Member** | See current plan, when it expires and who their trainer is, view progress history, update own details and password |

## 🛠️ Tech Stack

- **Backend:** PHP 8.0+ (mysqli, prepared statements)
- **Database:** MySQL 8 / MariaDB 10.4+
- **Frontend:** HTML, CSS, vanilla JS (no frameworks)
- **Local server:** XAMPP (Apache + MySQL)

## 🚀 Setup (XAMPP)

1. Copy this folder into `C:\xampp\htdocs\` (or `/opt/lampp/htdocs/` on Linux).
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), create a database called `gym_management_system`, then **Import** `gym_management_system.sql`.
4. If your MySQL user/password isn't the XAMPP default (`root` / empty), edit `includes/db_connect.php`.
5. Visit `http://localhost/gym_management_system/`.

> Already imported an older copy of the database? Drop it and re-import. The new SQL file adds auto-increment IDs and a unique email rule that registering members and generating receipts depend on.

## 🔑 Demo Logins

| Role | Email | Password |
|------|-------|----------|
| Admin | lutfursayem@gmail.com | admin123 |
| Receptionist | nusratjahan@gmail.com | rec123 |
| Trainer | mdjahan@gmail.com | trainer123 |
| Member | tanvirahmed@gmail.com | mem1 |

The seed data stores these as plain text so they're easy to try. Each one is automatically replaced with a secure hash the first time it logs in. Every new password (registration, password change) is hashed straight away.

## 📁 Folder Structure

```
gym_management_system/
├── index.php                 # sends you to login.php
├── login.php / logout.php
├── dashboard_redirect.php    # routes each role to its dashboard
├── gym_management_system.sql # schema + demo data
├── includes/
│   ├── db_connect.php
│   ├── session_check.php     # login check, require_role(), e() escaping helper
│   └── password.php          # password check (hashes + legacy plain text)
├── dashboard/
│   ├── admin/        admin.php, manage_equipment.php, manage_membership_plan.php
│   ├── receptionist/ receptionist.php, register_member.php, manage_members.php, generate_receipts.php
│   ├── trainer/      trainer.php, manage_measurements.php
│   └── member/       member.php
├── assets/css/style.css, assets/js/script.js
├── images/
└── tests/password_test.php
```

## 🔒 Security

- Passwords hashed with `password_hash()` (bcrypt); plain-text seed passwords upgrade on first login
- Every query uses prepared statements (no SQL injection)
- Every page checks the user's role, so a member can't open admin pages by typing the URL
- All output is HTML-escaped (no XSS)
- Changes and deletes happen only via POST; the session cookie is `HttpOnly` and `SameSite=Lax`
- Members with payment receipts can't be deleted, so payment history is never lost

## 🧪 Tests

```
php tests/password_test.php
```

## 👨‍💻 Contributors

- MD. LUTFUR RAHMAN (Backend + DB + Overall Flow)
- MD. Kamrul Hasan (Receptionist panel)
- Sadik Ullah (Trainer panel)
