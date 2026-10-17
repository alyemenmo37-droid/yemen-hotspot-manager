# اليوم 11: Relations ✅

## 📚 المفاهيم
- One-to-Many
- Many-to-Many (مع جدول وسيط)
- One-to-One
- Eager Loading vs Lazy Loading
- N+1 Problem

## 💡 الأهم لـ YHM
- category → speed_levels (1:N)
- speed_level → cards (1:N)
- card → sessions (1:N)
- card ↔ groups (N:M)
# اليوم 11: Relations ✅

## 📅 التاريخ: 2026-10-14

## ⏱️ الوقت المستغرق
- النظرية: 1 ساعة
- التطبيق: 1 ساعة
- المشروع: 1 ساعة
- **المجموع: 3 ساعات**

## ✅ ما تعلمته
- [x] One-to-Many
- [x] Many-to-Many (Pivot Table)
- [x] One-to-One
- [x] N+1 Problem
- [x] Eager Loading
- [x] GROUP_CONCAT
- [x] Repository Pattern
- [x] Foreign Keys في التطبيق
- [x] data_limit_mb (حجم البيانات)
- [x] data_used_mb (الاستهلاك)
- [x] RADIUS Accounting
- [x] MikroTik API (bytes-in/out)

## 💻 المشاريع المنجزة
1. `01-one-to-many.php` — عرض العلاقات
2. `02-many-to-many.php` — Pivot Table
3. `03-yhm-with-relations.php` — YHM كامل
4. `CardRepository.php` — Repository احترافي

## 🎁 محتوى جديد
- `data_limit_mb` — حد البيانات
- `data_used_mb` — المستهلك
- `card_groups` — المجموعات
- `card_group_pivot` — الجدول الوسيط

## 🚧 الصعوبات
- فهم N+1 → Eager Loading يحل المشكلة
- Foreign Keys بعد إنشاء → ALTER TABLE

## 🎯 خطة الغد
- [ ] JOIN + Aggregation متقدم
- [ ] تقارير احترافية
- [ ] Subqueries + CTE
- [ ] Window Functions

## 🏆 إنجاز اليوم
✅ فهم عميق للعلاقات
✅ Repository Pattern
✅ تتبع حجم البيانات
