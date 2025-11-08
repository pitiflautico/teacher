# 🚀 Teacher Platform - Project Launch Guide

Complete guide to launching and running the Teacher Platform project locally.

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Database Setup](#database-setup)
4. [Running the Project](#running-the-project)
5. [Default Credentials](#default-credentials)
6. [Features Overview](#features-overview)
7. [Troubleshooting](#troubleshooting)

---

## 🔧 Prerequisites

Make sure you have the following installed:

- **PHP**: 8.2+
- **Composer**: 2.7+
- **Node.js**: 20+
- **NPM**: 10+
- **MySQL/MariaDB**: 10.6+
- **Git**: 2.x+

### Verify Installation

```bash
php -v
composer -v
node -v
npm -v
mysql --version
```

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone [repository-url]
cd teacher
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teacher_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Configure AI Providers (Optional)

Add API keys for AI providers:

```env
# OpenAI (Primary)
OPENAI_API_KEY=sk-your-key-here

# Together.ai (Recommended - Cost Effective)
TOGETHER_API_KEY=your-key-here

# Replicate (Open Source Models)
REPLICATE_API_KEY=r8_your-key-here
```

**Get API Keys:**
- OpenAI: https://platform.openai.com/api-keys
- Together.ai: https://api.together.xyz/
- Replicate: https://replicate.com/account/api-tokens

---

## 🗄️ Database Setup

### 1. Create Database

```bash
mysql -u root -p
CREATE DATABASE teacher_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 2. Run Migrations & Seeders

```bash
php artisan migrate:fresh --seed
```

This will:
- Create all database tables (24 tables)
- Seed roles (admin, teacher, student)
- Create admin user
- Seed 20 initial badges
- Setup permissions

### 3. Additional Seeders (Optional)

```bash
# Seed sample data for testing
php artisan db:seed --class=BadgeSeeder
```

---

## 🎯 Running the Project

### Development Environment

#### 1. Start the Database (if not running)

```bash
# For MariaDB/MySQL
sudo systemctl start mysql

# Or if using Docker
docker-compose up -d mysql
```

#### 2. Build Frontend Assets

```bash
# Development (with hot reload)
npm run dev

# Production build
npm run build
```

#### 3. Start Laravel Server

```bash
php artisan serve
```

The application will be available at:
**http://localhost:8000/admin**

#### 4. Start Queue Worker (Optional - for background tasks)

In a new terminal:

```bash
php artisan queue:work
```

This is required for:
- AI content generation
- OCR processing
- Email notifications
- Badge awards

---

## 🔑 Default Credentials

After running seeders, you can login with:

```
Email: admin@admin.com
Password: password
```

**⚠️ IMPORTANT**: Change these credentials immediately in production!

---

## ✨ Features Overview

### 🎨 Modern Dashboard
- **Welcome Hero Card**: Personalized greeting with quick stats
- **Stats Overview**: 4 KPI cards with trends and charts
- **Learning Progress Chart**: 30-day activity visualization
- **Recent Activity Feed**: Real-time learning updates
- **Gamification Stats**: Points, badges, and achievements

### 📚 Core Features

1. **Subject Management**
   - Create and organize subjects
   - Nested topics hierarchy
   - Color-coded categories

2. **Material Upload & OCR**
   - Upload PDFs, images, documents
   - Automatic text extraction
   - AI-powered content analysis

3. **Exercise Generation**
   - AI-generated exercises from materials
   - 5 types: Multiple Choice, True/False, Short Answer, Essay, Problem Solving
   - 3 difficulty levels: Easy, Medium, Hard
   - Automatic grading

4. **Flashcards with SM-2**
   - Spaced repetition algorithm
   - Quality ratings (0-5)
   - Automatic scheduling
   - Due date tracking

5. **Calendar & Planning**
   - FullCalendar integration
   - Month/Week/Day views
   - Event management
   - Study session planning

6. **Mind Maps**
   - Visual knowledge organization
   - Node-based structure
   - JSON storage

7. **Study Groups**
   - Collaborative learning
   - Group messaging
   - Member management

8. **Gamification System**
   - 20 achievement badges
   - Points system
   - Level progression
   - Streak tracking

9. **Social Features**
   - User profiles
   - Follow system
   - Activity feeds
   - Public/private profiles

10. **Multi-AI Provider Support**
    - OpenAI (GPT-4o-mini)
    - Together.ai (Llama 3.1)
    - Replicate (Llama 2)
    - User-selectable preference
    - Live comparison tool

11. **REST API**
    - 15+ endpoints
    - Laravel Sanctum authentication
    - Mobile app ready
    - Gamification data included

---

## 🎨 Design System

### Color Palette

- **Primary Purple**: #8B5CF6
- **Accent Yellow**: #FBBF24
- **Success Green**: #34D399
- **Danger Coral**: #F87171
- **Info Blue**: #60A5FA

### Components

- **Cards**: Rounded (1rem), subtle shadows
- **Gradients**: Purple-to-yellow, blue-to-white
- **Badges**: Fully rounded, color-coded
- **Icons**: Heroicons (outline & solid)
- **Typography**: System fonts, bold metrics

---

## 🐛 Troubleshooting

### Database Connection Issues

```bash
# Check if MySQL is running
sudo systemctl status mysql

# Restart MySQL
sudo systemctl restart mysql

# Check .env database credentials
cat .env | grep DB_
```

### Migration Errors

```bash
# Fresh start (⚠️ deletes all data)
php artisan migrate:fresh --seed

# Rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

### Asset Compilation Issues

```bash
# Clear cache
npm cache clean --force
rm -rf node_modules
npm install

# Rebuild
npm run build
```

### Permission Errors

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Queue Not Processing

```bash
# Check queue status
php artisan queue:failed

# Restart queue worker
php artisan queue:restart
php artisan queue:work
```

---

## 📱 Accessing the Application

### Admin Panel
**URL**: http://localhost:8000/admin

### API Endpoints
**Base URL**: http://localhost:8000/api

**Authentication**:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@admin.com","password":"password"}'
```

---

## 🧪 Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature
```

### Test Coverage

```bash
php artisan test --coverage
```

---

## 📚 Additional Documentation

- **Feature Testing Guide**: `FEATURE_TESTING_GUIDE.md`
- **AI Providers Guide**: `AI_PROVIDERS_GUIDE.md`
- **Project Summary**: `PROJECT_SUMMARY.md`
- **Roadmap**: `ROADMAP.md`
- **Launch Guide** (Spanish): `GUIA_LANZAMIENTO.md`

---

## 🔄 Development Workflow

### Making Changes

1. Create a new branch
```bash
git checkout -b feature/your-feature-name
```

2. Make your changes

3. Run tests
```bash
php artisan test
```

4. Build assets
```bash
npm run build
```

5. Commit changes
```bash
git add .
git commit -m "feat: add your feature description"
```

6. Push to remote
```bash
git push origin feature/your-feature-name
```

---

## 🚀 Production Deployment

### Before Deploying

1. **Optimize autoloader**
```bash
composer install --optimize-autoloader --no-dev
```

2. **Cache configuration**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Build production assets**
```bash
npm run build
```

4. **Set environment**
```env
APP_ENV=production
APP_DEBUG=false
```

5. **Run migrations**
```bash
php artisan migrate --force
```

6. **Setup queue supervisor** (recommended)
```bash
sudo apt-get install supervisor
```

---

## 💡 Tips & Best Practices

### Performance

- Use **Redis** for cache and sessions in production
- Enable **OPcache** for PHP
- Use **queue workers** for heavy tasks
- Enable **database query caching**

### Security

- Change default admin credentials
- Use strong passwords
- Enable HTTPS in production
- Keep dependencies updated
- Use rate limiting for API

### Development

- Use **Laravel Debugbar** for debugging
- Run **php artisan optimize:clear** after changes
- Use **git hooks** for pre-commit checks
- Follow **PSR-12** coding standards

---

## 🆘 Getting Help

### Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# List all routes
php artisan route:list

# List all artisan commands
php artisan list

# Check system requirements
php artisan about

# Interactive tinker console
php artisan tinker
```

### Useful Links

- **Laravel Docs**: https://laravel.com/docs/11.x
- **Filament Docs**: https://filamentphp.com/docs/3.x
- **TailwindCSS Docs**: https://tailwindcss.com/docs

---

## 📊 Project Stats

- **24** Database Tables
- **14** Filament Resources
- **46** Unit Tests
- **13** Major Features
- **15+** API Endpoints
- **20** Achievement Badges
- **3** AI Providers

---

## 🎉 Ready to Go!

Your Teacher Platform is now ready! Access the admin panel at:

**http://localhost:8000/admin**

Login with:
- Email: `admin@admin.com`
- Password: `password`

**Happy Teaching! 🎓**
