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
- **Frontend**: Laravel Blade, Tailwind CSS, Alpine.js
- **Database**: MySQL
- **Payment**: Stripe
- **Authentication**: Laravel Breeze
- **API**: Laravel Sanctum
- **Queue**: Redis
- **Caching**: Redis

## Installation

### Quick Install (Linux/Mac)

```bash
# Make the installation script executable
chmod +x install.sh

# Run the installation script
./install.sh
Manual Installation
Clone the repository

bash
git clone https://github.com/yourusername/saas-assessment.git
cd saas-assessment
Install dependencies

bash
composer install
npm install
npm run build
Configure environment

bash
cp .env.example .env
php artisan key:generate
Update .env file with your database credentials

Run migrations and seeders

bash
php artisan migrate --force
php artisan db:seed
Set permissions

bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
Create storage link

bash
php artisan storage:link
Development
Using Docker
bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Run seeders
docker-compose exec app php artisan db:seed

# Access the application
open http://localhost:8000
Local Development
bash
# Start Laravel development server
php artisan serve

# Start Vite for assets
npm run dev

# Run queue worker (in separate terminal)
php artisan queue:work
Testing
bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter CompanyRegistrationTest

# Generate test data
php artisan test-data:generate --count=10
Deployment
Production Checklist
Environment Setup

Use .env.production as base

Set APP_ENV=production

Set APP_DEBUG=false

Generate new APP_KEY

Security

Enable HTTPS

Set secure session cookie options

Configure CORS properly

Set up firewall rules

Performance

Configure Redis for cache and sessions

Set up queue workers

Configure OPcache

Enable database query caching

Monitoring

Set up error tracking (Sentry/Bugsnag)

Configure log rotation

Set up server monitoring

Deployment Script
bash
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
API Documentation
The platform provides REST API endpoints for all major features. API documentation is available at /api/docs (when configured).

Authentication
Use Bearer tokens for API authentication:

http
Authorization: Bearer {api_token}
Main Endpoints
POST /api/register/company - Register a new company

GET /api/companies/{id} - Get company details

GET /api/subscriptions/plans - List available plans

POST /api/subscriptions - Create subscription

GET /api/team - List team members

POST /api/team/invite - Invite team member

GET /api/files - List files

POST /api/files - Upload file

GET /api/support - List support tickets

POST /api/support - Create support ticket

User Roles
Super Admin - Platform owner with full access

Company Admin - Full company access, can manage team and subscriptions

Support User - Can reply to support messages and manage files

Normal User - Basic access to view files and send support messages

Default Credentials
After seeding:

Super Admin: superadmin@example.com / password123

Company Admin (for registered companies): Uses registered email

Cron Jobs
Set up the following cron jobs:

bash
# Run every minute
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Cleanup expired trials (daily)
0 0 * * * cd /path-to-your-project && php artisan trials:cleanup

# Cleanup expired invitations (daily)
0 1 * * * cd /path-to-your-project && php artisan invitations:cleanup
Troubleshooting
Common Issues
File upload permissions

bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
Queue not processing

bash
# Start queue worker
php artisan queue:work --tries=3

# Or use supervisor
sudo supervisorctl restart all
Storage link not working

bash
php artisan storage:link
Cache issues

bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
Getting Help
Check Laravel logs: storage/logs/laravel.log

Enable debug mode temporarily in .env

Check server error logs

License
This project is licensed under the MIT License - see the LICENSE file for details.

Acknowledgments
Laravel - The PHP framework

Stripe - Payment processing

Tailwind CSS - CSS framework

Alpine.js - JavaScript framework

text

## **19. Complete Application Setup:**

### **Final Steps to Run the Application:**

1. **Run the complete setup:**
```bash
# Make installation script executable
chmod +x install.sh

# Run installation
./install.sh
Or manually:

bash
# Install dependencies
composer install
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Update .env with your database credentials
# DB_DATABASE=saas_assessment
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Start development server
php artisan serve

# In another terminal, start Vite
npm run dev
Access the application:

Website: http://localhost:8000

Super Admin Login: superadmin@example.com / password123

Company Registration: http://localhost:8000/register/company

✅ Project Complete!
You now have a fully functional multi-tenant SaaS platform with:

Core Features Implemented:
✅ Company registration with trial system

✅ Super admin approval workflow

✅ Subscription management with Stripe

✅ Team management with invitations

✅ File storage with quotas

✅ Support messaging system

✅ Comprehensive audit logging

✅ Role-based access control

✅ REST API endpoints

✅ Responsive UI with Tailwind CSS

Production Ready:
Docker configuration

Deployment scripts

Security best practices

Testing suite

Email templates

Cron jobs for maintenance

API documentation

To Test the Application:
Register a new company (goes to trial)

Login as super admin to approve companies

Test subscription flow (use Stripe test cards)

Invite team members

Upload files with storage limits

Create support tickets

View audit logs