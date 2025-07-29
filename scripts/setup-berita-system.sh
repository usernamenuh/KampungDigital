#!/bin/bash

echo "🚀 Setting up Berita System..."

# Create storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/app/public/berita/images
mkdir -p storage/app/public/berita/videos
chmod -R 755 storage/app/public/berita

# Create storage link if not exists
echo "🔗 Creating storage link..."
if [ ! -L public/storage ]; then
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
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Berita system setup completed!"
echo ""
echo "📝 Next steps:"
echo "1. Make sure your user has the correct role (admin, kades, rw, or rt)"
echo "2. Access /berita to view the berita management"
echo "3. Click 'Tambah Berita' to create a new article"
echo ""
echo "🔧 If you still have issues, check:"
echo "- Database connection"
echo "- User permissions"
echo "- Storage permissions"
