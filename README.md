# Deckoid ERP - Premium Lead Management System

A high-performance, professional-grade **Lead Management ERP System** built with **Vanilla PHP**, **MySQL**, and **Tailwind CSS**. Designed for high-density workspaces and modern SaaS aesthetics.

---

## ✨ Key Features

- **📊 Intelligence Dashboard**: 
  - Real-time analytics, lead growth trends, and source distribution charts.
  - Role-based views: Admins see system-wide performance; Staff see prioritized leads.
- **⚡ Smart Form Validation**: 
  - Premium red-highlighted alerts with icons for immediate error identification.
  - Automated tab-switching: If an error is in a hidden tab, the system automatically navigates you to the problem area.
- **🔐 Enterprise Security**: 
  - Role-Based Access Control (RBAC) for Admins and Staff.
  - Secure session handling, password hashing (BCRYPT), and CSRF protection.
- **📋 Lead Management (32+ Columns)**: 
  - Comprehensive tracking from initial inquiry to project onboarding.
  - Multi-tab interface for organized data entry (Basic, Location, Sales, Project).
- **🧾 Sales & Invoicing**: 
  - Professional invoice generation with GST support.
  - Dynamic item management and grand total calculations.
- **🔄 Activity Tracking**: 
  - Automated audit logs tracking every modification for full transparency.
- **📱 Ultra-Responsive UI**: 
  - Optimized for 14" and 15" laptops with a collapsible sidebar and mobile-ready layouts.

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.1+ (Vanilla)
- **Database**: MariaDB / MySQL 5.7+
- **Frontend**: Tailwind CSS 3.4, Google Fonts (Inter)
- **Charts**: Chart.js 4.4
- **Icons**: Heroicons & Lucide-inspired SVGs

---

## 🚀 Getting Started

### 1️⃣ Prerequisites
- **XAMPP / WAMP** (PHP 8.1+ and MySQL/MariaDB)
- Browser: Chrome, Edge, or Firefox (Recommended)

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
   > This script will initialize/reset the database. Use with caution in production.

4. **Launch**
   Navigate to: `http://localhost/Deckoid_ERP_System/login.php`

---

## 🔐 Default Access

| Role | Username | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` |

---

## 📂 Project Structure

```bash
├── admin/              # Core Management Modules (Dashboard, Leads, Invoices)
├── api/                # RESTful API Backend (Auth, Leads, Logs, Invoices)
├── assets/             # Global Assets (CSS, Branding, Images)
├── config/             # Environment and System Configuration
├── database/           # SQL Schema and Migration Scripts
├── includes/           # Core Logic (Auth, Database, Session, Utils)
│   └── components/     # UI Framework (Sidebar, Header, Navbar, Footer)
├── setup.php           # Automated Installation Script
└── login.php           # Premium Login Portal
```

---

## 🛡️ Best Practices

- **Security**: Update the `APP_KEY` in `.env` immediately.
- **Maintenance**: Periodically check `Activity Logs` to monitor system health.
- **Performance**: Use the built-in filters on the Lead List to manage high volumes of data efficiently.

---

## 🤝 Support & Contact

For technical support or customization requests, please reach out to the **Deckoid** development team.

© 2026 **Deckoid**. Empowering business growth through intelligent data management.