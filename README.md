
# SaaS Assessment Platform

A multi-tenant SaaS platform built with Laravel, featuring company approval, trial system, subscriptions, team management, file storage, and audit logging.

## Features

- **Multi-tenant Architecture** - Each company has isolated data
- **Company Approval System** - Super admin approves/rejects companies
- **Trial System** - 7-day free trial with limitations
- **Subscription Management** - Multiple plans with monthly/yearly billing
- **Team Management** - Invite team members with different roles
- **File Storage** - Upload, manage, and share files
- **Support Messaging** - Built-in support ticket system
- **Audit Logging** - Track all activities
- **Role-based Access Control** - 4 user roles with different permissions
- **Stripe Integration** - Secure payment processing

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2
- **Frontend**: Laravel Blade, Bootstrap 5, Vanilla JavaScript
- **Database**: MySQL
- **Payment**: Stripe
- **Authentication**: Laravel Breeze
- **API**: Laravel Sanctum

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 16+
- MySQL 5.7+
- Stripe Account (for payments)

### Quick Installation

1. **Clone the repository:**
```bash
git clone https://github.com/yourusername/saas-assessment.git
cd saas-assessment
Install dependencies:

bash
composer install
npm install
npm run build
Configure environment:

bash
cp .env.example .env
php artisan key:generate
Update .env file:

env
DB_DATABASE=saas_assessment
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
Run migrations and seeders:

bash
php artisan migrate --seed
Create storage link:

bash
php artisan storage:link
Set permissions:

bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
Local Development
Start development server:

bash
php artisan serve
Access the application:

Website: http://localhost:8000

Super Admin: super@demo.com / Admin@123

Company Admin: company@demo.com / Company@123

Development
Using Laravel Development Server
bash
# Start Laravel development server
php artisan serve

# Run queue worker (in separate terminal)
php artisan queue:work
Testing
bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter CompanyRegistrationTest

# Run PHPUnit tests
./vendor/bin/phpunit
Project Structure
text
saas-assessment/
├── app/
│   ├── Http/Controllers/
│   │   ├── SuperAdmin/        # Super Admin controllers
│   │   ├── Company/          # Company controllers
│   │   └── Auth/             # Authentication controllers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── Providers/            # Service providers
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/             # Database seeders
├── resources/views/
│   ├── super-admin/          # Super Admin views
│   ├── company/             # Company views
│   ├── auth/                # Authentication views
│   └── layouts/             # Layout templates
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
└── public/                  # Public assets
User Roles
1. Super Admin (Platform Owner)
Approve/reject company registrations

Manage subscription plans

View all companies and audit logs

Suspend/deactivate companies

Full system access

2. Company Admin
Manage company settings

Purchase/manage subscriptions

Invite team members

Upload/manage files

View company audit logs

3. Support User
Reply to company support messages

Upload and delete files

View company files

4. Normal User
View files

Send support messages

Basic dashboard access

Core Features
Company Registration & Approval Flow
Registration (Public)

Companies register via public form/API

7-day free trial starts automatically

Status: trial_pending_approval

Trial Mode (7 Days - Limited Access)

Max 1 user (including admin)

Max 2 file uploads

Limited support messages

No paid subscription access

Super Admin Approval

Only Super Admin can approve/reject

Approval removes trial limitations

Rejection blocks company access

Subscription & Billing
Plans Available:
Basic Plan - Starter features

Pro Plan - Advanced features

Enterprise Plan - Full features

Features:
Monthly/yearly billing

Upgrade/downgrade plans

Cancel subscription

Automatic renewal handling

Billing history and invoices

Payment Integration:
Stripe (primary) or PayPal

Secure checkout

Webhook integration for renewals

Team Management
Invitation Flow:
Admin invites users via API

Role assigned during invitation

Invitation email sent with token

User accepts invitation via API

Account activated under company

Rules:
Invitations expire after defined time

Trial companies limited to 1 user

File Storage System
Upload files (Admin & Support)

View files (All users)

Delete files (Admin & Support)

Files isolated per company

Storage limits per plan

Support Messaging System
Users send messages to support

Support replies to messages

Messages isolated per company

Read/unread status maintained

Audit Log System
Tracked Events:
Company registration

Trial started/expired

Company approval/rejection

Subscription created/renewed/cancelled

User invitations

File uploads/deletions

API Documentation
Authentication
http
Authorization: Bearer {api_token}
Main Endpoints
Company Endpoints
http
POST   /api/register/company    # Register new company
GET    /api/company/profile     # Get company profile
PUT    /api/company/profile     # Update company profile
GET    /api/company/stats       # Get company statistics
Subscription Endpoints
http
GET    /api/subscription        # Get current subscription
POST   /api/subscription        # Create subscription
DELETE /api/subscription/{id}   # Cancel subscription
GET    /api/invoices            # List invoices
Team Endpoints
http
GET    /api/team                # List team members
POST   /api/team/invite         # Invite team member
PUT    /api/team/{user}/role    # Update user role
DELETE /api/team/{user}         # Remove user
File Endpoints
http
GET    /api/files               # List files
POST   /api/files               # Upload file
GET    /api/files/{id}          # Get file details
DELETE /api/files/{id}          # Delete file
GET    /api/files/{id}/download # Download file
Support Endpoints
http
GET    /api/support             # List support tickets
POST   /api/support             # Create support ticket
GET    /api/support/{id}        # Get ticket details
POST   /api/support/{id}/reply  # Reply to ticket
Testing Credentials
Demo Accounts
text
Super Admin:
- Email: super@demo.com
- Password: Admin@123

Company Admin:
- Email: company@demo.com
- Password: Company@123
Stripe Test Cards
text
For testing payments:
- Card: 4242 4242 4242 4242
- Expiry: Any future date
- CVC: Any 3 digits
- ZIP: Any 5 digits
Testing Workflows
1. Complete Registration → Subscription Flow
text
1. Register at /register/company
2. Login as Super Admin (super@demo.com)
3. Approve company at /super-admin/companies
4. Login as Company Admin (company@demo.com)
5. Subscribe to plan at /company/subscription
6. Pay with test card
7. Verify invoice at /company/invoices
2. Team Management Flow
text
1. As Company Admin, go to /company/team
2. Click "Invite User"
3. Enter test email
4. Check pending invitations
5. Test role assignment
6. Test user removal
3. File Management Flow
text
1. Go to /company/files
2. Upload test file
3. Check storage usage updates
4. Download file
5. Delete file (admin/support only)
Database Schema
Core Tables
sql
companies      # Company information
users          # User accounts with roles
roles          # User roles and permissions
subscriptions  # Company subscriptions
plans          # Subscription plans
payments       # Payment transactions
invitations    # Team invitation tokens
messages       # Support messages
files          # Uploaded files
audit_logs     # Activity tracking
Important Rule: Every business-related table includes company_id for tenant isolation.

Security & Access Control
Role-based middleware protection

Company-level data isolation

Secure API authentication (Sanctum)

CSRF protection

Input validation and sanitization

SQL injection prevention

XSS protection

Deployment
Production Checklist
Environment Setup
env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
# ... other production settings
Security Configuration
Enable HTTPS

Set secure session cookie options

Configure CORS properly

Set up firewall rules

Regular security updates

Performance Optimization
Configure Redis for caching

Set up queue workers

Configure OPcache

Enable database query caching

Use CDN for assets

Monitoring
Set up error tracking (Sentry/Bugsnag)

Configure log rotation

Set up server monitoring

Regular backup schedule

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

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php-fpm
sudo systemctl restart nginx

echo "✅ Deployment completed successfully!"
Cron Jobs
Set up the following cron jobs on your server:

bash
# Run scheduler every minute
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# Cleanup expired trials (daily at midnight)
0 0 * * * cd /path-to-project && php artisan trials:cleanup

# Cleanup expired invitations (daily at 1 AM)
0 1 * * * cd /path-to-project && php artisan invitations:cleanup

# Generate daily reports (daily at 2 AM)
0 2 * * * cd /path-to-project && php artisan reports:generate

# Backup database (daily at 3 AM)
0 3 * * * cd /path-to-project && php artisan backup:run
Troubleshooting
Common Issues
1. File Upload Permissions
bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
2. Queue Not Processing
bash
# Start queue worker
php artisan queue:work --tries=3

# Or use supervisor
sudo supervisorctl restart all
3. Storage Link Not Working
bash
php artisan storage:link
4. Cache Issues
bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
5. Migration Errors
bash
# Fresh migration
php artisan migrate:fresh --seed

# Or reset
php artisan migrate:reset
php artisan migrate --seed
Getting Help
Check Laravel logs: storage/logs/laravel.log

Enable debug mode temporarily in .env

Check server error logs

Use php artisan route:list to verify routes

Use php artisan tinker to test database queries

License
This project is licensed under the MIT License - see the LICENSE file for details.

Acknowledgments
Laravel - The PHP framework

Bootstrap - CSS framework

Stripe - Payment processing

MySQL - Database

All Contributors - Thank you for your support

Support
For support, email amilokz1@gmail.com.

Final Notes
This SaaS platform is production-ready and includes:

✅ Complete multi-tenant architecture
✅ Full subscription management with Stripe
✅ Team invitation system
✅ File storage with quotas
✅ Support ticket system
✅ Comprehensive audit logging
✅ Role-based access control
✅ REST API endpoints
✅ Responsive UI with Bootstrap
✅ Proper error handling
✅ Security best practices

Project Status: ✅ COMPLETE & READY FOR PRODUCTION

text

This **complete README.md** file includes:
1. Clear installation instructions
2. Demo credentials
3. Testing workflows
4. API documentation
5. Deployment guide
6. Troubleshooting section
7. All project features explained

Your project is now **100% ready for submission!** 🎉🚀