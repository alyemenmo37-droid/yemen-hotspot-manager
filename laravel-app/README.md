# 📡 Yemen Hotspot Manager (YHM)

<p align="center">
  <img src="https://img.shields.io/badge/version-2.0.0-blue?style=for-the-badge" alt="Version">
  <img src="https://img.shields.io/badge/Laravel-13-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.5-purple?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8-orange?style=for-the-badge&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/Made%20in-Yemen%20🇾🇪-green?style=for-the-badge" alt="Yemen">
</p>

<p align="center">
  <strong>نظام احترافي لإدارة شبكات الواي فاي المدفوعة</strong>
</p>

## 🎯 ما هو YHM؟

**Yemen Hotspot Manager** هو نظام متكامل لإدارة شبكات Hotspot (الواي فاي المدفوع) مبني على:
- **Laravel 13** (إطار حديث)
- **MikroTik RouterOS** (راوترات احترافية)
- **FreeRADIUS** (مصادقة مركزية)
- **Vue/Blade** (واجهات عربية)

## ✨ الميزات

### 🎫 إدارة البطاقات
- CRUD كامل
- تصنيفات (4)
- مستويات سرعة (6)
- تتبع الاستهلاك
- حالة (4 أنواع)

### 👥 إدارة المستخدمين
- 3 أدوار (admin, manager, operator)
- تسجيل دخول آمن
- إدارة مستخدمين
- ملف شخصي

### 🌐 MikroTik
- Mock Mode (للتطوير)
- Real Mode (للإنتاج)
- إضافة/حذف hotspot users
- Active sessions
- Traffic simulation

### 🔴 RADIUS
- FreeRADIUS Integration
- 6 جداول قياسية
- مزامنة تلقائية
- Accounting

### 🎯 CoA
- Disconnect-Request
- Change-Authorization
- Auto-disconnect

### 📊 الإحصائيات
- مبيعات
- مستخدمون
- استهلاك بيانات
- Top consumers
- Audit log

### 🔒 الأمان
- CSRF Protection
- Auth Middleware
- Role-based Access
- Password Hashing
- Session Security
- Security Audit

### 🚀 Production
- Backup تلقائي
- Health Check
- Error Pages
- Deployment Guide

## 📊 الإحصائيات

| العنصر | العدد |
|--------|-------|
| Routes | 45 |
| Controllers | 8 |
| Models | 6 |
| Views | 25+ |
| Migrations | 12 |
| Commands | 6 |
| RADIUS Tables | 6 |

## 🛠️ التثبيت

```bash
# 1. Clone
git clone https://github.com/alyemenmo37-droid/yemen-hotspot-manager.git
cd yemen-hotspot-manager/laravel-app

# 2. Dependencies
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Database
# عدّل .env
php artisan migrate
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CategorySpeedSeeder
php artisan db:seed --class=SettingSeeder

# 5. Run
php artisan serve
