# 📡 Yemen Hotspot Manager (YHM)

نظام احترافي مفتوح المصدر لإدارة شبكات الواي فاي المدفوعة (Hotspot).

## ✨ الميزات

### 🎫 البطاقات
- CRUD كامل
- تصنيفات وفئات
- مستويات سرعة
- تتبع استهلاك

### 🔐 المصادقة
- 3 أدوار (admin, manager, operator)
- تسجيل دخول آمن
- 2FA (قريباً)

### 🌐 MikroTik
- API Integration
- Mock Mode للتطوير
- Active Sessions
- Traffic Simulation

### 🔴 RADIUS
- FreeRADIUS Integration
- CoA Client
- Accounting Sync
- Auto-disconnect

### 📊 التقارير
- PDF Reports (3)
- CSV Export
- إحصائيات متقدمة

### 📱 Mobile API
- Laravel Sanctum
- 11 endpoints
- Rate Limiting

### 🏢 Multi-Tenant
- شبكات متعددة
- عزل البيانات

### 📅 Subscriptions
- اشتراكات شهرية
- تجديد تلقائي

## 🛠️ التقنيات

- **Laravel 13**
- **PHP 8.5**
- **MySQL 8**
- **FreeRADIUS 3.2**
- **MikroTik RouterOS**
- **TailwindCSS**

## 🚀 البدء السريع

```bash
git clone https://github.com/alyemenmo37-droid/yemen-hotspot-manager.git
cd yemen-hotspot-manager/laravel-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
