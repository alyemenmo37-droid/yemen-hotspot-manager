# اليوم 5: Files + JSON ✅

## 📅 التاريخ: 2026-10-10

## 📚 المفاهيم
- قراءة/كتابة الملفات
- JSON Encode/Decode
- file_get_contents / file_put_contents
- file_exists / unlink
- JSON_PRETTY_PRINT
- تأمين الملفات

## 💡 الأهم لـ YHM
- تخزين البطاقات في ملف JSON (بدل قاعدة البيانات مؤقتاً)
- حفظ سجل المبيعات
- سجل الدخول (logs)
- Backup بسيط

## 🎯 المشروع
نظام بطاقات كامل يعمل بملف JSON:
- قراءة البطاقات من ملف
- إضافة بطاقة جديدة
- تعديل بطاقة
- حذف بطاقة
- البحث والفلترة

# اليوم 5: Files + JSON ✅

## 📅 التاريخ: 2026-10-10

## ⏱️ الوقت المستغرق
- النظرية: 1 ساعة
- التطبيق: 1 ساعة
- المشروع: 1 ساعة
- **المجموع: 3 ساعات**

## ✅ ما تعلمته
- [x] قراءة/كتابة الملفات (`file_get_contents/put_contents`)
- [x] append بوضع `FILE_APPEND`
- [x] قراءة سطراً بسطر (`file()`)
- [x] معلومات الملفات (`filesize`, `filemtime`)
- [x] حذف الملفات (`unlink`)
- [x] JSON Encode/Decode
- [x] `JSON_PRETTY_PRINT` و `JSON_UNESCAPED_UNICODE`
- [x] معالجة أخطاء JSON (`json_last_error`)
- [x] إنشاء Class كامل (`CardStorage`)

## 💻 المشاريع المنجزة
1. `01-file-basics.php` — أساسيات الملفات
2. `02-json-basics.php` — أساسيات JSON
3. `03-yhm-storage.php` — نظام تخزين البطاقات الكامل 🎉

## 🎁 CardStorage Class
- `add()` — إضافة بطاقة
- `find()` — بحث
- `delete()` — حذف
- `setStatus()` — تغيير الحالة
- `stats()` — إحصائيات
- `generate()` — توليد عشوائي
- `log()` — تسجيل العمليات (داخلي)

## 🚧 الصعوبات
- فهم `__DIR__` → مسار الملف الحالي
- `JSON_PRETTY_PRINT` يجعل الملف أكبر لكن أسهل للقراءة

## 🎯 خطة الغد
- [ ] OOP في PHP (Classes, Objects)
- [ ] الوراثة (Inheritance)
- [ ] الواجهات (Interfaces)
- [ ] مشروع: نظام YHM بالكامل بـ OOP

## 🏆 إنجاز اليوم
✅ أول نظام تخزين حقيقي (بديل مؤقت عن قاعدة البيانات)
✅ أول Class كامل في YHM
✅ فهم أعمق لـ OOP
