# CLAUDE.md — Context for Claude Code

โปรเจคนี้คือเว็บไซต์บริษัท **DOSC Group** (บริษัทรับทำเว็บไซต์) พัฒนาด้วย Laravel + Inertia.js + Vue 3

## Database (Supabase PostgreSQL)

ใช้ **Transaction Pooler** เท่านั้น:
```
Host: aws-0-ap-northeast-1.pooler.supabase.com
Port: 6543
Username: postgres.{project_ref}
```
ห้ามใช้ direct host (`db.xxx.supabase.co`) เพราะ DNS resolve ไม่ได้

## Home.vue — Section Map

| Section | Vue ref/state | หมายเหตุ |
|---------|--------------|----------|
| Services | `serviceSlide`, `serviceSliderRef` | programmatic scrollTo on overflow-x:hidden |
| Portfolio | `portfolioFilter`, `filteredPortfolios` | client-side filter, limit 9 |
| Packages | — | badge absolute position บน price block |
| Testimonials | `activeSlide`, `autoplayTimer` | Vue Transition slide-card |
| Partners | `marqueeItems` | CSS marquee animation |

## Admin Forms — Pattern

ทุก admin form ใช้ `useForm()` จาก Inertia + `router.post/delete` pattern:
- store: `form.post(route('admin.xxx.store'))`
- update: `form.post(route('admin.xxx.update', id))` (ใช้ POST ไม่ใช่ PUT เพราะมี file upload)
- reorder: `router.post(route('admin.xxx.reorder'), { ids })` drag-sort

## File Upload Pattern

```js
// ใน form: image เป็น File object
form.image = file;
// submit ด้วย form.post() — Inertia จัดการ multipart ให้อัตโนมัติ
```

Controller ฝั่ง PHP:
```php
$this->storage->upload($file, "folder/{$id}.{$ext}");
// → return ['path' => ..., 'url' => ...]
```

## Favicon

ดึงจาก DB โดยตรงใน `resources/views/app.blade.php`:
```php
@php $favicon = \App\Models\SiteSetting::current()->favicon_url; @endphp
```
(ไม่ผ่าน Inertia เพราะต้องอยู่ใน `<head>` ของ blade)

## Fields ที่เพิ่มล่าสุด

- `services.card_bg_color` — สีพื้นหลัง card ใน slider (migration: `2026_06_18_090000`)
- ต้อง include ใน select ของ `HomeController::index()` และ `ServiceController::validated()`

## สิ่งที่ควรระวัง

1. **ไม่ใช้ `route('dashboard')`** — ต้องใช้ `route('admin.dashboard')` เสมอ
2. **Tailwind v4** — ไม่มี `tailwind.config.js` content array แบบเดิม ใช้ plugin ผ่าน vite แทน
3. **Service description เป็น bullet** — แยกด้วย `\n` ใน textarea, split ใน Vue template
4. **Portfolio ใน home** — ดึงทั้งหมด (ไม่มี limit) แล้ว filter client-side แสดง 9 รายการ
5. **Supabase free tier** — project อาจ pause ได้ ถ้า DNS ไม่ resolve ให้เช็ค dashboard ก่อน
