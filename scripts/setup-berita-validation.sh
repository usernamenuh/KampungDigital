#!/bin/bash

echo "🔧 Setting up Berita system with enhanced validation..."

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/app/public/berita/images
mkdir -p storage/app/public/berita/videos
chmod -R 755 storage/app/public/berita

# Create storage link if not exists
if [ ! -L public/storage ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize for production
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache

# Check if berita table exists
echo "🔍 Checking berita table..."
php artisan tinker --execute="
try {
    \$count = \App\Models\Berita::count();
    echo 'Berita table exists with ' . \$count . ' records\n';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\n';
}
"

# Test file permissions
echo "📝 Testing file permissions..."
if [ -w storage/app/public/berita/images ]; then
    echo "✅ Images directory is writable"
else
    echo "❌ Images directory is not writable"
    chmod -R 755 storage/app/public/berita/images
fi

if [ -w storage/app/public/berita/videos ]; then
    echo "✅ Videos directory is writable"
else
    echo "❌ Videos directory is not writable"
    chmod -R 755 storage/app/public/berita/videos
fi

echo "✅ Berita system setup completed!"
echo ""
echo "📋 Next steps:"
echo "1. Make sure your user has role: admin, kades, rw, or rt"
echo "2. Check that RT/RW data exists in database"
echo "3. Try creating a berita through the web interface"
echo ""
echo "🐛 If you still have issues, check:"
echo "- Laravel logs: storage/logs/laravel.log"
echo "- Web server error logs"
echo "- Database connection"
