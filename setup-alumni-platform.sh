#!/bin/bash

echo "🚀 Appiah Kubi Alumni Platform Setup"
echo "======================================"

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: This doesn't appear to be a Laravel project directory."
    echo "Please run this script from your Laravel project root."
    exit 1
fi

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Publish vendor files
echo "📄 Publishing vendor configurations..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Run migrations
echo "🗃️ Running database migrations..."
php artisan migrate:fresh

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed

# Create storage link
echo "📁 Creating storage link..."
php artisan storage:link

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Generate IDE helper
echo "🔧 Generating IDE helper..."
php artisan ide-helper:generate
php artisan ide-helper:models --nowrite
php artisan ide-helper:meta

echo ""
echo "✅ Setup complete!"
echo ""
echo "🎉 Appiah Kubi Alumni Platform is ready!"
echo ""
echo "📝 Default Login Credentials:"
echo "   Admin: admin@appiahkubi.edu.gh / admin123"
echo "   Sample Alumni: Check the seeders for credentials"
echo ""
echo "🚀 To start the development server:"
echo "   php artisan serve"
echo ""
echo "📖 Don't forget to:"
echo "   1. Configure your .env file with database credentials"
echo "   2. Set up your mail configuration"
echo "   3. Configure payment gateway (Razorpay) if needed"
echo "   4. Set up queue workers for background jobs"
echo ""
echo "Happy coding! 🎓"
