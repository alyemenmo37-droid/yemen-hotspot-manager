# 📡 Yemen Hotspot Manager (YHM)

<p align="center">
  <img src="assets/logo.png" alt="YHM Logo" width="200">
</p>

<p align="center">
  <strong>نظام إدارة Hotspot احترافي، مفتوح المصدر، آمن، ويمني.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/version-0.1.0--dev-blue" alt="Version">
  <img src="https://img.shields.io/badge/status-under--development-yellow" alt="Status">
  <img src="https://img.shields.io/badge/made%20in-Yemen%20🇾🇪-green" alt="Made in Yemen">
  <img src="https://img.shields.io/badge/license-MIT-blue" alt="License">
</p>

---

## 🎯 ما هو YHM؟

**Yemen Hotspot Manager** هو نظام متكامل لإدارة شبكات الواي فاي
المدفوعة (Hotspot)، يجمع بين:

- 🎫 إدارة البطاقات (توليد، تفعيل، طباعة)
- 👥 إدارة المستخدمين
- ⚡ تحديد السرعات
- 📊 التقارير والإحصائيات
- 🔐 الأمان المتقدم
- 🌐 تكامل MikroTik + RADIUS

## ✨ الميزات

### 🎫 إدارة البطاقات
- [x] توليد بطاقات بكميات كبيرة
- [x] فئات متعددة (بطيء، متوسط، عالي، مفتوح)
- [ ] طباعة PDF قابلة للتخصيص
- [ ] إعادة شحن
- [ ] تفعيل/إيقاف
- [ ] استيراد من ملفات CSV/SQLite

### 👥 إدارة المستخدمين
- [ ] لوحة تحكم إدارية
- [ ] صلاحيات متعددة
- [ ] سجل النشاطات
- [ ] 2FA للمسؤولين

### ⚡ السرعات
- [x] مستويات قابلة للتخصيص
- [x] تغيير فوري
- [ ] جدولة زمنية
- [ ] Rate Limiting متقدم

### 📊 التقارير
- [ ] مبيعات يومية/شهرية
- [ ] استهلاك البيانات
- [ ] أنماط الاستخدام
- [ ] تصدير Excel/PDF

### 🔐 الأمان
- [ ] Rate Limiting شامل
- [ ] CSRF/XSS Protection
- [ ] SQL Injection Prevention
- [ ] Audit Logging
- [ ] HTTPS إجباري
- [ ] Fail2ban Integration

### 🌐 التكامل
- [ ] MikroTik RouterOS
- [ ] FreeRADIUS
- [ ] SMS Gateway
- [ ] WhatsApp/Telegram Bots

## 🛠️ التقنيات

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Laravel-11+-FF2D20?logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vue.js" alt="Vue">
  <img src="https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/Redis-7-DC382D?logo=redis" alt="Redis">
  <img src="https://img.shields.io/badge/TailwindCSS-3-38B2AC?logo=tailwind-css" alt="Tailwind">
</p>

## 🚀 البدء السريع

### المتطلبات
- PHP 8.3+
- Composer
- Node.js 20+
- MySQL 8+ أو MariaDB
- Redis (اختياري)

### التثبيت

```bash
git clone https://github.com/alyemenmo37-droid/yemen-hotspot-manager.git
cd yemen-hotspot-manager

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed

php artisan serve
