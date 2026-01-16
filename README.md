SaaS Assessment Platform
A complete, production-ready multi-tenant SaaS platform built with Laravel. Features company approval system, trial management, subscription billing, team collaboration, file storage, and comprehensive audit logging.

🌐 Live Demo
Live URL: https://saas-assessment.wuaze.com

GitHub Repository: https://github.com/amilokz/saas-assessment

📋 Demo Credentials
Super Admin (Platform Owner)
Email: admin@saas.test

Password: password123

Dashboard: https://saas-assessment.wuaze.com/super-admin/dashboard

Access: Full system control, company approvals, plan management

Company Admin (Sample Company)
Email: admin@newcompany.com

Password: password

Dashboard: https://saas-assessment.wuaze.com/company/dashboard

Access: Company dashboard, team management, file uploads, subscriptions

🚀 Features
✅ Core Functionality
Multi-tenant Architecture - Complete data isolation between companies

Company Registration System - Public registration with automatic trial

Approval Workflow - Super admin approves/rejects companies

Subscription Management - Multiple plans with Stripe integration

Team Collaboration - 4-tier role system with invitations

File Storage - Secure, company-isolated file management

Support System - Built-in ticketing system

Audit Logging - Comprehensive activity tracking

Professional UI - Modern, responsive Bootstrap 5 interface

🎨 UI/UX Highlights
Dashboard Analytics - Real-time stats and metrics

Clean Navigation - Intuitive sidebar menu system

Responsive Design - Mobile-friendly interface

Professional Cards - Modern card-based layout

Interactive Tables - Sortable, searchable data tables

Form Validation - Client and server-side validation

🛠️ Tech Stack
Backend
Framework: Laravel 11

PHP Version: 8.2+

Database: MySQL 5.7+

Authentication: Laravel Breeze

API: Laravel Sanctum

Payments: Stripe Integration

Roles: Spatie Laravel Permission

Frontend
Templating: Laravel Blade

CSS Framework: Bootstrap 5

Icons: Font Awesome 6

JavaScript: Vanilla ES6+

Charts: Chart.js (for analytics)

📁 Project Structure
text
saas-assessment/
├── app/
│   ├── Http/Controllers/
│   │   ├── SuperAdmin/        # Super Admin controllers
│   │   ├── Company/          # Company controllers
│   │   ├── Auth/             # Authentication
│   │   └── API/              # API endpoints
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic
│   ├── Providers/            # Service providers
│   └── Middleware/           # Custom middleware
├── database/
│   ├── migrations/           # Database schema
│   ├── seeders/             # Test data
│   └── factories/           # Model factories
├── resources/views/
│   ├── layouts/             # Base layouts
│   ├── super-admin/         # Super Admin views
│   ├── company/             # Company views
│   ├── auth/                # Auth views
│   └── components/          # Reusable components
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
└── public/                  # Public assets
🚀 Quick Installation
1. Clone Repository
bash
git clone https://github.com/amilokz/saas-assessment.git
cd saas-assessment
2. Install Dependencies
bash
composer install
npm install
npm run build
3. Environment Setup
bash
cp .env.example .env
php artisan key:generate
4. Configure Environment (.env)
env
APP_NAME="SaaS Assessment"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_assessment
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=pk_test_your_key
STRIPE_SECRET=sk_test_your_secret
STRIPE_WEBHOOK_SECRET=whsec_your_webhook

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@saas-assessment.com
MAIL_FROM_NAME="SaaS Platform"
5. Database Setup
bash
php artisan migrate --seed
php artisan storage:link
6. Permissions
bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
7. Start Development Server
bash
php artisan serve
# Access at: http://localhost:8000
📊 User Roles & Permissions
1. Super Admin (Platform Owner)
✅ Approve/Reject company registrations

✅ Manage subscription plans

✅ View all companies and audit logs

✅ Suspend/Activate companies

✅ Full system configuration access

2. Company Admin (Company Owner)
✅ Manage company profile and settings

✅ Purchase and manage subscriptions

✅ Invite and manage team members

✅ Upload and manage files

✅ View company audit logs

✅ Create support tickets

3. Support User (Company Staff)
✅ Reply to support messages

✅ Upload and delete files

✅ View company files

✅ Basic dashboard access

4. Normal User (Basic Access)
✅ View files

✅ Send support messages

✅ Basic dashboard view

🔄 Workflow Examples
Company Registration Flow
Register Company → /register/company

7-Day Trial Starts → Automatic trial with limitations

Super Admin Approval → /super-admin/companies

Company Activated → Full access granted

Subscribe to Plan → /company/subscription

Team Management Flow
Company Admin → /company/team

