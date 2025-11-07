#!/bin/bash

echo "🚀 Starting Teacher Platform..."
echo ""

# Start MariaDB
echo "📦 Starting database..."
pkill -9 mariadbd 2>/dev/null
sleep 2
mariadbd --user=mysql --datadir=/var/lib/mysql --socket=/run/mysqld/mysqld.sock > /dev/null 2>&1 &
sleep 4

# Clear all caches
echo "🧹 Clearing caches..."
php artisan optimize:clear > /dev/null 2>&1

# Start Laravel server
echo "⚡ Starting Laravel server..."
echo ""
echo "✅ Teacher Platform is ready!"
echo "🌐 Access the platform at: http://localhost:8000/admin"
echo ""
echo "📧 Login with:"
echo "   Email: admin@admin.com"
echo "   Password: password"
echo ""
echo "🎨 NEW FEATURES:"
echo "   - Getting Started wizard (first login)"
echo "   - Upload Homework wizard (3 easy steps)"
echo "   - Quick Actions widget on dashboard"
echo "   - Bilingual support (EN/ES)"
echo "   - Modern purple design system"
echo ""
echo "Press Ctrl+C to stop the server"
echo "----------------------------------------"

php artisan serve
