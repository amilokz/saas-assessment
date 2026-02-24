<div align="center">
  <h1>🚀 SaaS Assessment Platform</h1>
  <p><strong>A complete, production-ready multi-tenant SaaS platform built with Laravel</strong></p>
  <p>Company approval system | Trial management | Subscription billing | Team collaboration | File storage | Audit logging</p>

  <!-- Badges -->
  <p>
    <img src="https://img.shields.io/badge/version-1.0.0-blue.svg" alt="Version 1.0.0">
    <img src="https://img.shields.io/badge/status-live-brightgreen.svg" alt="Live">
    <img src="https://img.shields.io/badge/Laravel-11-red.svg" alt="Laravel 11">
    <img src="https://img.shields.io/badge/PHP-8.2%2B-purple.svg" alt="PHP 8.2+">
    <img src="https://img.shields.io/badge/license-MIT-green.svg" alt="MIT License">
    <img src="https://img.shields.io/github/stars/amilokz/saas-assessment?style=social" alt="GitHub stars">
  </p>

  <!-- Live Demo & Repo Links -->
  <h3>
    <a href="https://saas-assessment.wuaze.com">🌐 Live Demo</a> •
    <a href="https://github.com/amilokz/saas-assessment">📦 GitHub Repo</a> •
    <a href="https://github.com/amilokz/saas-assessment/wiki">📚 Documentation</a>
  </h3>
</div>

---

