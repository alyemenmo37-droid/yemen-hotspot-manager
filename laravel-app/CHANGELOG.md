# Changelog
## [2.1.0] - 2026-11-05

### ✨ الجديد

#### 📱 Mobile API
- Laravel Sanctum Integration
- 11 API endpoints
- Token-based auth
- Rate limiting (5/min login)
- API Resources

#### 📄 PDF Reports
- Cards Report (landscape)
- Sales Report (portrait)
- Usage Report (top 30)
- Arabic RTL support
- DejaVu Sans font

#### 📊 CSV Export
- Cards CSV
- Usage CSV
- UTF-8 BOM (Excel compatible)

#### 🔔 Notifications
- Telegram Bot integration
- 5 notification types:
  - New card
  - Data limit exceeded
  - Low stock
  - New user
  - CoA failure
- Auto-alerts

#### 🔍 Advanced Features
- Advanced Search (by number)
- Multi-filter (status, category, price)
- Sort options (4 types)
- Bulk actions (activate, expire, delete)
- Select all
- Bulk action bar

#### 📈 Accounting
- RADIUS Accounting dashboard
- Top consumers
- Overall stats (GB, sessions)
- Manual sync button

### 🐛 إصلاحات
- Fixed missing `create()` method in CardController
- Fixed search behavior (correct LIKE queries)
- Fixed all cache issues

### 📊 الإحصائيات
- Routes: 55
- Commands: 8
- API Endpoints: 11
- Notification Types: 5
- Report Templates: 3
- Migrations: 12

### 🎯 الميزات الكاملة
- Authentication (3 roles)
- Cards CRUD
- Categories & Speed Levels
- User Management
- Profile
- Statistics
- MikroTik (mock + real)
- RADIUS
- CoA
- Accounting
- Real-time Updates
- API (Mobile)
- PDF Reports
- CSV Export
- Telegram Notifications
- Advanced Search
- Bulk Actions

---

## [2.0.0] - 2026-10-31

## [1.0.0] - 2026-10-28

### 🎉 الإصدار الأول

#### ✨ الجديد
- نظام كامل لإدارة البطاقات
- نظام مصادقة (3 أدوار)
- لوحة إدارة المستخدمين
- تكامل MikroTik (Mock + Real)
- إحصائيات متقدمة
- Real-time updates (Polling)
- Toast notifications
- إدارة الجلسات

#### 🎨 UI
- تصميم RTL عربي
- Responsive (Mobile-friendly)
- Live updates
- Beautiful components

#### 🔒 الأمان
- CSRF protection
- Auth middleware
- Role-based access
- Password hashing
- Session security

#### 📊 البيانات
- 4 Categories
- 6 Speed Levels
- 31 Cards (test)
- 3 Users (test)
- Audit log

#### 🛠️ التقنيات
- Laravel 13
- PHP 8.5
- MySQL 8
- Blade + Vanilla JS

## [0.4.0] - 2026-10-27
- Statistics page
- Enhanced dashboard
- Custom pagination

## [0.3.0] - 2026-10-25
- Authentication
- Middleware + Roles
- User Profile

## [0.2.0] - 2026-10-18
- Cards CRUD
- Categories & Speed Levels
- Relations

## [0.1.0] - 2026-10-15
- PHP basics
- MySQL
- OOP

## [0.0.1] - 2026-10-05
- Project setup

# Changelog

جميع التغييرات المهمة في YHM.

## [2.0.0] - 2026-10-31

### 🎉 الإصدار الرئيسي الثاني

#### ✨ الجديد
- **RADIUS Integration** (FreeRADIUS)
  - 6 جداول RADIUS قياسية
  - مزامنة تلقائية مع البطاقات
  - `radius:sync` command
  
- **CoA Client** (RFC 5176)
  - Disconnect-Request (فصل فوري)
  - Change-Authorization (تغيير سرعة)
  - Port 3799
  
- **Accounting Sync**
  - مزامنة استهلاك البيانات
  - `radius:sync-accounting`
  - تحديث `data_used_mb` تلقائياً
  
- **Auto-Disconnect**
  - فصل تلقائي عند تجاوز الحد
  - `radius:check-limits`
  - Scheduler كل 5 دقائق
  
- **Production Ready**
  - Backup تلقائي (`backup:database`)
  - Health Check (`health:check`)
  - Error Pages (404, 500)
  - Security Checklist
  - Deployment Guide

#### 🔒 الأمان
- تحديث `symfony/mime` (CVE-2026-45070)
- `composer audit`: 0 vulnerabilities
- Session encryption
- CSRF protection كامل

#### 📊 الإحصائيات
- **Routes:** 45
- **Commands:** 6
- **RADIUS Tables:** 6
- **Views:** 25+
- **Docs:** 2 (SECURITY, DEPLOYMENT)

#### 🎯 الميزات الكاملة
- Authentication (3 roles)
- Cards CRUD
- Categories & Speed Levels
- User Management
- Profile
- Statistics
- MikroTik (mock + real)
- RADIUS
- CoA
- Accounting
- Real-time Updates

## [1.0.0] - 2026-10-28

### 🎉 الإصدار الأول

#### ✨ الجديد
- نظام كامل لإدارة البطاقات
- Authentication (3 roles)
- لوحة إدارة المستخدمين
- إحصائيات أساسية
- واجهة RTL

#### 🛠️ التقنيات
- Laravel 13
- PHP 8.5
- MySQL 8

---

## صيغة الإصدار

نستخدم [Semantic Versioning](https://semver.org/):
- **Major:** تغييرات كبيرة
- **Minor:** ميزات جديدة
- **Patch:** إصلاحات
