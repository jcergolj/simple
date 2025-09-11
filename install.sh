#!/bin/bash

# Simple Time Tracking App Installation Script
# This script sets up the Laravel application after git clone

set -e  # Exit on any error

echo "🕐 Simple Time Tracking App - Installation Script"
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ] || [ ! -f "artisan" ]; then
    echo "❌ Error: This doesn't appear to be a Laravel project directory."
    echo "   Make sure you're in the project root directory."
    exit 1
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed or not in PATH."
    echo "   Please install PHP 8.2+ and try again."
    exit 1
fi

# Check PHP version (align with composer.json requirement)
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
REQUIRED_VERSION="8.2"

if [ "$(printf '%s\n' "$REQUIRED_VERSION" "$PHP_VERSION" | sort -V | head -n1)" != "$REQUIRED_VERSION" ]; then
    echo "❌ Error: PHP $REQUIRED_VERSION or higher is required. Found PHP $PHP_VERSION"
    exit 1
fi

echo "✅ PHP $PHP_VERSION detected"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer is not installed or not in PATH."
    echo "   Please install Composer and try again."
    exit 1
fi

echo "✅ Composer detected"

# Step 1: Install PHP dependencies
echo "📦 Installing PHP dependencies..."
if ! composer install --no-dev --optimize-autoloader; then
    echo "❌ Error: Failed to install PHP dependencies"
    exit 1
fi

# Step 2: Create environment file
echo "⚙️  Setting up environment..."
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅ Created .env file from .env.example"
    else
        echo "❌ Error: .env.example file not found"
        exit 1
    fi
else
    echo "ℹ️  .env file already exists, skipping..."
fi

# Step 3: Generate application key
echo "🔐 Generating application key..."
if ! php artisan key:generate --ansi --force; then
    echo "❌ Error: Failed to generate application key"
    exit 1
fi

# Step 4: Setup database
echo "💾 Setting up database..."

# Create database directory if it doesn't exist
mkdir -p database

# Check if SQLite database exists
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    echo "✅ Created SQLite database file"
fi

# Run migrations
echo "🗃️  Running database migrations..."
if ! php artisan migrate --force; then
    echo "❌ Error: Database migration failed"
    exit 1
fi

# Step 5: Setup frontend assets (importmap and tailwindcss)
echo "🎨 Setting up frontend assets..."
if ! php artisan importmap:install; then
    echo "❌ Error: Failed to setup importmap"
    exit 1
fi

if ! php artisan tailwindcss:download; then
    echo "❌ Error: Failed to download TailwindCSS"
    exit 1
fi

if ! php artisan tailwindcss:install; then
    echo "❌ Error: Failed to install TailwindCSS"
    exit 1
fi

echo "🔨 Building TailwindCSS..."
if ! php artisan tailwindcss:build; then
    echo "❌ Error: Failed to build TailwindCSS"
    exit 1
fi

# Step 6: Optimize application for production
echo "⚡ Optimizing application..."
if ! php artisan config:cache; then
    echo "❌ Error: Failed to cache config"
    exit 1
fi

if ! php artisan route:cache; then
    echo "❌ Error: Failed to cache routes"
    exit 1
fi

if ! php artisan view:cache; then
    echo "❌ Error: Failed to cache views"
    exit 1
fi

# Step 7: Set proper permissions (if on Unix-like system)
if [[ "$OSTYPE" == "linux-gnu"* ]] || [[ "$OSTYPE" == "darwin"* ]]; then
    echo "🔒 Setting proper permissions..."
    
    # Create necessary directories if they don't exist
    mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache
    
    # Set permissions
    chmod -R 755 storage bootstrap/cache
    
    # Set database permissions if it exists
    if [ -f "database/database.sqlite" ]; then
        chmod 644 database/database.sqlite
    fi
    
    echo "✅ Permissions set successfully"
fi

# Step 9: Final setup completion
echo ""
echo "🎉 Installation completed successfully!"
echo ""

# Check if user wants to create account now
read -p "Would you like to create your user account now? (y/N): " create_user
if [[ $create_user =~ ^[Yy]$ ]]; then
    echo ""
    echo "👤 Creating your user account..."
    if php artisan app:create-user; then
        echo ""
        echo "✅ User account created! You can now login to the application."
    else
        echo "❌ Failed to create user account. You can create it later."
    fi
else
    echo ""
    echo "📋 Next steps:"
    echo "1. Configure your .env file if needed (database, app settings)"
    echo "2. Create your first user by running:"
    echo "   php artisan app:create-user"
fi

echo ""
echo "🌟 Application ready!"
echo ""
echo "📚 Features:"
echo "   • Single user time tracking application"
echo "   • Client and project management"
echo "   • Real-time dashboard updates with Turbo"
echo "   • Hourly rate tracking and earnings calculation"
echo "   • No registration required after initial user creation"
echo ""
echo "🚀 Starting options:"
if [ -f "./frankenphp" ]; then
    echo "   • Production (FrankenPHP): ./frankenphp run"
fi
echo "   • Development (built-in server): php artisan serve"
echo ""
echo "Happy time tracking! 🕐✨"
