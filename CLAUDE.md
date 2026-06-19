# CLAUDE.md — Context for Claude Code

โปรเจคนี้คือเว็บไซต์บริษัท **DOSC Group** (บริษัทรับทำเว็บไซต์) พัฒนาด้วย Laravel + Inertia.js + Vue 3

## Stack โดยสรุป

- **Laravel 13** + **PHP 8.5** — backend, routing, auth
- **Inertia.js** + **Vue 3 (Composition API `<script setup>`)** — frontend SPA
- **Tailwind CSS v4** — styling (ใช้ `@tailwindcss/vite` plugin ไม่ใช่ PostCSS config เดิม)
- **PostgreSQL ผ่าน Supabase** — database (connection pooler port 6543)
- **Supabase Storage** — file uploads ทุกชนิด (ผ่าน `SupabaseStorageService`)
- **Tiptap v2** — rich text editor สำหรับ blog

## คำสั่งที่ใช้บ่อย

```bash
npm run build          # build assets สำหรับ production
npm run dev            # dev server (Vite HMR)
php artisan migrate    # run pending migrations
php artisan serve      # Laravel dev server
composer run dev       # รัน artisan serve + npm dev พร้อมกัน
```

## โครงสร้าง Frontend ที่สำคัญ

```
resources/js/
├── Layouts/
│   ├── PublicLayout.vue     # navbar + footer สาธารณะ
│   └── AdminLayout.vue      # sidebar admin
└── Pages/
    ├── Home.vue             # หน้าแรก — ทุก section อยู่ในไฟล์เดียว
    └── Admin/
        ├── Service/Index.vue
        ├── Portfolio/Index.vue
        ├── Packages/...
        └── Settings/Index.vue
```

## โครงสร้าง Backend ที่สำคัญ

```
app/
├── Http/
│   ├── Controllers/Admin/   # CMS controllers
│   ├── Controllers/Auth/    # Breeze auth — redirect ไป admin.dashboard
│   └── Middleware/HandleInertiaRequests.php  # share site settings ทุก page
├── Models/                  # Eloquent models
└── Services/SupabaseStorageService.php
```

## Routes

- Public: `/`, `/portfolio`, `/services/*`, `/faq`, `/blog/*`, `/contact`, `/privacy-policy`
- Admin: `/admin/*` — prefix `admin.`, middleware `auth,verified`
- Auth: `/login`, `/register` ฯลฯ — หลัง login redirect → `admin.dashboard`

## Database (Supabase PostgreSQL)

ใช้ **Transaction Pooler** เท่านั้น:
```
Host: aws-0-ap-northeast-1.pooler.supabase.com
Port: 6543
Username: postgres.{project_ref}
```
ห้ามใช้ direct host (`db.xxx.supabase.co`) เพราะ DNS resolve ไม่ได้

## Global Shared Props (ทุก page)

ผ่าน `HandleInertiaRequests`:
- `page.props.site` — `SiteSetting` object (logo_url, favicon_url, phone, email, social_links ฯลฯ)
- `page.props.auth.user` — user ปัจจุบัน

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