## 📋 **Table of Contents**
- [✨ Overview](#-overview)
- [🔑 Demo Credentials](#-demo-credentials)
- [🚀 Key Features](#-key-features)
- [🛠️ Tech Stack](#️-tech-stack)
- [📁 Project Structure](#-project-structure)
- [⚡ Quick Installation](#-quick-installation)
- [📊 User Roles & Permissions](#-user-roles--permissions)
- [🔄 Workflow Examples](#-workflow-examples)
- [💳 Subscription Plans](#-subscription-plans)
- [🔐 Security Features](#-security-features)
- [📈 API Endpoints](#-api-endpoints)
- [🚀 Deployment Guide](#-deployment-guide)
- [🐛 Troubleshooting](#-troubleshooting)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)
- [🙏 Acknowledgments](#-acknowledgments)
- [📞 Support & Contact](#-support--contact)

---

## ✨ **Overview**

**SaaS Assessment Platform** is a fully-functional, production-ready multi-tenant Software-as-a-Service (SaaS) boilerplate built with **Laravel 11**. It provides all the essential features needed to launch a modern SaaS product, including company registration with approval workflow, subscription management via Stripe, team collaboration tools, secure file storage, and comprehensive audit logging.

Whether you're building the next big SaaS or learning advanced Laravel concepts, this platform serves as a solid foundation with **complete data isolation** between tenants (companies) and a **professional, responsive UI**.

> **Project Status:** ✅ **COMPLETE, LIVE & PRODUCTION-READY** (Last Updated: January 2026)

---

## 🔑 **Demo Credentials**

You can explore the live platform using these test accounts. **Please do not share or misuse these credentials.**

### 👑 **Super Admin (Platform Owner)**
Access the full system, manage companies, approve registrations, and configure plans.
- **URL:** [https://saas-assessment.wuaze.com/super-admin/dashboard](https://saas-assessment.wuaze.com/super-admin/dashboard)
- **Email:** `admin@saas.test`
- **Password:** `password123`
- **Access:** Full system control, company approvals, plan management, audit logs.

### 🏢 **Company Admin (Sample Company)**
Experience the platform from a tenant's perspective: manage your team, subscriptions, and files.
- **URL:** [https://saas-assessment.wuaze.com/company/dashboard](https://saas-assessment.wuaze.com/company/dashboard)
- **Email:** `admin@newcompany.com`
- **Password:** `password`
- **Access:** Company dashboard, team management, file uploads, subscriptions, support tickets.

---

## 🚀 **Key Features**

### ✅ **Core Functionality**
| Feature | Description |
| :--- | :--- |
| **Multi-tenant Architecture** | Complete data isolation between companies for security and scalability. |
| **Company Registration System** | Public registration form for new companies with an automatic **7-day trial**. |
| **Approval Workflow** | Super Admin must approve or reject new company registrations before full access is granted. |
| **Subscription Management** | Fully integrated with **Stripe** for recurring billing. Multiple plans (Monthly/Yearly). |
| **Team Collaboration** | Invite team members with a **4-tier role system** (Admin, Support, Normal). |
| **File Storage** | Secure, company-isolated file management with upload, download, and delete capabilities. |
| **Support System** | Built-in ticketing system for companies to communicate with the platform owner. |
| **Audit Logging** | Comprehensive activity tracking for all critical actions across the platform. |
| **Professional UI** | Modern, clean, and fully responsive interface built with **Bootstrap 5**. |

### 🎨 **UI/UX Highlights**
- **Dashboard Analytics:** Real-time stats and key metrics for both Super Admin and Company users.
- **Clean Navigation:** Intuitive sidebar menu system for easy access to all modules.
- **Interactive Tables:** Sortable, searchable, and paginated data tables for managing companies, users, files, etc.
- **Form Validation:** Robust client-side and server-side validation for all forms.
- **Modern Card-based Layout:** Professional and visually appealing design using cards and modals.

---

## 🛠️ **Tech Stack**

### **Backend**
| Technology | Purpose |
| :--- | :--- |
| **Framework** | Laravel 11 |
| **PHP Version** | 8.2+ |
| **Database** | MySQL 5.7+ |
| **Authentication** | Laravel Breeze |
| **API** | Laravel Sanctum |
| **Payments** | Stripe Integration |
| **Roles & Permissions** | Spatie Laravel Permission |
| **Queue/Jobs** | Laravel Queue (Database/Redis) |

### **Frontend**
| Technology | Purpose |
| :--- | :--- |
| **Templating** | Laravel Blade |
| **CSS Framework** | Bootstrap 5 |
| **Icons** | Font Awesome 6 |
| **JavaScript** | Vanilla ES6+ (with Blade integration) |
| **Charts** | Chart.js (for analytics dashboards) |
| **Build Tool** | Vite |

---

## 📁 **Project Structure**
saas-assessment/
├── app/
│ ├── Http/Controllers/
│ │ ├── SuperAdmin/ # All Super Admin controllers (Companies, Plans, Approvals)
│ │ ├── Company/ # All Company-level controllers (Dashboard, Team, Files, Subs)
│ │ ├── Auth/ # Authentication controllers (Login, Register, Verification)
│ │ └── API/ # API controllers (v1)
│ ├── Models/ # Eloquent models (User, Company, Plan, Subscription, File, etc.)
│ ├── Services/ # Business logic (StripeService, FileService, InvitationService)
│ ├── Providers/ # Service providers (including custom ones)
│ └── Middleware/ # Custom middleware (Tenant, Role, Subscription)
├── database/
│ ├── migrations/ # Database schema migrations
│ ├── seeders/ # Database seeders (Plans, Test Users)
│ └── factories/ # Model factories for testing
├── resources/views/
│ ├── layouts/ # Base Blade layout files
│ ├── super-admin/ # Views for Super Admin dashboard
│ ├── company/ # Views for Company dashboard
│ ├── auth/ # Authentication views (login, register)
│ └── components/ # Reusable Blade components
├── routes/
│ ├── web.php # Web routes (browser)
│ └── api.php # API routes
└── public/ # Public assets (CSS, JS, images)

text

---

## ⚡ **Quick Installation**

Get the platform up and running on your local machine in minutes.

### **Prerequisites**
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 5.7+
- Stripe Account (for payment testing)

### **Installation Steps**

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/amilokz/saas-assessment.git
    cd saas-assessment
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    npm run build
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Configure Environment (`.env`)**  
    Open the `.env` file and set your database, mail, and Stripe credentials.
    ```env
    APP_NAME="SaaS Assessment"
    APP_ENV=local
    APP_DEBUG=true
    APP_URL=http://localhost:8000

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=saas_assessment
    DB_USERNAME=root
    DB_PASSWORD=

    STRIPE_KEY=pk_test_your_stripe_publishable_key
    STRIPE_SECRET=sk_test_your_stripe_secret_key
    STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    MAIL_FROM_ADDRESS=noreply@saas-assessment.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```

5.  **Database Setup**
    ```bash
    php artisan migrate --seed
    php artisan storage:link
    ```

6.  **Set Permissions (For Linux/Mac)**
    ```bash
    chmod -R 775 storage bootstrap/cache
    # For production, you might need: chown -R www-data:www-data storage bootstrap/cache
    ```

7.  **Start the Development Server**
    ```bash
    php artisan serve
    ```
    Access the application at **http://localhost:8000**.

---

## 📊 **User Roles & Permissions**

The platform uses a robust role-based access control system.

### 1. **Super Admin (Platform Owner)**
- ✅ Approve/Reject new company registrations
- ✅ Create, edit, and manage subscription plans
- ✅ View all registered companies and their details
- ✅ Access comprehensive audit logs for the entire platform
- ✅ Suspend or activate any company
- ✅ Full system configuration access

### 2. **Company Admin (Company Owner)**
- ✅ Manage company profile and settings
- ✅ Purchase, upgrade, or cancel subscriptions
- ✅ Invite, manage, and remove team members
- ✅ Upload, download, and manage company files
- ✅ View audit logs specific to their company
- ✅ Create and manage support tickets

### 3. **Support User (Company Staff)**
- ✅ Reply to support messages
- ✅ Upload and delete files (if permitted by Company Admin)
- ✅ View all company files
- ✅ Basic dashboard access with limited analytics

### 4. **Normal User (Basic Access)**
- ✅ View and download files they have access to
- ✅ Send support messages
- ✅ View basic dashboard statistics

---

## 🔄 **Workflow Examples**

### **Company Registration Flow**
```mermaid
graph LR
    A[Register Company] --> B{7-Day Trial Starts};
    B --> C[Super Admin Approval];
    C -- Approved --> D[Company Activated];
    C -- Rejected --> E[Registration Rejected];
    D --> F[Subscribe to Plan];
A new company registers via the public form at /register/company.

An automatic 7-day trial begins, granting limited access.

The Super Admin reviews the request at /super-admin/companies.

Upon approval, the company is activated and can subscribe to a plan.

After subscribing, the company gains full access based on their chosen plan.

Team Management Flow
Company Admin navigates to /company/team.

Clicks "Invite Member" and enters the user's email and role (Admin/Support/Normal).

An invitation email is sent to the user with a secure link.

The user clicks the link, sets up their password, and account is activated.

The user now has role-based access within the company.

File Management Flow
Users access the file manager at /company/files.

Files can be uploaded via drag-and-drop or a file selector.

Storage usage is tracked against the plan's limits.

Files can be downloaded securely with access control checks.

Authorized users (Admin/Support) can delete files.

💳 Subscription Plans
Three tiered plans are available, with monthly and yearly billing options via Stripe.

Starter Plan
💰 $29/month | $299/year

Up to 5 users

1GB storage

100 file uploads/month

Basic email support

Standard features included

Professional Plan (Popular)
💰 $79/month | $799/year

Up to 20 users

5GB storage

500 file uploads/month

Priority support (24h response)

Advanced analytics

API access

Enterprise Plan
💰 $199/month | $1999/year

Up to 100 users

20GB storage

5000 file uploads/month

24/7 Premium support

Custom integrations

Dedicated account manager

🔐 Security Features
Security Layer	Implementation
Data Isolation	All database queries are automatically scoped to the current tenant (company) using Laravel's global scopes.
Role-based Access	Granular permission control using Spatie Laravel Permission. Middleware checks on every route.
Secure Authentication	Laravel Breeze provides secure login, registration, password reset, and email verification.
CSRF Protection	Enabled by default on all web routes.
SQL Injection Prevention	Laravel's Eloquent ORM uses parameter binding to prevent SQL injection.
XSS Protection	Blade's {{ }} syntax automatically escapes output, preventing XSS attacks.
Audit Logging	Comprehensive logging of critical actions (logins, approvals, payments, file deletions).
HTTPS Enforcement	Recommended for production to ensure all data in transit is encrypted.
📈 API Endpoints
The platform provides a RESTful API for integrations, secured with Laravel Sanctum.

Authentication
Method	Endpoint	Description
POST	/api/login	User login, returns API token.
POST	/api/register	User registration.
POST	/api/logout	User logout (revokes token).
GET	/api/user	Get authenticated user's information.
Company Management
Method	Endpoint	Description
POST	/api/companies	Register a new company.
GET	/api/companies	List companies (Super Admin only).
PUT	/api/companies/{id}	Update company details.
DELETE	/api/companies/{id}	Delete a company (Super Admin only).
File Management
Method	Endpoint	Description
GET	/api/files	List files for the authenticated user's company.
POST	/api/files	Upload a new file (multipart/form-data).
GET	/api/files/{id}	Get metadata for a specific file.
DELETE	/api/files/{id}	Delete a file.
GET	/api/files/{id}/download	Download a file (streams the file).
Team Management
Method	Endpoint	Description
GET	/api/team	List team members.
POST	/api/team/invite	Send a team invitation.
PUT	/api/team/{id}/role	Update a team member's role.
DELETE	/api/team/{id}	Remove a team member.
🚀 Deployment Guide
Follow these steps to deploy the platform to a production server.

Production Checklist
Set APP_ENV=production and APP_DEBUG=false in .env.

Configure a secure HTTPS (SSL certificate).

Set up a production-grade database (e.g., managed MySQL).

Configure a cache driver (Redis/Memcached).

Set up a queue driver (Redis/Database) and supervisor.

Configure a cron job for the task scheduler.

Set up regular database backups.

Configure error tracking (e.g., Sentry).

Deployment Script Example
Create a deploy.sh script for automated deployments.

bash
#!/bin/bash
echo "🚀 Starting deployment..."

# Pull latest changes
git pull origin main

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install and build frontend assets
npm install
npm run build

# Run database migrations
php artisan migrate --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart queue worker (if using supervisor)
# sudo supervisorctl restart laravel-worker:*

echo "✅ Deployment completed successfully!"
Essential Cron Jobs
Add these entries to your server's crontab (crontab -e).

bash
# Run Laravel task scheduler every minute
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Daily cleanup tasks (adjust times as needed)
0 0 * * * cd /path-to-your-project && php artisan trials:cleanup
0 1 * * * cd /path-to-your-project && php artisan invitations:cleanup
0 2 * * * cd /path-to-your-project && php artisan reports:generate
0 3 * * * cd /path-to-your-project && php artisan backup:run
🐛 Troubleshooting
Common Issue	Likely Solution
File Uploads Fail	Check storage permissions: sudo chown -R www-data:www-data storage and sudo chmod -R 775 storage.
404 on All Routes	Configure your web server (Nginx/Apache) to point to the /public directory.
Cache Issues	Clear all Laravel caches: php artisan optimize:clear.
Storage Link Missing	Run php artisan storage:link.
Queued Jobs Not Processing	Ensure the queue worker is running: php artisan queue:work (or via supervisor).
Migration Errors	If starting fresh: php artisan migrate:fresh --seed. For specific errors, check the migrations table.
🤝 Contributing
Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are greatly appreciated.

Fork the Project

Create your Feature Branch (git checkout -b feature/AmazingFeature)

Commit your Changes (git commit -m 'Add some AmazingFeature')

Push to the Branch (git push origin feature/AmazingFeature)

Open a Pull Request

Reporting Issues: Please use the GitHub Issues tab to report bugs or suggest features.

📄 License
Distributed under the MIT License. See LICENSE file for more information.

🙏 Acknowledgments
Laravel - The elegant PHP framework.

Bootstrap - For the responsive frontend framework.

Stripe - For seamless payment processing.

Spatie - For the excellent Laravel Permission package.

Font Awesome - For the comprehensive icon library.

All Contributors - Thanks to everyone who helps improve this project!

📞 Support & Contact
GitHub Issues: https://github.com/amilokz/saas-assessment/issues

Email: amilokz1@gmail.com

Documentation: https://github.com/amilokz/saas-assessment/wiki

Project Link: https://github.com/amilokz/saas-assessment

<div align="center"> <h3>⭐ Found this project helpful? Please consider giving it a star!</h3> <p>It helps others discover it and motivates further development.</p> <p> <a href="https://github.com/amilokz/saas-assessment"><img src="https://img.shields.io/github/stars/amilokz/saas-assessment?style=for-the-badge" alt="Star on GitHub"></a> </p> <p>Built with ❤️ by <a href="https://github.com/amilokz">amilokz</a></p> </div> ```
This comprehensive README is designed to be the perfect introduction to your project, helping users, contributors, and potential employers quickly understand its value, features, and how to get started. It's structured for clarity, uses visual elements like badges and tables, and provides all necessary technical details.

