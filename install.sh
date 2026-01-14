#!/bin/bash

echo "🚀 Starting SaaS Platform Installation..."

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer."
    exit 1
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 16 or higher."
    exit 1
fi

echo "✅ Prerequisites checked."

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# Build assets
echo "🔨 Building assets..."
npm run build

# Copy environment file
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Ask for database configuration
echo "💾 Database Setup"
read -p "Database name (default: saas_assessment): " db_name
db_name=${db_name:-saas_assessment}

read -p "Database username (default: root): " db_user
db_user=${db_user:-root}

read -p "Database password: " db_pass

# Update .env file
echo "Updating .env file..."
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Run seeders
echo "🌱 Seeding database..."
php artisan db:seed

# Set storage permissions
echo "🔧 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate storage link
echo "📂 Creating storage link..."
php artisan storage:link

# Clear cache
echo "🧹 Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "✅ Installation complete!"
echo ""
echo "📋 Next steps:"
echo "1. Configure your web server (nginx/apache) to point to the public directory"
echo "2. Set up SSL certificate for production"
echo "3. Configure cron jobs:"
echo "   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1"
echo "4. Set up queue worker: php artisan queue:work"
echo ""
echo "🔑 Super Admin Credentials:"
echo "   Email: superadmin@example.com"
echo "   Password: password123"
echo ""
echo "🌐 Access your application at: http://localhost:8000"