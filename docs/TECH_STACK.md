TECH_STACK.md
Lead Management ERP System
1. Project Overview
Property	Value
Application Name	Lead Management ERP System
Application Type	Web Application
Scale	MVP → Small Business Production
Target Users	Sales Teams, Business Owners
Primary Stack	PHP + MySQL + HTML/Tailwind
Deployment Type	XAMPP / Apache
MVP Timeline	4–6 Weeks
2. Frontend Technology Stack
Frontend Framework
Technology

HTML5 + Vanilla JavaScript (ES2022)

Version
HTML5
ECMAScript 2022
Documentation
HTML: HTML Documentation
JavaScript: JavaScript Documentation
Reason for Selection
Lightweight
Faster rendering
No unnecessary framework overhead
Easy integration with PHP
Easier hosting on XAMPP/shared hosting
Alternatives Considered
Alternative	Reason Rejected
React 18.2.0	Unnecessary complexity for MVP
Vue 3.4.21	Additional build tooling
Angular 17.3.0	Heavy for ERP MVP
Styling Framework
Technology

Tailwind CSS

Version

3.4.3

Documentation

Tailwind CSS Documentation

Reason for Selection
Fast UI development
Responsive utilities
Clean ERP-style layouts
Consistent spacing system
Smaller CSS maintenance
Alternatives Considered
Alternative	Reason Rejected
Bootstrap 5.3.3	Generic UI appearance
Material UI 5.15.15	React dependency
Bulma 1.0.0	Less utility flexibility
State Management
Technology

Vanilla JavaScript State Objects

Version

ES2022

Documentation

JavaScript Objects Documentation

Reason for Selection
Application scale is small
No complex global state needed
Faster development
Alternatives Rejected
Alternative	Reason Rejected
Redux Toolkit 2.2.3	Overkill for MVP
Zustand 4.5.2	Requires React
Pinia 2.1.7	Requires Vue
Forms & Validation
Technology

Native HTML5 Validation + Custom JavaScript Validation

Version

HTML5 + ES2022

Documentation

HTML Form Validation Docs

Reason for Selection
Lightweight
Fast validation
Minimal dependencies
HTTP Client
Technology

Fetch API

Version

Browser Native (ES2022)

Documentation

Fetch API Documentation

Reason for Selection
Native browser support
No external dependency
Lightweight
Alternatives Rejected
Alternative	Reason Rejected
Axios 1.6.8	Extra dependency unnecessary
UI Components
Technology

Custom Tailwind Components

Version

Tailwind CSS 3.4.3

Documentation

Tailwind Components Documentation

Reason for Selection
ERP-specific customization
Avoid heavy UI libraries
3. Backend Technology Stack
Runtime Environment
Technology

PHP

Version

8.2.17

Documentation

PHP Documentation

Reason for Selection
Native XAMPP compatibility
Easy deployment
Strong MySQL support
Low hosting cost
Alternatives Rejected
Alternative	Reason Rejected
Node.js 20.11.1	Extra infrastructure complexity
Django 5.0.4	Higher hosting resource usage
Laravel 11.2.0	Unnecessary framework overhead
Web Server
Technology

Apache HTTP Server

Version

2.4.58

Documentation

Apache Documentation

Reason for Selection
Included with XAMPP
Stable PHP integration
Database
Technology

MySQL

Version

8.0.36

Documentation

MySQL Documentation

Reason for Selection
Relational structure fits ERP
Fast filtering/searching
Excellent PHP compatibility
Alternatives Rejected
Alternative	Reason Rejected
MongoDB 7.0.7	Structured ERP data unsuitable
PostgreSQL 16.2	More setup complexity
Database Access Layer
Technology

PDO (PHP Data Objects)

Version

PHP 8.2 Native

Documentation

PDO Documentation

Reason for Selection
SQL injection protection
Native prepared statements
Lightweight
Authentication
Technology

PHP Sessions + password_hash()

Version

PHP 8.2 Native

Documentation

PHP Password Hash Docs

Reason for Selection
Secure
Session-based ERP access
No token complexity required
File Export
Technology

PhpSpreadsheet

Version

1.29.0

Documentation

PhpSpreadsheet Documentation

Reason for Selection
Accurate Excel export
UTF-8 support
Large dataset support
Email Service
Technology

PHPMailer

Version

6.9.1

Documentation

PHPMailer Documentation

Reason for Selection
SMTP support
Stable PHP ecosystem
Caching
Technology

No Dedicated Cache (MVP)

Reason
Small application scale
Minimal infrastructure
Low concurrent load
Future Option

Redis 7.2.4

File Storage
Technology

Local Server Storage

Path

/uploads

Reason for Selection
Simple deployment
No cloud dependency
4. Development Tooling
Package Manager
Technology

npm

Version

10.5.0

Documentation

npm Documentation

CSS Build Tool
Technology

PostCSS

Version

8.4.38

Documentation

PostCSS Documentation

Tailwind Build Tool
Technology

Tailwind CLI

Version

3.4.3

Code Formatting
Technology

Prettier

Version

3.2.5

Documentation

Prettier Documentation

5. Environment Variables
APP_NAME=Lead_Management_ERP
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=lead_management
DB_USERNAME=root
DB_PASSWORD=

SESSION_TIMEOUT=1800

EXPORT_PATH=/exports
UPLOAD_PATH=/uploads

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=
SMTP_PASSWORD=
SMTP_FROM_EMAIL=
SMTP_FROM_NAME=Lead ERP

BCRYPT_ROUNDS=12

MAX_LOGIN_ATTEMPTS=5
LOGIN_LOCKOUT_SECONDS=900

CORS_ALLOWED_ORIGIN=http://localhost

RATE_LIMIT_REQUESTS=100
RATE_LIMIT_WINDOW=60
6. package.json Scripts
{
  "scripts": {
    "dev": "tailwindcss -i ./assets/css/input.css -o ./assets/css/output.css --watch",
    "build": "tailwindcss -i ./assets/css/input.css -o ./assets/css/output.css --minify",
    "format": "prettier --write .",
    "lint": "eslint ."
  }
}
7. Frontend Dependency Lock
{
  "tailwindcss": "3.4.3",
  "postcss": "8.4.38",
  "autoprefixer": "10.4.19",
  "prettier": "3.2.5",
  "eslint": "8.57.0"
}
8. Backend Dependency Lock
{
  "php": "8.2.17",
  "mysql": "8.0.36",
  "phpoffice/phpspreadsheet": "1.29.0",
  "phpmailer/phpmailer": "6.9.1"
}
9. Security Configuration
Password Security
Setting	Value
Hash Algorithm	PASSWORD_BCRYPT
bcrypt rounds	12
Session Security
Setting	Value
Session Timeout	30 minutes
Secure Cookies	Enabled
HttpOnly Cookies	Enabled
SameSite	Strict
Rate Limiting
Setting	Value
Login Attempts	5
Lockout Duration	15 minutes
CORS Policy
Allowed Origin:
http://localhost
SQL Injection Protection
PDO prepared statements mandatory
Dynamic SQL concatenation prohibited
File Upload Restrictions
Rule	Value
Max Upload Size	10MB
Allowed Extensions	xlsx, csv
Executable Uploads	Blocked
