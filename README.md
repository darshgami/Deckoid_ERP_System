# 🚀 Deckoid ERP - Premium Lead Management System

![Deckoid ERP Logo](assets/ERP.png)

A high-performance, professional-grade **Lead Management ERP System** built with **Vanilla PHP**, **MySQL**, and **Tailwind CSS**. Designed for high-density workspaces and modern SaaS aesthetics.

---

## ✨ Key Features

- **📊 Dynamic Dashboard**: Real-time analytics, lead growth trends, and conversion statistics.
- **⚡ High-Density UI**: Optimized for 14" and 15" laptops to maximize data visibility without excessive scrolling.
- **🔐 Robust Authentication**: Centralized secure login with session management and role-based access control (Admin/Staff).
- **📋 Lead Management**: Comprehensive 32-column lead tracking system covering everything from initial contact to project start.
- **🔄 Activity Logs**: Automated tracking of all lead modifications for full auditability.
- **📱 Fully Responsive**: Seamless experience across mobile, tablet, and desktop with a collapsible sidebar and mobile-optimized navbar.
- **📥 CSV Export**: One-click data export for offline analysis and reporting.

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.1+ (Vanilla)
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: Tailwind CSS 3.x, Heroicons
- **Charts**: Chart.js for data visualization
- **Architecture**: Modular Component-based PHP (Header, Sidebar, Navbar, Footer)

---

## 🚀 Getting Started

### 1️⃣ Prerequisites
- **XAMPP / WAMP** (Recommended for local development)
- **PHP 8.1+**
- **MySQL 5.7+**

### 2️⃣ Installation

1. **Clone the Repository**
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/yourusername/Deckoid_ERP_System.git
   ```

2. **Configure Environment**
   Rename `.env.example` (or create `.env`) in the root directory and update your database credentials:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=lead_management
   DB_USERNAME=root
   DB_PASSWORD=
   APP_KEY=your_secure_random_string
   ```

3. **Initialize Database**
   Run the automated setup script to create the database, tables, and default user:
   ```bash
   php setup.php
   ```
   > [!IMPORTANT]
   > This script will reset the database if it already exists. Use with caution in production.

4. **Launch**
   Open your browser and navigate to: `http://localhost/Deckoid_ERP_System/login.php`

---

## 🔐 Default Access

| Role | Username | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` |

---

## 📂 Project Structure

```bash
├── admin/              # Management Pages (Leads, Dashboard, Staff, etc.)
├── api/                # RESTful API Endpoints (Auth, Leads, Logs)
├── assets/             # Core Assets (Images, Premium Logo, Tailwind Config)
├── config/             # Environment and Global Configuration
├── database/           # SQL Schema and Migration Scripts
├── includes/           # Reusable Logic (Auth Controller, Database Wrapper)
│   └── components/     # UI Components (Sidebar, Navbar, Footer)
├── setup.php           # Automated Installation & Reset Script
└── login.php           # Centralized Premium Login Portal
```

---

## 🛡️ Security Best Practices

- [ ] Update `APP_KEY` in `.env` immediately after installation.
- [ ] Change default `admin` password on first login.
- [ ] Ensure `exports/` and `uploads/` directories have appropriate write permissions.
- [ ] Disable `setup.php` in production environments.

---

## 🤝 Support

For technical support or feature requests, please contact the development team at `support@deckoid.com`.

---

© 2026 **Deckoid**. All Rights Reserved.