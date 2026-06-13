<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\Testimonial;
use App\Models\TestimonialSetting;
use App\Models\WhyUsItem;
use App\Models\WorkStep;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Site Settings ───────────────────────────────────────────────────
        SiteSetting::current()->update([
            'site_name'        => 'DOSC Group',
            'tagline'          => 'พาร์ทเนอร์รู้ใจสำหรับธุรกิจของคุณ',
            'meta_description' => 'DOSC Group รับทำเว็บไซต์ครบวงจร ออกแบบ พัฒนา และดูแลเว็บไซต์สำหรับธุรกิจ ด้วยทีมมืออาชีพที่มีประสบการณ์กว่า 10 ปี',
            'phone'            => '081-689-9976',
            'email'            => 'info@dosc.co.th',
            'address'          => '25/49 ถนนรามคำแหง แขวงหัวหมาก เขตบางกะปิ กรุงเทพมหานคร 10240',
            'google_map_url'   => 'https://maps.google.com',
        ]);

        // ─── Social Links ─────────────────────────────────────────────────────
        SocialLink::where('platform', 'facebook')->update(['url' => 'https://facebook.com/doscgroup', 'is_active' => true]);
        SocialLink::where('platform', 'line')->update(['url' => 'https://line.me/ti/p/@dosc', 'is_active' => true]);
        SocialLink::where('platform', 'instagram')->update(['url' => 'https://instagram.com/doscgroup', 'is_active' => true]);

        // ─── Services ─────────────────────────────────────────────────────────
        $services = [
            [
                'title'       => 'ออกแบบเว็บไซต์',
                'subtitle'    => 'เว็บไซต์สวยงาม ตอบโจทย์ธุรกิจทุกขนาด',
                'description' => 'ออกแบบ UI/UX เว็บไซต์ให้น่าใช้งาน สวยงาม และสื่อสารแบรนด์ของคุณได้อย่างมีประสิทธิภาพ พร้อม Responsive Design รองรับทุกอุปกรณ์',
                'icon_name'   => 'design',
                'cta_text'    => 'ดูรายละเอียด',
                'cta_url'     => '/services/website',
                'badge_text'  => 'ยอดนิยม',
                'badge_color' => '#7c1d1d',
                'sort_order'  => 0,
            ],
            [
                'title'       => 'พัฒนาเว็บไซต์',
                'subtitle'    => 'โค้ดสะอาด เร็ว และดูแลง่ายในระยะยาว',
                'description' => 'พัฒนาเว็บไซต์ด้วยเทคโนโลยีล่าสุด ประสิทธิภาพสูง โหลดเร็ว SEO-ready พร้อมระบบจัดการ Content ให้คุณอัปเดตข้อมูลได้เอง',
                'icon_name'   => 'code',
                'cta_text'    => 'ดูรายละเอียด',
                'cta_url'     => '/services/website',
                'badge_text'  => null,
                'badge_color' => '#7c1d1d',
                'sort_order'  => 1,
            ],
            [
                'title'       => 'ร้านค้าออนไลน์',
                'subtitle'    => 'E-Commerce ครบวงจร พร้อมระบบชำระเงิน',
                'description' => 'สร้างร้านค้าออนไลน์ที่สมบูรณ์แบบ ระบบตะกร้าสินค้า การชำระเงิน การจัดการออเดอร์ และรายงานยอดขาย ทุกอย่างในที่เดียว',
                'icon_name'   => 'cart',
                'cta_text'    => 'ดูรายละเอียด',
                'cta_url'     => '/services/website',
                'badge_text'  => 'แนะนำ',
                'badge_color' => '#1d4ed8',
                'sort_order'  => 2,
            ],
            [
                'title'       => 'SEO & การตลาดดิจิทัล',
                'subtitle'    => 'ติดหน้าแรก Google เพิ่มลูกค้าออนไลน์',
                'description' => 'วางกลยุทธ์ SEO ให้เว็บไซต์ติดอันดับ Google ทำ Google Ads Facebook Ads เพิ่ม Traffic และ Conversion Rate ให้ธุรกิจของคุณ',
                'icon_name'   => 'chart',
                'cta_text'    => 'ดูรายละเอียด',
                'cta_url'     => '/services/google-ads',
                'badge_text'  => null,
                'badge_color' => '#7c1d1d',
                'sort_order'  => 3,
            ],
        ];

        foreach ($services as $s) {
            Service::firstOrCreate(['title' => $s['title']], array_merge($s, ['status' => 'active']));
        }

        // ─── Packages ─────────────────────────────────────────────────────────
        $packages = [
            [
                'pkg' => [
                    'name'            => 'One Page Package',
                    'slug'            => 'one-page',
                    'tag_text'        => 'เริ่มต้นง่าย',
                    'price'           => 15000,
                    'is_vat_excluded' => true,
                    'description'     => 'เหมาะสำหรับธุรกิจขนาดเล็ก ร้านค้า หรือฟรีแลนซ์ที่ต้องการออนไลน์อย่างรวดเร็ว',
                    'color_from'      => '#1d4ed8',
                    'color_to'        => '#3b82f6',
                    'badge_text'      => '',
                    'cta_primary_text'   => 'สั่งซื้อเลย',
                    'cta_primary_url'    => '/contact',
                    'cta_secondary_text' => 'ดูตัวอย่าง',
                    'cta_secondary_url'  => '/portfolio',
                    'status'          => 'active',
                    'sort_order'      => 0,
                ],
                'features' => [
                    'เว็บไซต์ 1 หน้า (Landing Page)',
                    'Responsive Design ทุกอุปกรณ์',
                    'ออกแบบ UI/UX สวยงาม',
                    'ติดตั้ง Google Analytics',
                    'SSL Certificate ฟรี',
                    'ส่งมอบภายใน 7 วันทำการ',
                    'แก้ไขได้ 2 รอบ',
                    'รับประกัน 6 เดือน',
                ],
            ],
            [
                'pkg' => [
                    'name'            => 'Starter Package',
                    'slug'            => 'starter',
                    'tag_text'        => 'คุ้มที่สุด',
                    'price'           => 28000,
                    'is_vat_excluded' => true,
                    'description'     => 'เหมาะสำหรับธุรกิจ SME ที่ต้องการเว็บไซต์หลายหน้าพร้อมระบบจัดการเนื้อหา',
                    'color_from'      => '#7c3aed',
                    'color_to'        => '#a855f7',
                    'badge_text'      => 'แนะนำ',
                    'cta_primary_text'   => 'สั่งซื้อเลย',
                    'cta_primary_url'    => '/contact',
                    'cta_secondary_text' => 'ดูตัวอย่าง',
                    'cta_secondary_url'  => '/portfolio',
                    'status'          => 'active',
                    'sort_order'      => 1,
                ],
                'features' => [
                    'เว็บไซต์สูงสุด 5 หน้า',
                    'ระบบจัดการเนื้อหา (CMS)',
                    'Responsive Design ทุกอุปกรณ์',
                    'ออกแบบ UI/UX Premium',
                    'ติดตั้ง SEO พื้นฐาน',
                    'Google Analytics & Search Console',
                    'SSL Certificate ฟรี',
                    'ส่งมอบภายใน 14 วันทำการ',
                    'แก้ไขได้ 3 รอบ',
                    'รับประกัน 12 เดือน',
                ],
            ],
            [
                'pkg' => [
                    'name'            => 'Business Package',
                    'slug'            => 'business',
                    'tag_text'        => 'ครบครัน',
                    'price'           => 34000,
                    'is_vat_excluded' => true,
                    'description'     => 'เหมาะสำหรับธุรกิจที่ต้องการเว็บไซต์ครบวงจรพร้อมระบบจัดการขั้นสูง',
                    'color_from'      => '#1f2937',
                    'color_to'        => '#374151',
                    'badge_text'      => '',
                    'cta_primary_text'   => 'สั่งซื้อเลย',
                    'cta_primary_url'    => '/contact',
                    'cta_secondary_text' => 'ดูตัวอย่าง',
                    'cta_secondary_url'  => '/portfolio',
                    'status'          => 'active',
                    'sort_order'      => 2,
                ],
                'features' => [
                    'เว็บไซต์สูงสุด 10 หน้า',
                    'ระบบจัดการเนื้อหา (CMS) ขั้นสูง',
                    'Responsive Design Premium',
                    'ระบบบทความ/Blog',
                    'ระบบติดต่อสอบถาม',
                    'SEO On-Page ครบถ้วน',
                    'Google Analytics ขั้นสูง',
                    'ระบบแจ้งเตือน Email',
                    'SSL Certificate ฟรี',
                    'ส่งมอบภายใน 21 วันทำการ',
                    'แก้ไขได้ไม่จำกัด 30 วัน',
                    'รับประกัน 12 เดือน',
                ],
            ],
            [
                'pkg' => [
                    'name'            => 'E-Commerce Package',
                    'slug'            => 'ecommerce',
                    'tag_text'        => 'ขายออนไลน์',
                    'price'           => 43000,
                    'is_vat_excluded' => true,
                    'description'     => 'ร้านค้าออนไลน์ครบวงจร พร้อมระบบชำระเงินและจัดการออเดอร์',
                    'color_from'      => '#b45309',
                    'color_to'        => '#f59e0b',
                    'badge_text'      => '',
                    'cta_primary_text'   => 'สั่งซื้อเลย',
                    'cta_primary_url'    => '/contact',
                    'cta_secondary_text' => 'ดูตัวอย่าง',
                    'cta_secondary_url'  => '/portfolio',
                    'status'          => 'active',
                    'sort_order'      => 3,
                ],
                'features' => [
                    'ร้านค้าออนไลน์ครบวงจร',
                    'ระบบตะกร้าสินค้าและ Checkout',
                    'เชื่อมต่อ Payment Gateway (SCB, KBank)',
                    'ระบบจัดการสินค้าและสต็อก',
                    'ระบบจัดการออเดอร์',
                    'ระบบแจ้งเตือนลูกค้าอัตโนมัติ',
                    'ระบบสมาชิกและโปรโมชั่น',
                    'รายงานยอดขายและสถิติ',
                    'SEO สำหรับ E-Commerce',
                    'ส่งมอบภายใน 30 วันทำการ',
                    'แก้ไขได้ไม่จำกัด 45 วัน',
                    'รับประกัน 12 เดือน',
                ],
            ],
        ];

        foreach ($packages as $item) {
            $pkg = Package::firstOrCreate(['slug' => $item['pkg']['slug']], $item['pkg']);
            if ($pkg->wasRecentlyCreated) {
                foreach ($item['features'] as $i => $title) {
                    PackageFeature::create([
                        'package_id' => $pkg->id,
                        'title'      => $title,
                        'type'       => 'included',
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        // ─── Portfolios ───────────────────────────────────────────────────────
        $portfolios = [
            ['slug' => 'go-hair', 'title' => 'GO HAIR - เว็บไซต์ร้านเสริมสวย', 'package_name' => 'Business Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'paintberry', 'title' => 'Paintberry - เว็บไซต์ธุรกิจสี', 'package_name' => 'Starter Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1541123437800-1bb1317badc2?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'mojiko', 'title' => 'MOJIKO - ร้านอาหารญี่ปุ่น', 'package_name' => 'One Page Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'alanna-spa', 'title' => 'ALANNA - สปาและความงาม', 'package_name' => 'Business Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'cg-finance', 'title' => 'CG - ที่ปรึกษาการเงิน', 'package_name' => 'Starter Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'techstart', 'title' => 'TechStart - Startup Technology', 'package_name' => 'Business Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'greenlife', 'title' => 'GreenLife - สินค้าออร์แกนิก', 'package_name' => 'E-Commerce Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'fitzone', 'title' => 'FitZone - ฟิตเนสและสุขภาพ', 'package_name' => 'Starter Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&h=600&fit=crop', 'client_url' => '#'],
            ['slug' => 'artspace', 'title' => 'ArtSpace - แกลเลอรีศิลปะ', 'package_name' => 'One Page Package', 'thumbnail_url' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?w=800&h=600&fit=crop', 'client_url' => '#'],
        ];

        foreach ($portfolios as $i => $p) {
            Portfolio::firstOrCreate(['slug' => $p['slug']], array_merge($p, [
                'status'     => 'active',
                'sort_order' => $i,
            ]));
        }

        // ─── Work Steps ───────────────────────────────────────────────────────
        $steps = [
            ['step_number' => 1, 'title' => 'รับ Brief & วิเคราะห์', 'description' => 'พูดคุยทำความเข้าใจธุรกิจ เป้าหมาย และกลุ่มเป้าหมายของคุณ เพื่อวางแผนเว็บไซต์ที่ตรงจุด', 'icon_name' => 'chat', 'sort_order' => 0],
            ['step_number' => 2, 'title' => 'ออกแบบ UI/UX', 'description' => 'ทีมดีไซน์สร้าง Mockup และ Prototype ให้คุณเห็นภาพก่อนพัฒนาจริง พร้อมปรับแก้ตามความต้องการ', 'icon_name' => 'design', 'sort_order' => 1],
            ['step_number' => 3, 'title' => 'พัฒนาและทดสอบ', 'description' => 'โปรแกรมเมอร์มืออาชีพลงมือพัฒนาด้วยเทคโนโลยีล่าสุด ทดสอบทุกฟีเจอร์ให้สมบูรณ์ก่อนส่งมอบ', 'icon_name' => 'code', 'sort_order' => 2],
            ['step_number' => 4, 'title' => 'ส่งมอบและดูแล', 'description' => 'ส่งมอบเว็บไซต์พร้อมอบรมการใช้งาน พร้อม After-Sales Support ดูแลตลอดระยะเวลารับประกัน', 'icon_name' => 'rocket', 'sort_order' => 3],
        ];

        WorkStep::truncate();
        foreach ($steps as $s) {
            WorkStep::create($s);
        }

        // ─── Why Us Items ─────────────────────────────────────────────────────
        $whyUs = [
            ['icon_name' => 'star', 'title' => 'ประสบการณ์กว่า 10 ปี', 'description' => 'ทีมงานมืออาชีพที่มีประสบการณ์ทำเว็บไซต์มากกว่า 10 ปี ผ่านโปรเจกต์มาแล้วกว่า 200+ งาน', 'sort_order' => 0],
            ['icon_name' => 'shield', 'title' => 'ราคาโปร่งใส ไม่มีค่าใช้จ่ายซ่อน', 'description' => 'กำหนดราคาชัดเจน ไม่มีค่าใช้จ่ายแอบแฝง คุณรู้ค่าใช้จ่ายทั้งหมดตั้งแต่เริ่มต้น', 'sort_order' => 1],
            ['icon_name' => 'clock', 'title' => 'ส่งงานตรงเวลา', 'description' => 'เราให้ความสำคัญกับ Deadline ของลูกค้า บริหารโปรเจกต์อย่างมีระบบ ส่งมอบงานตรงเวลาทุกครั้ง', 'sort_order' => 2],
            ['icon_name' => 'support', 'title' => 'After-Sales Support ตลอดการใช้งาน', 'description' => 'ดูแลลูกค้าแม้หลังส่งมอบงาน พร้อมตอบคำถามและช่วยแก้ปัญหาตลอดระยะเวลารับประกัน', 'sort_order' => 3],
            ['icon_name' => 'globe', 'title' => 'SEO-Ready ทุกโปรเจกต์', 'description' => 'ทุกเว็บไซต์ที่เราทำผ่านการ Optimize SEO พื้นฐาน ให้ Google index ได้ง่ายและโหลดเร็ว', 'sort_order' => 4],
            ['icon_name' => 'chart', 'title' => 'วัดผลได้จริง', 'description' => 'ติดตั้ง Analytics ให้ทุกโปรเจกต์ คุณสามารถติดตาม Traffic, Conversion และผลลัพธ์ทางธุรกิจได้ชัดเจน', 'sort_order' => 5],
        ];

        WhyUsItem::truncate();
        foreach ($whyUs as $w) {
            WhyUsItem::create(array_merge($w, ['status' => 'active']));
        }

        // ─── Partners ─────────────────────────────────────────────────────────
        $partnersData = [
            ['name' => 'GO HAIR', 'website_url' => '#', 'logo_url' => null, 'sort_order' => 0],
            ['name' => 'Paintberry', 'website_url' => '#', 'logo_url' => null, 'sort_order' => 1],
            ['name' => 'Rabbit', 'website_url' => '#', 'logo_url' => null, 'sort_order' => 2],
            ['name' => 'MOJIKO', 'website_url' => '#', 'logo_url' => null, 'sort_order' => 3],
            ['name' => 'ALANNA', 'website_url' => '#', 'logo_url' => null, 'sort_order' => 4],
            ['name' => 'CG Greenzyme', 'website_url' => '#', 'logo_url' => null, 'sort_order' => 5],
        ];

        foreach ($partnersData as $p) {
            Partner::firstOrCreate(['name' => $p['name']], array_merge($p, ['status' => 'active']));
        }

        // ─── Testimonials ─────────────────────────────────────────────────────
        $testimonials = [
            [
                'customer_name'  => 'คุณสมชาย วงศ์ดี',
                'customer_title' => 'เจ้าของร้าน GO HAIR',
                'avatar_color'   => '#7c1d1d',
                'review_text'    => 'ทีม DOSC ทำเว็บให้ร้านผมสวยมาก ลูกค้าโทรมาถามว่าเว็บทำที่ไหน ยอดจองออนไลน์เพิ่มขึ้นกว่า 40% หลังเปิดเว็บใหม่ ประทับใจมากครับ',
                'rating'         => 5,
                'source'         => 'google',
                'sort_order'     => 0,
            ],
            [
                'customer_name'  => 'คุณนภา สมิทธ์',
                'customer_title' => 'ผู้จัดการ ALANNA Spa',
                'avatar_color'   => '#1d4ed8',
                'review_text'    => 'ประทับใจมากเลยค่ะ ทีมงานใจเย็น อธิบายเข้าใจง่าย แก้ไขรวดเร็ว เว็บออกมาสวยงามเกินคาด และยังช่วยสอนการใช้งานจนเข้าใจด้วย',
                'rating'         => 5,
                'source'         => 'facebook',
                'sort_order'     => 1,
            ],
            [
                'customer_name'  => 'คุณธนกฤต ปิยะวงค์',
                'customer_title' => 'CEO TechStart Thailand',
                'avatar_color'   => '#065f46',
                'review_text'    => 'ราคาสมเหตุสมผล งานออกมาดีมาก ส่งงานตรงเวลา ทีม DOSC เป็นมืออาชีพจริงๆ ครับ ใครมองหาคนทำเว็บแนะนำที่นี่เลย',
                'rating'         => 5,
                'source'         => 'google',
                'sort_order'     => 2,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(
                ['customer_name' => $t['customer_name']],
                array_merge($t, ['status' => 'active'])
            );
        }

        // ─── FAQ ──────────────────────────────────────────────────────────────
        $faqs = [
            ['question' => 'ทำเว็บไซต์กี่วันถึงจะเสร็จ?', 'answer' => 'ขึ้นอยู่กับขนาดและความซับซ้อนของโปรเจกต์ โดยประมาณ One Page: 7 วัน, Starter: 14 วัน, Business: 21 วัน, E-Commerce: 30 วัน นับจากวันที่ได้รับข้อมูลครบถ้วน'],
            ['question' => 'ราคาที่แสดงรวม VAT แล้วหรือไม่?', 'answer' => 'ราคาที่แสดงยังไม่รวม VAT 7% โดยราคารวม VAT จะคำนวณในขั้นตอนออกใบเสนอราคา ติดต่อเราเพื่อรับใบเสนอราคาฟรีได้เลย'],
            ['question' => 'หลังส่งมอบงาน มีบริการดูแลหลังการขายไหม?', 'answer' => 'ทุกแพ็กเกจมีรับประกันการทำงานหลังส่งมอบ ตามระยะเวลาที่กำหนดในแต่ละแพ็กเกจ นอกจากนี้ยังมีบริการ Maintenance รายเดือนสำหรับผู้ที่ต้องการดูแลอย่างต่อเนื่อง'],
            ['question' => 'สามารถแก้ไขข้อมูลบนเว็บไซต์เองได้ไหม?', 'answer' => 'ได้เลยครับ ทุกเว็บไซต์ที่เราทำมีระบบจัดการเนื้อหา (CMS) ให้คุณอัปเดตข้อมูล รูปภาพ และบทความได้เองโดยไม่ต้องมีความรู้ด้านโค้ด พร้อมคู่มือและวิดีโอสอนการใช้งาน'],
            ['question' => 'ถ้าต้องการเว็บไซต์ที่มีฟีเจอร์พิเศษ สามารถขอ Custom ได้ไหม?', 'answer' => 'ได้ครับ เราพัฒนาฟีเจอร์พิเศษตามความต้องการของลูกค้าได้ เช่น ระบบจองออนไลน์ ระบบสมาชิก ระบบชำระเงิน ฯลฯ ติดต่อเราเพื่อขอใบเสนอราคาเฉพาะ'],
            ['question' => 'โฮสติ้งและโดเมนรวมอยู่ในราคาหรือเปล่า?', 'answer' => 'ราคาแพ็กเกจไม่รวมค่าโฮสติ้งและโดเมน แต่เราช่วยแนะนำและดูแลการติดตั้งให้ โดยค่าโดเมน .com อยู่ที่ประมาณ 300-500 บาท/ปี และโฮสติ้งขึ้นอยู่กับขนาดของเว็บไซต์'],
            ['question' => 'รับชำระเงินผ่านช่องทางใดบ้าง?', 'answer' => 'รับชำระผ่านการโอนเงินธนาคาร (กสิกรไทย, ไทยพาณิชย์, กรุงไทย) และ PromptPay โดยมีการแบ่งชำระ 50% เริ่มงาน และ 50% ส่งมอบงาน'],
        ];

        Faq::truncate();
        foreach ($faqs as $i => $f) {
            Faq::create(array_merge($f, ['status' => 'active', 'sort_order' => $i]));
        }

        // ─── Blog Posts ───────────────────────────────────────────────────────
        // Only create if blog table is empty
        if (\App\Models\Blog::count() === 0) {
            $blogs = [
                [
                    'title'        => '5 เหตุผลที่ธุรกิจ SME ต้องมีเว็บไซต์ในปี 2025',
                    'slug'         => '5-reasons-sme-need-website-2025',
                    'excerpt'      => 'ในยุคที่ดิจิทัลเข้ามามีบทบาทสำคัญต่อการตัดสินใจซื้อของผู้บริโภค การมีเว็บไซต์ที่ดีกลายเป็นสิ่งจำเป็นสำหรับธุรกิจทุกขนาด',
                    'cover_image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=500&fit=crop',
                    'status'       => 'published',
                    'published_at' => now()->subDays(2),
                ],
                [
                    'title'        => 'เทคนิค SEO ปี 2025 ที่เจ้าของเว็บไซต์ต้องรู้',
                    'slug'         => 'seo-techniques-2025',
                    'excerpt'      => 'Google อัปเดต Algorithm บ่อยครั้ง เทคนิค SEO ที่ใช้ได้ผลในปี 2025 มีอะไรบ้าง มาดูกันว่าควรโฟกัสที่อะไร',
                    'cover_image_url' => 'https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=800&h=500&fit=crop',
                    'status'       => 'published',
                    'published_at' => now()->subDays(7),
                ],
                [
                    'title'        => 'วิธีเลือก Package ทำเว็บไซต์ให้เหมาะกับธุรกิจของคุณ',
                    'slug'         => 'how-to-choose-web-package',
                    'excerpt'      => 'มี Package ให้เลือกหลายแบบ จะรู้ได้อย่างไรว่าอันไหนเหมาะกับธุรกิจของเรา บทความนี้จะช่วยให้คุณตัดสินใจได้ง่ายขึ้น',
                    'cover_image_url' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&h=500&fit=crop',
                    'status'       => 'published',
                    'published_at' => now()->subDays(14),
                ],
            ];

            foreach ($blogs as $b) {
                \App\Models\Blog::create(array_merge($b, [
                    'content'          => '<p>' . $b['excerpt'] . '</p>',
                    'uuid'             => \Illuminate\Support\Str::uuid(),
                    'is_featured'      => false,
                ]));
            }
        }

        $this->command->info('Home content seeded successfully!');
    }
}
