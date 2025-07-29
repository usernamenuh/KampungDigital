#!/bin/bash

echo "🔧 Fixing Berita Routes and Permissions..."

# Clear all caches
echo "📦 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoload
echo "🔄 Regenerating autoload..."
composer dump-autoload

# Check if BeritaRequest exists
echo "🔍 Checking BeritaRequest class..."
if php artisan tinker --execute="echo class_exists('App\\Http\\Requests\\BeritaRequest') ? 'EXISTS' : 'NOT_FOUND';" | grep -q "EXISTS"; then
    echo "✅ BeritaRequest class found"
else
    echo "❌ BeritaRequest class not found"
fi

# Check routes
echo "🛣️ Checking berita routes..."
php artisan route:list --name=berita

# Check storage permissions
echo "📁 Checking storage permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Create storage directories
echo "📂 Creating storage directories..."
mkdir -p storage/app/public/berita/images
mkdir -p storage/app/public/berita/videos
chmod -R 755 storage/app/public/berita/

# Create symbolic link if not exists
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Test database connection
echo "🗄️ Testing database connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'DB_OK'; } catch(Exception \$e) { echo 'DB_ERROR: ' . \$e->getMessage(); }"

# Check beritas table
echo "📋 Checking beritas table..."
php artisan tinker --execute="
try {
    \$columns = DB::select('DESCRIBE beritas');
    echo 'TABLE_EXISTS: ' . count(\$columns) . ' columns';
    foreach(\$columns as \$col) {
        echo \$col->Field . ' ';
    }
} catch(Exception \$e) {
    echo 'TABLE_ERROR: ' . \$e->getMessage();
}
"

echo "✅ Berita routes fix completed!"
