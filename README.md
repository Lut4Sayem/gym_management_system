# 🏋️ Gym Management System

A web-based Gym Management System built using **PHP** and **MySQL**, designed to manage users, memberships, equipment, body measurements, and receipts efficiently. This system supports multiple roles: **Admin**, **Receptionist**, **Trainer**, and **Member** — each with their own dashboard and functionality.

---

## 📁 Folder Structure

└── gym_management_system/
    ├── dashboard/
    │   ├── admin/
    │   │   ├── admin.php
    │   │   ├── manage_equipment.php
    │   │   ├── manage_membership_plan.php
    │   ├── receptionist/
    │   │   ├── receptionist.php
    │   │   ├── register_member.php
    │   │   ├── manage_members.php
    │   │   ├── generate_receipts.php
    │   ├── trainer/
    │   │   ├── trainer.php
    │   │   ├── manage_measurements.php
    │   ├── member/
    │   │   ├── member.php
    ├── login.php
    ├── logout.php
    ├── includes/
    │   ├── db_connect.php
    │   ├── session_check.php
    ├── assets/
    │   ├── css/
    │   │   └── style.css
    │   ├── js/
    │   │   └── script.js
    ├── index.php  <-- (Main login page)
    ├── dashboard_redirect.php


---

## 👥 User Roles & Dashboards

| Role         | Features                                                                 |
|--------------|--------------------------------------------------------------------------|
| **Admin**    | Manage gym equipment, create/edit membership plans                       |
| **Receptionist** | Register members, manage member data, generate payment receipts      |
| **Trainer**  | View and update member body measurements                                 |
| **Member**   | View personal info and progress (read-only access)                       |

---

## 🛠️ Tech Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, JS
- **Local Server:** XAMPP (Apache + MySQL)

---


