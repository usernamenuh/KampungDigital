#!/bin/bash

echo "🔧 Fixing BeritaRequest class and autoloading..."

# Clear all caches first
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Dump autoload to ensure new classes are loaded
echo "🔄 Regenerating autoload files..."
composer dump-autoload

# Clear compiled files
echo "🗑️ Clearing compiled files..."
php artisan clear-compiled

# Optimize autoloader
echo "⚡ Optimizing autoloader..."
composer dump-autoload --optimize

# Test if BeritaRequest class exists
echo "🔍 Testing BeritaRequest class..."
php artisan tinker --execute="
try {
    \$request = new \App\Http\Requests\BeritaRequest();
    echo 'BeritaRequest class loaded successfully\n';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\n';
}
"

# Check if all required directories exist
echo "📁 Checking directories..."
mkdir -p app/Http/Requests
mkdir -p storage/app/public/berita/images
mkdir -p storage/app/public/berita/videos

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage/app/public/berita
chmod 644 app/Http/Requests/BeritaRequest.php

# Create storage link if not exists
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Test basic validation
echo "✅ Testing validation rules..."
php artisan tinker --execute="
try {
    \$rules = (new \App\Http\Requests\BeritaRequest())->rules();
    echo 'Validation rules loaded: ' . count(\$rules) . ' rules\n';
} catch (Exception \$e) {
    echo 'Validation error: ' . \$e->getMessage() . '\n';
}
"

echo "✅ BeritaRequest fix completed!"
echo ""
echo "📋 Next steps:"
echo "1. Try creating a berita again"
echo "2. Check Laravel logs if still failing: tail -f storage/logs/laravel.log"
echo "3. Make sure form fields match validation rules"
