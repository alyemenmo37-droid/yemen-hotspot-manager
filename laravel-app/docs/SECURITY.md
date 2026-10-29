# 🔒 قائمة الأمان (Security Checklist)

## ✅ قبل النشر

### 1. البيئة
- [ ] `APP_DEBUG=false` في `.env`
- [ ] `APP_ENV=production`
- [ ] `APP_KEY` مُولّد (32 char)
- [ ] `.env` غير مرفوع لـ Git
- [ ] `.env` صلاحيات 600

### 2. قاعدة البيانات
- [ ] كلمة مرور قوية
- [ ] مستخدم منفصل (ليس root)
- [ ] صلاحيات محددة
- [ ] `bind-address` = 127.0.0.1
- [ ] Backup يومي

### 3. HTTPS
- [ ] SSL Certificate (Let's Encrypt)
- [ ] Redirect HTTP → HTTPS
- [ ] HSTS header
- [ ] `SESSION_SECURE_COOKIE=true`

### 4. Authentication
- [ ] كلمات مرور قوية (8+ char)
- [ ] Rate limiting على /login
- [ ] 2FA للمسؤولين (اختياري)
- [ ] Session timeout

### 5. Authorization
- [ ] Middleware admin مفعل
- [ ] Policies/Guards
- [ ] CSRF على كل Forms
- [ ] Validation على كل Input

### 6. Files
- [ ] `storage/` غير قابل للوصول من الويب
- [ ] `vendor/` غير قابل للوصول
- [ ] `.git/` غير قابل للوصول
- [ ] `public/` فقط

### 7. Backup
- [ ] Backup تلقائي يومي
- [ ] Backup خارج السيرفر
- [ ] اختبار الاستعادة
- [ ] Retention 7+ أيام

### 8. Monitoring
- [ ] Health Check دوري
- [ ] Log rotation
- [ ] Alerts للإطاحة
- [ ] Uptime monitoring

### 9. Dependencies
- [ ] `composer audit` نظيف
- [ ] `npm audit` نظيف
- [ ] تحديث دوري

### 10. Logs
- [ ] `LOG_LEVEL=error` (ليس debug)
- [ ] Logs غير متاحة من الويب
- [ ] Rotation (7+ days)
