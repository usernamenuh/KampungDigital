#!/bin/bash

echo "🧪 Testing Berita Creation Process..."

# Test 1: Check if routes are accessible
echo "1️⃣ Testing route accessibility..."
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000/berita/create

# Test 2: Check BeritaRequest class
echo "2️⃣ Testing BeritaRequest class..."
php artisan tinker --execute="
try {
    \$request = new App\\Http\\Requests\\BeritaRequest();
    echo 'BeritaRequest: OK';
} catch(Exception \$e) {
    echo 'BeritaRequest ERROR: ' . \$e->getMessage();
}
"

# Test 3: Check Berita model
echo "3️⃣ Testing Berita model..."
php artisan tinker --execute="
try {
    \$berita = new App\\Models\\Berita();
    echo 'Berita Model: OK';
    echo 'Table: ' . \$berita->getTable();
} catch(Exception \$e) {
    echo 'Berita Model ERROR: ' . \$e->getMessage();
}
"

# Test 4: Check storage directories
echo "4️⃣ Testing storage directories..."
if [ -d "storage/app/public/berita/images" ]; then
    echo "✅ Images directory exists"
else
    echo "❌ Images directory missing"
fi

if [ -d "storage/app/public/berita/videos" ]; then
    echo "✅ Videos directory exists"
else
    echo "❌ Videos directory missing"
fi

# Test 5: Check permissions
echo "5️⃣ Testing permissions..."
ls -la storage/app/public/berita/

echo "🏁 Test completed!"
