#!/bin/bash

echo "🔍 Debugging Berita validation system..."

# Check current user and role
echo "👤 Checking current user..."
php artisan tinker --execute="
\$user = \App\Models\User::first();
if (\$user) {
    echo 'User: ' . \$user->name . ' (Role: ' . \$user->role . ')\n';
    if (\$user->penduduk) {
        echo 'Has penduduk data: Yes\n';
        if (\$user->penduduk->rtKetua) {
            echo 'RT Ketua: RT ' . \$user->penduduk->rtKetua->no_rt . '\n';
        }
        if (\$user->penduduk->rwKetua) {
            echo 'RW Ketua: RW ' . \$user->penduduk->rwKetua->no_rw . '\n';
        }
    } else {
        echo 'Has penduduk data: No\n';
    }
} else {
    echo 'No users found\n';
}
"

# Check RT/RW data
echo ""
echo "🏘️ Checking RT/RW data..."
php artisan tinker --execute="
\$rtCount = \App\Models\Rt::count();
\$rwCount = \App\Models\Rw::count();
echo 'RT count: ' . \$rtCount . '\n';
echo 'RW count: ' . \$rwCount . '\n';

if (\$rtCount > 0) {
    \$rt = \App\Models\Rt::with('rw')->first();
    echo 'Sample RT: ' . \$rt->no_rt . ' (RW: ' . \$rt->rw->no_rw . ')\n';
}
"

# Check berita table structure
echo ""
echo "🗄️ Checking berita table structure..."
php artisan tinker --execute="
try {
    \$columns = \Illuminate\Support\Facades\Schema::getColumnListing('beritas');
    echo 'Berita table columns: ' . implode(', ', \$columns) . '\n';
} catch (Exception \$e) {
    echo 'Error checking table: ' . \$e->getMessage() . '\n';
}
"

# Test validation rules
echo ""
echo "✅ Testing validation rules..."
php artisan tinker --execute="
\$request = new \App\Http\Requests\BeritaRequest();
\$rules = \$request->rules();
echo 'Validation rules loaded: ' . count(\$rules) . ' rules\n';
foreach (['judul', 'konten', 'kategori', 'tingkat_akses'] as \$field) {
    if (isset(\$rules[\$field])) {
        echo \$field . ': ' . implode('|', \$rules[\$field]) . '\n';
    }
}
"

# Check storage permissions
echo ""
echo "📁 Checking storage permissions..."
for dir in "storage/app/public/berita" "storage/app/public/berita/images" "storage/app/public/berita/videos"; do
    if [ -d "$dir" ]; then
        perms=$(stat -c "%a" "$dir" 2>/dev/null || stat -f "%A" "$dir" 2>/dev/null)
        echo "$dir: $perms"
    else
        echo "$dir: Directory not found"
    fi
done

# Check recent logs
echo ""
echo "📋 Recent Laravel logs (last 10 lines)..."
if [ -f "storage/logs/laravel.log" ]; then
    tail -10 storage/logs/laravel.log
else
    echo "No Laravel log file found"
fi

echo ""
echo "🔧 Debug completed!"
