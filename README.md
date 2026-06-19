# DOSC Group Company Website

เว็บไซต์บริษัท DOSC Group สร้างด้วย Laravel + Inertia.js + Vue 3 + Tailwind CSS
พร้อม Admin CMS เต็มรูปแบบ และ Supabase PostgreSQL + Storage

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP 8.5 |
| Frontend | Vue 3 (Composition API), Inertia.js |
| Styling | Tailwind CSS v4 |
| Database | PostgreSQL (Supabase) |
| File Storage | Supabase Storage |
| Rich Text | Tiptap v2 (blog editor) |
| Font | Kanit (Google Fonts via Bunny) |

## Setup

```bash
# 1. ติดตั้ง dependencies
composer install
npm install

# 2. copy env
cp .env.example .env
php artisan key:generate

# 3. ตั้งค่า .env (ดู section ด้านล่าง)

# 4. migrate database
php artisan migrate

# 5. run dev
composer run dev   # หรือรันแยก:
# php artisan serve
# npm run dev
```

## Environment Variables

```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-northeast-1.pooler.supabase.com   # ใช้ pooler ไม่ใช่ direct
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.{PROJECT_REF}
DB_PASSWORD={PASSWORD}

SUPABASE_URL=https://{PROJECT_REF}.supabase.co
SUPABASE_SERVICE_KEY={SERVICE_ROLE_KEY}
SUPABASE_STORAGE_BUCKET=portfolio-media
```

> **หมายเหตุ**: ใช้ Connection Pooler (`pooler.supabase.com:6543`) เท่านั้น
> Direct connection (`db.xxx.supabase.co:5432`) มีปัญหา DNS บางเครือข่าย

## Admin Access

- URL: `/admin`
- Login: `/login`
- Auth redirect หลัง login ไปที่ route `admin.dashboard` (`/admin`)

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/          # CMS controllers (Service, Portfolio, Package ฯลฯ)
│   └── Auth/           # Laravel Breeze auth (redirect → admin.dashboard)
├── Models/             # Eloquent models (ดูรายการด้านล่าง)
└── Services/
    └── SupabaseStorageService.php   # จัดการ upload/delete ไฟล์

resources/js/
├── Layouts/
│   ├── PublicLayout.vue    # Layout หน้าสาธารณะ (navbar + footer)
│   └── AdminLayout.vue     # Layout หน้า admin (sidebar)
└── Pages/
    ├── Home.vue            # หน้าแรก (รวม sections ทั้งหมด)
    ├── Admin/              # CMS pages
    └── ...

database/migrations/        # ทุก migration เรียงตามลำดับ
routes/web.php              # Public + Admin routes (prefix: admin.)
```

## Models

| Model | ตาราง | ใช้งาน |
|-------|-------|--------|
| SiteSetting | site_settings | logo, favicon, contact, social links |
| HeroBanner | hero_banners | hero section หน้าแรก |
| Service | services | บริการของเรา (slider หน้าแรก) + `card_bg_color` |
| Portfolio | portfolios | ผลงาน (filter by package_name) |
| Package | packages | แพ็กเกจ + gradient colors |
| PackageFeature | package_features | features ใน package |
| PackageAddon | package_addons | add-ons ของ package |
| WorkStep | work_steps | ขั้นตอนการทำงาน |
| WhyUsItem | why_us_items | ทำไมต้องเลือกเรา |
| Testimonial | testimonials | รีวิวลูกค้า (carousel) |
| TestimonialSetting | testimonial_settings | ตั้งค่า testimonial section |
| Partner | partners | พาร์ทเนอร์ / ลูกค้า (marquee) |
| PartnerSetting | partner_settings | ตั้งค่า partner section |
| Blog | blogs | บทความ (Tiptap editor) |
| BlogCategory | blog_categories | หมวดหมู่บทความ |
| Faq | faqs | คำถามพบบ่อย (accordion) |
| Contact | contacts | ข้อมูลติดต่อจากฟอร์ม |

## Home Page Sections (ตามลำดับ)

1. **Hero Banner** — dynamic bg (solid/gradient/image/video), typewriter animation
2. **Services** — full-width horizontal slider, `card_bg_color` per service, description เป็น bullet (แบ่งด้วย `\n`)
3. **Portfolio** — bg-gray-100, filter tabs by `package_name`, แสดง 9 รายการต่อ filter
4. **Packages** — bg-gray-50, white card + gradient bubble header, badge absolute บนราคา
5. **Contact Strip** — dark bar แสดง phone/email
6. **Work Steps** — dark bg section
7. **Why Us** — white section
8. **Partners** — marquee scroll, configurable bg color
9. **Testimonials** — carousel, configurable bg color
10. **FAQ** — accordion
11. **Blog Preview** — 3 latest posts

## Admin CMS

| URL | จัดการ |
|-----|--------|
| `/admin` | Dashboard |
| `/admin/settings` | Site name, logo, favicon, contact, social links |
| `/admin/hero-banner` | Hero banner (multiple, sortable) |
| `/admin/services` | Services (drag-sort, card_bg_color) |
| `/admin/portfolio` | Portfolio (drag-sort, filter tag = package_name) |
| `/admin/packages` | Packages + features + addons |
| `/admin/partners` | Partners + marquee settings |
| `/admin/testimonials` | Testimonials + section settings |
| `/admin/faq` | FAQ items |
| `/admin/contacts` | Contact form submissions |
| `/admin/blog` | Blog posts (Tiptap editor) |

## File Storage (Supabase)

`SupabaseStorageService` จัดการ upload/delete ผ่าน Supabase Storage REST API
- Bucket: `portfolio-media` (ตั้งค่าใน `.env`)
- URL เก็บใน `*_url` column, path เก็บใน `*_path` column

## Key Decisions & Gotchas

- **DB connection**: ใช้ pooler URL (`pooler.supabase.com:6543`) ไม่ใช่ direct (`db.xxx.supabase.co`) เพราะ DNS resolve ไม่ได้บางเครือข่าย
- **Favicon**: ดึงจาก `SiteSetting::current()->favicon_url` ใน `app.blade.php` โดยตรง ไม่ผ่าน Inertia
- **Site settings**: Share ผ่าน `HandleInertiaRequests` middleware → `page.props.site` ทุก page
- **Auth redirect**: route name ต้องเป็น `admin.dashboard` (prefix group) ไม่ใช่ `dashboard`
- **Services slider**: programmatic `scrollTo()` บน `overflow-x: hidden` element (ยังเลื่อนได้ด้วย JS)
- **Portfolio filter**: client-side จาก `package_name` field, limit 9 items per filter (ไม่มี pagination)
- **Package badge**: `position: absolute` บน price container เพื่อไม่ให้กดราคาลง