Invite Team Member → Enter email and role

User Accepts Invitation → Email invitation link

Account Activated → Role-based access granted

Manage Roles → Update or remove team members

File Management Flow
Access Files → /company/files

Upload Files → Drag & drop or file selection

Manage Storage → View usage against limits

Download Files → Secure file access

Delete Files → Admin/support users only

💳 Subscription Plans
Starter Plan ($29/month | $299/year)
Up to 5 users

1GB storage

100 file uploads

Basic email support

Standard features

Professional Plan ($79/month | $799/year)
Up to 20 users

5GB storage

500 file uploads

Priority support

Advanced analytics

API access

Enterprise Plan ($199/month | $1999/year)
Up to 100 users

20GB storage

5000 file uploads

24/7 premium support

Custom integrations

Dedicated account manager

🔐 Security Features
Data Isolation: Company-level data segregation

Role-based Access: Granular permission control

Secure Authentication: Laravel Breeze with validation

CSRF Protection: Built-in Laravel protection

SQL Injection Prevention: Eloquent ORM

XSS Protection: Blade templating engine

Audit Logging: Comprehensive activity tracking

HTTPS Enforcement: Secure connections

📈 API Endpoints
Authentication
http
POST   /api/login              # User login
POST   /api/register           # User registration
POST   /api/logout             # User logout
GET    /api/user               # Get user info
Company Management
http
POST   /api/companies          # Register company
GET    /api/companies          # List companies (Super Admin)
PUT    /api/companies/{id}     # Update company
DELETE /api/companies/{id}     # Delete company
File Management
http
GET    /api/files              # List files
POST   /api/files              # Upload file
GET    /api/files/{id}         # Get file details
DELETE /api/files/{id}         # Delete file
GET    /api/files/{id}/download # Download file
Team Management
http
GET    /api/team               # List team members
POST   /api/team/invite        # Invite team member
PUT    /api/team/{id}/role     # Update user role
DELETE /api/team/{id}          # Remove team member
🚀 Deployment
Production Checklist
Environment: Set APP_ENV=production

Debug Mode: Set APP_DEBUG=false

HTTPS: Configure SSL certificate

Cache: Enable Redis/OPcache

Queue: Set up supervisor for queues

Backup: Configure database backups

Monitoring: Set up error tracking

Deployment Script
bash
#!/bin/bash
# Deployment script

# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart services
sudo systemctl restart php-fpm
sudo systemctl restart nginx

echo "✅ Deployment completed successfully!"
Cron Jobs
bash
# Run scheduler every minute
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# Cleanup expired trials (daily)
0 0 * * * cd /path-to-project && php artisan trials:cleanup

# Cleanup expired invitations
0 1 * * * cd /path-to-project && php artisan invitations:cleanup

# Generate reports
0 2 * * * cd /path-to-project && php artisan reports:generate

# Backup database
0 3 * * * cd /path-to-project && php artisan backup:run
🐛 Troubleshooting
Common Issues & Solutions
File Upload Permissions:

bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
Cache Issues:

bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
Storage Link:

bash
php artisan storage:link
Queue Workers:

bash
php artisan queue:work --tries=3
# Or use supervisor
sudo supervisorctl restart all
Migration Errors:

bash
php artisan migrate:fresh --seed
# Or reset
php artisan migrate:reset
php artisan migrate
📞 Support & Contact
GitHub Issues: https://github.com/amilokz/saas-assessment/issues

Email: amilokz1@gmail.com

Documentation: https://github.com/amilokz/saas-assessment/wiki

📄 License
This project is licensed under the MIT License - see the LICENSE file for details.

🙏 Acknowledgments
Laravel - The PHP framework for web artisans

Bootstrap - Popular CSS framework

Stripe - Payment processing

Spatie - Laravel Permission package

Font Awesome - Icon library

All Contributors - Thanks for your support!

🎯 Project Status
✅ COMPLETE & PRODUCTION-READY
Last Updated: January 2026
Version: 1.0.0
Status: ✅ LIVE & FUNCTIONAL

Live Features Verified:
✅ Super Admin Dashboard with professional UI

✅ Company Admin Dashboard with all features

✅ Company Approval/Rejection system

✅ Subscription Plan Management

✅ File Upload and Management

✅ Team Invitation System

✅ Support Ticket System

✅ Audit Logging

✅ Responsive Design

✅ Secure Authentication

✅ Stripe Payment Integration

Live Demo: https://saas-assessment.wuaze.com
GitHub Repo: https://github.com/amilokz/saas-assessment

⭐ Star the repository if you find this project useful!
🐛 Report issues to help improve the platform!
🔀 Fork and customize for your own SaaS needs!
