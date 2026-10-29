# Changelog

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
