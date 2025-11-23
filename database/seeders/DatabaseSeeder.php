<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\LoyaltyService;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ==================== USERS (50 bản ghi) ====================
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Kiều Kiến Quốc',
            'email' => 'kieukienquocvn@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userNames = [
            'Nguyễn Văn Hoàng',
            'Trần Thị Lan Anh',
            'Lê Minh Tuấn',
            'Phạm Thị Hương',
            'Hoàng Văn Nam',
            'Vũ Thị Mai',
            'Đặng Quốc Bảo',
            'Bùi Thị Ngọc',
            'Ngô Văn Thắng',
            'Lý Thị Thanh',
            'Phan Minh Đức',
            'Trịnh Thị Hà',
            'Hồ Văn Phong',
            'Dương Thị Linh',
            'Cao Văn Khoa',
            'Lâm Thị Xuân',
            'Tạ Văn Hải',
            'Phùng Thị Yến',
            'Đỗ Văn Long',
            'Nguyễn Thị Thảo',
            'Trần Văn Tùng',
            'Lê Thị Hồng',
            'Phạm Văn Đạt',
            'Hoàng Thị Kim',
            'Vũ Văn Hiếu',
            'Đặng Thị Phương',
            'Bùi Văn Tân',
            'Ngô Thị Nga',
            'Lý Văn Sơn',
            'Phan Thị Dung',
            'Trịnh Văn Hùng',
            'Hồ Thị Vân',
            'Dương Văn Quý',
            'Cao Thị Nhung',
            'Lâm Văn Thịnh',
            'Tạ Thị Thu',
            'Phùng Văn Thành',
            'Đỗ Thị Loan',
            'Nguyễn Văn Bình',
            'Trần Thị Oanh',
            'Lê Văn Duy',
            'Phạm Thị Hạnh',
            'Hoàng Văn Lộc',
            'Vũ Thị Mỹ',
            'Đặng Văn Tài',
            'Bùi Thị Hằng',
            'Ngô Văn Toàn',
            'Lý Thị Quỳnh',
            'Phan Văn Kiên',
            'Trịnh Thị Huyền'
        ];

        $userIds = [];
        foreach ($userNames as $index => $name) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => 'user' . ($index + 1) . '@fashionoffice.vn',
                'password' => Hash::make('User@2025'),
                'role' => 'user',
                'status' => $index % 10 == 0 ? 'inactive' : 'active',
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $shipperNames = [
            'Đinh Văn Lực',
            'Lưu Thị Thanh Hà',
            'Mai Quốc Huy',
            'Phạm Đức Tín',
            'Vương Thị Mỹ Linh',
        ];

        $shipperUserIds = [];
        foreach ($shipperNames as $index => $name) {
            $shipperUserIds[] = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => 'shipper' . ($index + 1) . '@fashionoffice.vn',
                'password' => Hash::make('Shipper@2025'),
                'role' => 'shipper',
                'status' => 'active',
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);
        }

        $shipperProfileIds = [];
        foreach ($shipperUserIds as $index => $userId) {
            $shipperProfileIds[] = DB::table('shippers')->insertGetId([
                'user_id' => $userId,
                'phone' => '09' . rand(10000000, 99999999),
                'vehicle_type' => ['Xe máy', 'Ô tô bán tải', 'Xe tải nhỏ'][rand(0, 2)],
                'vehicle_plate' => '51H-' . rand(10000, 99999),
                'trust_score' => number_format(rand(45, 65) / 10, 2),
                'completed_deliveries' => 0,
                'cancelled_deliveries' => 0,
                'average_rating' => 0,
                'is_available' => true,
                'bio' => 'Tài xế chuyên nghiệp với hơn ' . rand(1, 5) . ' năm kinh nghiệm giao hàng.',
                'metadata' => json_encode(['preferred_area' => ['Nội thành', 'Ngoại thành', 'Liên tỉnh'][rand(0, 2)]]),
                'created_at' => now()->subDays(rand(10, 60)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);
        }

        // ==================== BRANDS (50 bản ghi) ====================
        $brands = [
            'Owen',
            'Aristino',
            'Pierre Cardin',
            'The Suit',
            'Routine',
            'Blue Exchange',
            'Mon Amie',
            'Canifa Office',
            'Mango Man',
            'Massimo Dutti',
            'Hugo Boss',
            'Armani Exchange',
            'Calvin Klein',
            'Tommy Hilfiger',
            'Ralph Lauren',
            'Brooks Brothers',
            'Charles Tyrwhitt',
            'TM Lewin',
            'Reiss',
            'Ted Baker',
            'Banana Republic',
            'J.Crew',
            'Club Monaco',
            'COS',
            'Sandro',
            'The Kooples',
            'AllSaints',
            'Topman',
            'River Island',
            'Next',
            'Marks & Spencer',
            'H&M Premium',
            'Zara Man',
            'Uniqlo Business',
            'Muji',
            'Everlane',
            'Bonobos',
            'Frank & Oak',
            'Ministry of Supply',
            'Suitsupply',
            'Indochino',
            'Black Lapel',
            'Spier & Mackay',
            'Proper Cloth',
            'Ratio Clothing',
            'Alton Lane',
            'Oliver Wicks',
            'Hockerty',
            'Lanieri',
            'Ermenegildo Zegna'
        ];

        $brandIds = [];
        foreach ($brands as $brand) {
            $brandIds[] = DB::table('brands')->insertGetId([
                'title' => $brand,
                'slug' => Str::slug($brand),
                'status' => 'active',
                'created_at' => now()->subDays(rand(30, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // ==================== BANNERS (5 bản ghi) ====================
        $banners = [
            ['title' => 'Bộ Sưu Tập Vest Xuân Hè 2025', 'description' => 'Giảm giá 25% cho tất cả vest nam cao cấp'],
            ['title' => 'Áo Sơ Mi Công Sở Mới Nhất', 'description' => 'Chất liệu cotton cao cấp, thiết kế hiện đại'],
            ['title' => 'Giày Da Nam Chính Hãng', 'description' => 'Giảm giá đến 40% cho toàn bộ giày da công sở'],
            ['title' => 'Phụ Kiện Đẳng Cấp', 'description' => 'Cà vạt, thắt lưng, ví da - Miễn phí vận chuyển'],
            ['title' => 'Sale Cuối Năm 2025', 'description' => 'Ưu đãi đặc biệt lên đến 50% toàn bộ sản phẩm']
        ];

        foreach ($banners as $index => $banner) {
            DB::table('banners')->insert([
                'title' => $banner['title'],
                'slug' => Str::slug($banner['title']),
                'description' => $banner['description'],
                'photo' => '/storage/photos/1/Banner/banner-0' . ($index + 1) . '.jpg',
                'status' => 'active',
                'created_at' => now()->subDays(rand(10, 60)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);
        }

        // ==================== CATEGORIES - Parent (10 bản ghi) ====================
        $parentCategories = [
            ['title' => 'Vest Nam Công Sở', 'summary' => 'Vest cao cấp cho doanh nhân và văn phòng'],
            ['title' => 'Áo Sơ Mi Nam', 'summary' => 'Áo sơ mi công sở lịch lãm, sang trọng'],
            ['title' => 'Quần Âu Nam', 'summary' => 'Quần tây công sở chất liệu cao cấp'],
            ['title' => 'Áo Khoác Nam', 'summary' => 'Áo khoác vest, blazer thời trang'],
            ['title' => 'Giày Da Nam', 'summary' => 'Giày tây công sở da thật chính hãng'],
            ['title' => 'Phụ Kiện Nam', 'summary' => 'Cà vạt, thắt lưng, ví da cao cấp'],
            ['title' => 'Váy Công Sở Nữ', 'summary' => 'Váy công sở thanh lịch, chuyên nghiệp'],
            ['title' => 'Áo Kiểu Nữ', 'summary' => 'Áo kiều, áo sơ mi nữ công sở'],
            ['title' => 'Giày Cao Gót', 'summary' => 'Giày cao gót công sở sang trọng'],
            ['title' => 'Túi Xách Công Sở', 'summary' => 'Túi xách da cao cấp cho doanh nhân']
        ];

        $parentCategoryIds = [];
        foreach ($parentCategories as $index => $category) {
            $parentCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => $category['title'],
                'slug' => Str::slug($category['title']),
                'summary' => $category['summary'],
                'photo' => '/storage/photos/1/Category/mini-banner' . ($index + 1) . '.jpg',
                'is_parent' => 1,
                'parent_id' => null,
                'added_by' => $adminId,
                'status' => 'active',
                'created_at' => now()->subDays(rand(60, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // ==================== CATEGORIES - Child (20 bản ghi) ====================
        $childCategories = [
            ['title' => 'Vest Một Hàng Khuy', 'parent_id' => $parentCategoryIds[0]],
            ['title' => 'Vest Hai Hàng Khuy', 'parent_id' => $parentCategoryIds[0]],
            ['title' => 'Áo Sơ Mi Trắng', 'parent_id' => $parentCategoryIds[1]],
            ['title' => 'Áo Sơ Mi Kẻ Sọc', 'parent_id' => $parentCategoryIds[1]],
            ['title' => 'Quần Âu Ống Đứng', 'parent_id' => $parentCategoryIds[2]],
            ['title' => 'Quần Âu Ống Côn', 'parent_id' => $parentCategoryIds[2]],
            ['title' => 'Áo Blazer Nam', 'parent_id' => $parentCategoryIds[3]],
            ['title' => 'Áo Cardigan Nam', 'parent_id' => $parentCategoryIds[3]],
            ['title' => 'Giày Oxford', 'parent_id' => $parentCategoryIds[4]],
            ['title' => 'Giày Derby', 'parent_id' => $parentCategoryIds[4]],
            ['title' => 'Cà Vạt Lụa', 'parent_id' => $parentCategoryIds[5]],
            ['title' => 'Thắt Lưng Da', 'parent_id' => $parentCategoryIds[5]],
            ['title' => 'Váy Bút Chì', 'parent_id' => $parentCategoryIds[6]],
            ['title' => 'Váy Xòe Công Sở', 'parent_id' => $parentCategoryIds[6]],
            ['title' => 'Áo Sơ Mi Nữ', 'parent_id' => $parentCategoryIds[7]],
            ['title' => 'Áo Kiểu Lụa', 'parent_id' => $parentCategoryIds[7]],
            ['title' => 'Giày Cao Gót 5cm', 'parent_id' => $parentCategoryIds[8]],
            ['title' => 'Giày Cao Gót 7cm', 'parent_id' => $parentCategoryIds[8]],
            ['title' => 'Túi Xách Tay', 'parent_id' => $parentCategoryIds[9]],
            ['title' => 'Cặp Laptop Da', 'parent_id' => $parentCategoryIds[9]]
        ];

        $childCategoryIds = [];
        foreach ($childCategories as $index => $category) {
            $childCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => $category['title'],
                'slug' => Str::slug($category['title']),
                'summary' => 'Sản phẩm ' . $category['title'] . ' chất lượng cao',
                'photo' => '/storage/photos/1/Category/subcat_' . ($index + 1) . '.jpg',
                'is_parent' => 0,
                'parent_id' => $category['parent_id'],
                'added_by' => $adminId,
                'status' => 'active',
                'created_at' => now()->subDays(rand(30, 180)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // ==================== PRODUCTS (50 bản ghi) ====================
        $products = [
            // Vest Nam
            ['title' => 'Vest Nam Owen Cao Cấp VN001', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[0], 'brand_id' => $brandIds[0], 'price' => 4500000, 'discount' => 15, 'stock' => 45, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Vest Aristino Slim Fit VN002', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[0], 'brand_id' => $brandIds[1], 'price' => 5200000, 'discount' => 20, 'stock' => 38, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Vest Pierre Cardin Xám VN003', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[1], 'brand_id' => $brandIds[2], 'price' => 6800000, 'discount' => 10, 'stock' => 25, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Vest The Suit Navy VN004', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[1], 'brand_id' => $brandIds[3], 'price' => 5500000, 'discount' => 18, 'stock' => 32, 'condition' => 'hot', 'is_featured' => false],
            ['title' => 'Vest Routine Đen VN005', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[0], 'brand_id' => $brandIds[4], 'price' => 4200000, 'discount' => null, 'stock' => 50, 'condition' => 'default', 'is_featured' => false],

            // Áo Sơ Mi Nam
            ['title' => 'Áo Sơ Mi Trắng Owen SM001', 'cat_id' => $parentCategoryIds[1], 'child_cat_id' => $childCategoryIds[2], 'brand_id' => $brandIds[0], 'price' => 850000, 'discount' => 10, 'stock' => 120, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Áo Sơ Mi Xanh Navy Aristino SM002', 'cat_id' => $parentCategoryIds[1], 'child_cat_id' => $childCategoryIds[2], 'brand_id' => $brandIds[1], 'price' => 920000, 'discount' => 15, 'stock' => 95, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Áo Sơ Mi Kẻ Sọc Pierre SM003', 'cat_id' => $parentCategoryIds[1], 'child_cat_id' => $childCategoryIds[3], 'brand_id' => $brandIds[2], 'price' => 1150000, 'discount' => 12, 'stock' => 78, 'condition' => 'new', 'is_featured' => false],
            ['title' => 'Áo Sơ Mi Hồng Routine SM004', 'cat_id' => $parentCategoryIds[1], 'child_cat_id' => $childCategoryIds[2], 'brand_id' => $brandIds[4], 'price' => 780000, 'discount' => null, 'stock' => 110, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Áo Sơ Mi Kẻ Ca Rô Blue Exchange SM005', 'cat_id' => $parentCategoryIds[1], 'child_cat_id' => $childCategoryIds[3], 'brand_id' => $brandIds[5], 'price' => 890000, 'discount' => 8, 'stock' => 88, 'condition' => 'new', 'is_featured' => false],

            // Quần Âu Nam
            ['title' => 'Quần Âu Xám Owen QA001', 'cat_id' => $parentCategoryIds[2], 'child_cat_id' => $childCategoryIds[4], 'brand_id' => $brandIds[0], 'price' => 1200000, 'discount' => 15, 'stock' => 85, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Quần Âu Navy Aristino QA002', 'cat_id' => $parentCategoryIds[2], 'child_cat_id' => $childCategoryIds[4], 'brand_id' => $brandIds[1], 'price' => 1350000, 'discount' => 10, 'stock' => 72, 'condition' => 'new', 'is_featured' => false],
            ['title' => 'Quần Âu Đen Pierre QA003', 'cat_id' => $parentCategoryIds[2], 'child_cat_id' => $childCategoryIds[5], 'brand_id' => $brandIds[2], 'price' => 1580000, 'discount' => 18, 'stock' => 64, 'condition' => 'hot', 'is_featured' => false],
            ['title' => 'Quần Âu Ống Côn The Suit QA004', 'cat_id' => $parentCategoryIds[2], 'child_cat_id' => $childCategoryIds[5], 'brand_id' => $brandIds[3], 'price' => 1450000, 'discount' => null, 'stock' => 90, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Quần Âu Be Routine QA005', 'cat_id' => $parentCategoryIds[2], 'child_cat_id' => $childCategoryIds[4], 'brand_id' => $brandIds[4], 'price' => 1180000, 'discount' => 12, 'stock' => 75, 'condition' => 'new', 'is_featured' => false],

            // Áo Khoác Nam
            ['title' => 'Áo Blazer Xanh Owen AK001', 'cat_id' => $parentCategoryIds[3], 'child_cat_id' => $childCategoryIds[6], 'brand_id' => $brandIds[0], 'price' => 2800000, 'discount' => 20, 'stock' => 42, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Áo Blazer Xám Aristino AK002', 'cat_id' => $parentCategoryIds[3], 'child_cat_id' => $childCategoryIds[6], 'brand_id' => $brandIds[1], 'price' => 3200000, 'discount' => 15, 'stock' => 35, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Áo Cardigan Pierre AK003', 'cat_id' => $parentCategoryIds[3], 'child_cat_id' => $childCategoryIds[7], 'brand_id' => $brandIds[2], 'price' => 1980000, 'discount' => null, 'stock' => 58, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Áo Blazer Đen The Suit AK004', 'cat_id' => $parentCategoryIds[3], 'child_cat_id' => $childCategoryIds[6], 'brand_id' => $brandIds[3], 'price' => 2950000, 'discount' => 18, 'stock' => 48, 'condition' => 'hot', 'is_featured' => false],
            ['title' => 'Áo Cardigan Xám Routine AK005', 'cat_id' => $parentCategoryIds[3], 'child_cat_id' => $childCategoryIds[7], 'brand_id' => $brandIds[4], 'price' => 1750000, 'discount' => 10, 'stock' => 62, 'condition' => 'new', 'is_featured' => false],

            // Giày Da Nam
            ['title' => 'Giày Oxford Đen Owen GD001', 'cat_id' => $parentCategoryIds[4], 'child_cat_id' => $childCategoryIds[8], 'brand_id' => $brandIds[0], 'price' => 2200000, 'discount' => 25, 'stock' => 55, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Giày Oxford Nâu Aristino GD002', 'cat_id' => $parentCategoryIds[4], 'child_cat_id' => $childCategoryIds[8], 'brand_id' => $brandIds[1], 'price' => 2450000, 'discount' => 20, 'stock' => 48, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Giày Derby Đen Pierre GD003', 'cat_id' => $parentCategoryIds[4], 'child_cat_id' => $childCategoryIds[9], 'brand_id' => $brandIds[2], 'price' => 2850000, 'discount' => 15, 'stock' => 38, 'condition' => 'new', 'is_featured' => false],
            ['title' => 'Giày Derby Nâu The Suit GD004', 'cat_id' => $parentCategoryIds[4], 'child_cat_id' => $childCategoryIds[9], 'brand_id' => $brandIds[3], 'price' => 2650000, 'discount' => null, 'stock' => 52, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Giày Oxford Xám Routine GD005', 'cat_id' => $parentCategoryIds[4], 'child_cat_id' => $childCategoryIds[8], 'brand_id' => $brandIds[4], 'price' => 2100000, 'discount' => 18, 'stock' => 60, 'condition' => 'new', 'is_featured' => false],

            // Phụ Kiện Nam
            ['title' => 'Cà Vạt Lụa Đỏ Owen CV001', 'cat_id' => $parentCategoryIds[5], 'child_cat_id' => $childCategoryIds[10], 'brand_id' => $brandIds[0], 'price' => 450000, 'discount' => 10, 'stock' => 150, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Cà Vạt Lụa Xanh Aristino CV002', 'cat_id' => $parentCategoryIds[5], 'child_cat_id' => $childCategoryIds[10], 'brand_id' => $brandIds[1], 'price' => 520000, 'discount' => 15, 'stock' => 135, 'condition' => 'hot', 'is_featured' => false],
            ['title' => 'Thắt Lưng Da Đen Pierre TL001', 'cat_id' => $parentCategoryIds[5], 'child_cat_id' => $childCategoryIds[11], 'brand_id' => $brandIds[2], 'price' => 980000, 'discount' => 20, 'stock' => 92, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Thắt Lưng Da Nâu The Suit TL002', 'cat_id' => $parentCategoryIds[5], 'child_cat_id' => $childCategoryIds[11], 'brand_id' => $brandIds[3], 'price' => 850000, 'discount' => null, 'stock' => 108, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Ví Da Nam Đen Routine VI001', 'cat_id' => $parentCategoryIds[5], 'child_cat_id' => $childCategoryIds[11], 'brand_id' => $brandIds[4], 'price' => 680000, 'discount' => 12, 'stock' => 125, 'condition' => 'new', 'is_featured' => false],

            // Váy Công Sở Nữ
            ['title' => 'Váy Bút Chì Đen Mon Amie VCS001', 'cat_id' => $parentCategoryIds[6], 'child_cat_id' => $childCategoryIds[12], 'brand_id' => $brandIds[6], 'price' => 1350000, 'discount' => 18, 'stock' => 68, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Váy Bút Chì Navy Canifa VCS002', 'cat_id' => $parentCategoryIds[6], 'child_cat_id' => $childCategoryIds[12], 'brand_id' => $brandIds[7], 'price' => 1150000, 'discount' => 15, 'stock' => 82, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Váy Xòe Xám Mango VCS003', 'cat_id' => $parentCategoryIds[6], 'child_cat_id' => $childCategoryIds[13], 'brand_id' => $brandIds[8], 'price' => 1580000, 'discount' => 20, 'stock' => 55, 'condition' => 'hot', 'is_featured' => false],
            ['title' => 'Váy Xòe Be Massimo VCS004', 'cat_id' => $parentCategoryIds[6], 'child_cat_id' => $childCategoryIds[13], 'brand_id' => $brandIds[9], 'price' => 1720000, 'discount' => null, 'stock' => 48, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Váy Bút Chì Xanh Mon Amie VCS005', 'cat_id' => $parentCategoryIds[6], 'child_cat_id' => $childCategoryIds[12], 'brand_id' => $brandIds[6], 'price' => 1280000, 'discount' => 10, 'stock' => 72, 'condition' => 'new', 'is_featured' => false],

            // Áo Kiểu Nữ
            ['title' => 'Áo Sơ Mi Nữ Trắng Mon Amie AK001', 'cat_id' => $parentCategoryIds[7], 'child_cat_id' => $childCategoryIds[14], 'brand_id' => $brandIds[6], 'price' => 880000, 'discount' => 12, 'stock' => 98, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Áo Kiểu Lụa Hồng Canifa AK002', 'cat_id' => $parentCategoryIds[7], 'child_cat_id' => $childCategoryIds[15], 'brand_id' => $brandIds[7], 'price' => 1120000, 'discount' => 18, 'stock' => 75, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Áo Sơ Mi Nữ Navy Mango AK003', 'cat_id' => $parentCategoryIds[7], 'child_cat_id' => $childCategoryIds[14], 'brand_id' => $brandIds[8], 'price' => 1050000, 'discount' => 15, 'stock' => 85, 'condition' => 'new', 'is_featured' => false],
            ['title' => 'Áo Kiểu Lụa Xanh Massimo AK004', 'cat_id' => $parentCategoryIds[7], 'child_cat_id' => $childCategoryIds[15], 'brand_id' => $brandIds[9], 'price' => 1280000, 'discount' => null, 'stock' => 68, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Áo Sơ Mi Nữ Kẻ Sọc Mon Amie AK005', 'cat_id' => $parentCategoryIds[7], 'child_cat_id' => $childCategoryIds[14], 'brand_id' => $brandIds[6], 'price' => 920000, 'discount' => 10, 'stock' => 92, 'condition' => 'new', 'is_featured' => false],

            // Giày Cao Gót Nữ
            ['title' => 'Giày Cao Gót 5cm Đen Mon Amie GN001', 'cat_id' => $parentCategoryIds[8], 'child_cat_id' => $childCategoryIds[16], 'brand_id' => $brandIds[6], 'price' => 1450000, 'discount' => 20, 'stock' => 62, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Giày Cao Gót 7cm Nude Canifa GN002', 'cat_id' => $parentCategoryIds[8], 'child_cat_id' => $childCategoryIds[17], 'brand_id' => $brandIds[7], 'price' => 1680000, 'discount' => 15, 'stock' => 54, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Giày Cao Gót 5cm Nâu Mango GN003', 'cat_id' => $parentCategoryIds[8], 'child_cat_id' => $childCategoryIds[16], 'brand_id' => $brandIds[8], 'price' => 1580000, 'discount' => 18, 'stock' => 58, 'condition' => 'hot', 'is_featured' => false],
            ['title' => 'Giày Cao Gót 7cm Đen Massimo GN004', 'cat_id' => $parentCategoryIds[8], 'child_cat_id' => $childCategoryIds[17], 'brand_id' => $brandIds[9], 'price' => 1850000, 'discount' => null, 'stock' => 45, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Giày Cao Gót 5cm Xám Mon Amie GN005', 'cat_id' => $parentCategoryIds[8], 'child_cat_id' => $childCategoryIds[16], 'brand_id' => $brandIds[6], 'price' => 1380000, 'discount' => 12, 'stock' => 70, 'condition' => 'new', 'is_featured' => false],

            // Túi Xách Công Sở
            ['title' => 'Túi Xách Tay Da Đen Mon Amie TX001', 'cat_id' => $parentCategoryIds[9], 'child_cat_id' => $childCategoryIds[18], 'brand_id' => $brandIds[6], 'price' => 2850000, 'discount' => 25, 'stock' => 38, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Cặp Laptop Da Nâu Canifa TX002', 'cat_id' => $parentCategoryIds[9], 'child_cat_id' => $childCategoryIds[19], 'brand_id' => $brandIds[7], 'price' => 3200000, 'discount' => 20, 'stock' => 32, 'condition' => 'hot', 'is_featured' => true],
            ['title' => 'Túi Xách Tay Da Xám Mango TX003', 'cat_id' => $parentCategoryIds[9], 'child_cat_id' => $childCategoryIds[18], 'brand_id' => $brandIds[8], 'price' => 2680000, 'discount' => 15, 'stock' => 42, 'condition' => 'new', 'is_featured' => false],
            ['title' => 'Cặp Laptop Da Đen Massimo TX004', 'cat_id' => $parentCategoryIds[9], 'child_cat_id' => $childCategoryIds[19], 'brand_id' => $brandIds[9], 'price' => 3450000, 'discount' => null, 'stock' => 28, 'condition' => 'default', 'is_featured' => false],
            ['title' => 'Túi Xách Tay Da Be Mon Amie TX005', 'cat_id' => $parentCategoryIds[9], 'child_cat_id' => $childCategoryIds[18], 'brand_id' => $brandIds[6], 'price' => 2580000, 'discount' => 18, 'stock' => 36, 'condition' => 'new', 'is_featured' => false]
        ];

        $productIds = [];
        foreach ($products as $index => $product) {
            $productIds[] = DB::table('products')->insertGetId([
                'title' => $product['title'],
                'slug' => Str::slug($product['title']),
                'summary' => 'Sản phẩm ' . $product['title'] . ' chất lượng cao, thiết kế hiện đại, phù hợp văn phòng.',
                'description' => 'Mô tả chi tiết: ' . $product['title'] . ' được làm từ chất liệu cao cấp, đảm bảo độ bền và thoải mái khi sử dụng. Thiết kế thanh lịch, sang trọng, phù hợp cho môi trường công sở chuyên nghiệp.',
                'photo' => '/storage/photos/1/Products/product' . ($index + 1) . '.webp',
                'stock' => $product['stock'],
                'size' => in_array($product['cat_id'], [$parentCategoryIds[0], $parentCategoryIds[1], $parentCategoryIds[2], $parentCategoryIds[3]]) ? 'S,M,L,XL,XXL' : (in_array($product['cat_id'], [$parentCategoryIds[4], $parentCategoryIds[8]]) ? '38,39,40,41,42,43' : 'One Size'),
                'condition' => $product['condition'],
                'status' => 'active',
                'price' => number_format($product['price'], 2, '.', ''),
                'discount' => $product['discount'] ? number_format($product['discount'], 2, '.', '') : null,
                'is_featured' => $product['is_featured'],
                'cat_id' => $product['cat_id'],
                'child_cat_id' => $product['child_cat_id'],
                'brand_id' => $product['brand_id'],
                'created_at' => now()->subDays(rand(10, 120)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);
        }

        // ==================== POST_CATEGORIES (10 bản ghi) ====================
        $postCategories = [
            'Xu Hướng Thời Trang',
            'Bí Quyết Phối Đồ',
            'Chăm Sóc Trang Phục',
            'Phong Cách Công Sở',
            'Thời Trang Bền Vững',
            'Tin Tức Ngành',
            'Review Sản Phẩm',
            'Tips Mua Sắm',
            'Sự Kiện Thời Trang',
            'Thương Hiệu Nổi Bật'
        ];

        $postCategoryIds = [];
        foreach ($postCategories as $category) {
            $postCategoryIds[] = DB::table('post_categories')->insertGetId([
                'title' => $category,
                'slug' => Str::slug($category),
                'status' => 'active',
                'created_at' => now()->subDays(rand(60, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // ==================== POST_TAGS (10 bản ghi) ====================
        $postTags = [
            'vest công sở',
            'áo sơ mi',
            'phối đồ nam',
            'phối đồ nữ',
            'giày da',
            'phụ kiện',
            'xu hướng 2025',
            'thời trang bền vững',
            'tips mặc đẹp',
            'chăm sóc quần áo'
        ];

        $postTagIds = [];
        foreach ($postTags as $tag) {
            $postTagIds[] = DB::table('post_tags')->insertGetId([
                'title' => $tag,
                'slug' => Str::slug($tag),
                'status' => 'active',
                'created_at' => now()->subDays(rand(60, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // ==================== POSTS (20 bản ghi) - FIXED VERSION ====================
        $posts = [
            ['title' => '10 Cách Phối Vest Nam Đẹp Nhất 2025', 'post_cat_id' => $postCategoryIds[1], 'post_tag_id' => $postTagIds[0], 'added_by' => $userIds[0], 'tag_slugs' => ['vest-cong-so', 'ao-so-mi', 'phoi-do-nam']],
            ['title' => 'Bí Quyết Chọn Áo Sơ Mi Phù Hợp Dáng Người', 'post_cat_id' => $postCategoryIds[1], 'post_tag_id' => $postTagIds[1], 'added_by' => $userIds[1], 'tag_slugs' => ['ao-so-mi', 'phoi-do-nam', 'phoi-do-nu']],
            ['title' => 'Xu Hướng Thời Trang Công Sở Nam 2025', 'post_cat_id' => $postCategoryIds[0], 'post_tag_id' => $postTagIds[6], 'added_by' => $userIds[2], 'tag_slugs' => ['phoi-do-nam', 'phoi-do-nu', 'giay-da']],
            ['title' => 'Cách Bảo Quản Vest Da Đúng Cách', 'post_cat_id' => $postCategoryIds[2], 'post_tag_id' => $postTagIds[9], 'added_by' => $userIds[3], 'tag_slugs' => ['phoi-do-nu', 'giay-da', 'phu-kien']],
            ['title' => 'Top 5 Thương Hiệu Giày Da Nam Uy Tín', 'post_cat_id' => $postCategoryIds[9], 'post_tag_id' => $postTagIds[4], 'added_by' => $userIds[4], 'tag_slugs' => ['giay-da', 'phu-kien', 'xu-huong-2025']],
            ['title' => 'Phong Cách Công Sở Nữ Thanh Lịch', 'post_cat_id' => $postCategoryIds[3], 'post_tag_id' => $postTagIds[3], 'added_by' => $userIds[5], 'tag_slugs' => ['vest-cong-so', 'ao-so-mi', 'phoi-do-nam']],
            ['title' => 'Review Vest Owen - Có Đáng Đồng Tiền?', 'post_cat_id' => $postCategoryIds[6], 'post_tag_id' => $postTagIds[0], 'added_by' => $userIds[6], 'tag_slugs' => ['ao-so-mi', 'phoi-do-nam', 'phoi-do-nu']],
            ['title' => 'Cách Phối Phụ Kiện Nam Đẳng Cấp', 'post_cat_id' => $postCategoryIds[1], 'post_tag_id' => $postTagIds[5], 'added_by' => $userIds[7], 'tag_slugs' => ['phoi-do-nam', 'phoi-do-nu', 'giay-da']],
            ['title' => 'Thời Trang Bền Vững - Xu Hướng Tương Lai', 'post_cat_id' => $postCategoryIds[4], 'post_tag_id' => $postTagIds[7], 'added_by' => $userIds[8], 'tag_slugs' => ['phoi-do-nu', 'giay-da', 'phu-kien']],
            ['title' => '5 Lỗi Thường Gặp Khi Mặc Vest', 'post_cat_id' => $postCategoryIds[3], 'post_tag_id' => $postTagIds[8], 'added_by' => $userIds[9], 'tag_slugs' => ['giay-da', 'phu-kien', 'tips-mac-dep']],
            ['title' => 'Hướng Dẫn Chọn Size Áo Sơ Mi Chuẩn', 'post_cat_id' => $postCategoryIds[7], 'post_tag_id' => $postTagIds[1], 'added_by' => $userIds[10], 'tag_slugs' => ['vest-cong-so', 'ao-so-mi', 'phoi-do-nam']],
            ['title' => 'Sự Kiện Fashion Week 2025 Highlights', 'post_cat_id' => $postCategoryIds[8], 'post_tag_id' => $postTagIds[6], 'added_by' => $userIds[11], 'tag_slugs' => ['ao-so-mi', 'phoi-do-nam', 'phoi-do-nu']],
            ['title' => 'Quần Âu Nam: Ống Đứng Hay Ống Côn?', 'post_cat_id' => $postCategoryIds[1], 'post_tag_id' => $postTagIds[2], 'added_by' => $userIds[12], 'tag_slugs' => ['phoi-do-nam', 'phoi-do-nu', 'giay-da']],
            ['title' => 'Cách Làm Sạch Giày Da Hiệu Quả', 'post_cat_id' => $postCategoryIds[2], 'post_tag_id' => $postTagIds[9], 'added_by' => $userIds[13], 'tag_slugs' => ['phoi-do-nu', 'giay-da', 'phu-kien']],
            ['title' => 'Aristino vs Pierre Cardin - So Sánh Chi Tiết', 'post_cat_id' => $postCategoryIds[6], 'post_tag_id' => $postTagIds[0], 'added_by' => $userIds[14], 'tag_slugs' => ['giay-da', 'phu-kien', 'xu-huong-2025']],
            ['title' => 'Phối Đồ Công Sở Với Màu Be', 'post_cat_id' => $postCategoryIds[3], 'post_tag_id' => $postTagIds[8], 'added_by' => $userIds[15], 'tag_slugs' => ['vest-cong-so', 'ao-so-mi', 'tips-mac-dep']],
            ['title' => 'Tips Mua Vest Giảm Giá Mùa Sale', 'post_cat_id' => $postCategoryIds[7], 'post_tag_id' => $postTagIds[8], 'added_by' => $userIds[16], 'tag_slugs' => ['ao-so-mi', 'phoi-do-nam', 'tips-mac-dep']],
            ['title' => 'Túi Xách Công Sở Nữ - Chọn Sao Cho Đúng?', 'post_cat_id' => $postCategoryIds[1], 'post_tag_id' => $postTagIds[3], 'added_by' => $userIds[17], 'tag_slugs' => ['phoi-do-nam', 'phoi-do-nu', 'giay-da']],
            ['title' => 'Lịch Sử Phát Triển Của Vest Nam', 'post_cat_id' => $postCategoryIds[5], 'post_tag_id' => $postTagIds[0], 'added_by' => $userIds[18], 'tag_slugs' => ['phoi-do-nu', 'giay-da', 'phu-kien']],
            ['title' => 'Xu Hướng Màu Sắc Thời Trang 2025', 'post_cat_id' => $postCategoryIds[0], 'post_tag_id' => $postTagIds[6], 'added_by' => $userIds[19], 'tag_slugs' => ['giay-da', 'phu-kien', 'xu-huong-2025']]
        ];

        $postIds = [];
        foreach ($posts as $index => $post) {
            $postIds[] = DB::table('posts')->insertGetId([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'summary' => 'Tóm tắt ngắn gọn về ' . $post['title'] . '. Cung cấp thông tin hữu ích cho người đọc.',
                'description' => '<p>Nội dung chi tiết bài viết <strong>' . $post['title'] . '</strong>.</p><p>Bài viết cung cấp những thông tin chuyên sâu, hữu ích về thời trang công sở, giúp bạn nâng cao phong cách và tự tin hơn trong môi trường làm việc chuyên nghiệp.</p>',
                'quote' => 'Thời trang là cách bạn thể hiện bản thân mà không cần nói một lời.',
                'photo' => '/storage/photos/1/Blog/blog' . ($index + 1) . '.jpg',
                'tags' => implode(',', $post['tag_slugs']), // LƯU SLUG TAGS
                'post_cat_id' => $post['post_cat_id'],
                'post_tag_id' => $post['post_tag_id'],
                'added_by' => $post['added_by'],
                'status' => 'active',
                'created_at' => now()->subDays(rand(5, 90)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
        }

        // ==================== MESSAGES (10 bản ghi) ====================
        $messages = [
            ['name' => 'Nguyễn Văn Hoàng', 'subject' => 'Hỏi về vest Owen VN001', 'email' => 'hoang.nv@gmail.com', 'phone' => '0901234567', 'message' => 'Chào shop, tôi muốn hỏi vest Owen VN001 có size XXL không ạ? Và thời gian giao hàng bao lâu?'],
            ['name' => 'Trần Thị Lan', 'subject' => 'Chính sách đổi trả', 'email' => 'lan.tt@yahoo.com', 'phone' => '0912345678', 'message' => 'Shop cho em hỏi chính sách đổi trả trong vòng bao lâu? Em mua áo sơ mi nhưng sợ không vừa size.'],
            ['name' => 'Lê Minh Tuấn', 'subject' => 'Hỏi về giày da', 'email' => 'tuan.lm@outlook.com', 'phone' => '0923456789', 'message' => 'Giày Oxford đen Owen có chất liệu da thật không shop? Có bảo hành không ạ?'],
            ['name' => 'Phạm Thị Hương', 'subject' => 'Yêu cầu tư vấn', 'email' => 'huong.pt@hotmail.com', 'phone' => '0934567890', 'message' => 'Em muốn mua vest cho chồng, anh ấy cao 1m75, nặng 70kg thì nên chọn size nào ạ?'],
            ['name' => 'Hoàng Văn Nam', 'subject' => 'Khiếu nại đơn hàng', 'email' => 'nam.hv@gmail.com', 'phone' => '0945678901', 'message' => 'Đơn hàng ORD-00015 của tôi giao sai màu. Tôi đặt vest xám nhưng nhận được vest đen. Yêu cầu đổi lại.'],
            ['name' => 'Vũ Thị Mai', 'subject' => 'Hỏi về mã giảm giá', 'email' => 'mai.vt@yahoo.com', 'phone' => '0956789012', 'message' => 'Shop có mã giảm giá nào cho khách hàng mới không ạ? Em muốn mua váy công sở.'],
            ['name' => 'Đặng Quốc Bảo', 'subject' => 'Thanh toán thất bại', 'email' => 'bao.dq@gmail.com', 'phone' => '0967890123', 'message' => 'Tôi đã thanh toán qua PayPal nhưng đơn hàng vẫn hiển thị chưa thanh toán. Mã đơn: ORD-00008'],
            ['name' => 'Bùi Thị Ngọc', 'subject' => 'Góp ý sản phẩm', 'email' => 'ngoc.bt@outlook.com', 'phone' => '0978901234', 'message' => 'Shop nên bổ sung thêm màu hồng phấn cho áo sơ mi nữ. Em thấy màu này rất phù hợp công sở.'],
            ['name' => 'Ngô Văn Thắng', 'subject' => 'Hợp tác kinh doanh', 'email' => 'thang.nv@company.vn', 'phone' => '0989012345', 'message' => 'Công ty chúng tôi muốn đặt hàng số lượng lớn vest và áo sơ mi. Vui lòng liên hệ để báo giá.'],
            ['name' => 'Lý Thị Thanh', 'subject' => 'Câu hỏi về vận chuyển', 'email' => 'thanh.lt@gmail.com', 'phone' => '0990123456', 'message' => 'Shop có giao hàng tỉnh không ạ? Em ở Đà Nẵng, muốn mua giày cao gót.']
        ];

        foreach ($messages as $index => $message) {
            DB::table('messages')->insert([
                'name' => $message['name'],
                'subject' => $message['subject'],
                'email' => $message['email'],
                'photo' => null,
                'phone' => $message['phone'],
                'message' => $message['message'],
                'read_at' => $index % 3 == 0 ? now()->subDays(rand(1, 5)) : null,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
        }

        // ==================== SHIPPINGS (Nâng cấp logic) ====================
        $shippings = [
            [
                'code' => 'STD_INNER',
                'type' => 'Giao hàng tiêu chuẩn - Nội thành',
                'service_level' => 'Standard',
                'delivery_zone' => 'Nội thành',
                'pricing_strategy' => 'flat',
                'price' => 30000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => '1-2 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => true,
                'description' => 'Tiết kiệm chi phí, giao hàng trong khung giờ hành chính nội thành.',
                'priority' => 1,
            ],
            [
                'code' => 'STD_OUTER',
                'type' => 'Giao hàng tiêu chuẩn - Ngoại thành',
                'service_level' => 'Standard',
                'delivery_zone' => 'Ngoại thành',
                'pricing_strategy' => 'flat',
                'price' => 50000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => '2-3 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => false,
                'description' => 'Phù hợp cho các quận ngoại thành với chi phí ổn định.',
                'priority' => 2,
            ],
            [
                'code' => 'EXPRESS_INNER',
                'type' => 'Giao hàng nhanh - Nội thành',
                'service_level' => 'Express',
                'delivery_zone' => 'Nội thành',
                'pricing_strategy' => 'flat',
                'price' => 50000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => 'Trong 24 giờ',
                'supports_cod' => true,
                'is_recommended' => true,
                'description' => 'Ưu tiên giao nhanh cho khách hàng nội thành.',
                'priority' => 1,
            ],
            [
                'code' => 'EXPRESS_OUTER',
                'type' => 'Giao hàng nhanh - Ngoại thành',
                'service_level' => 'Express',
                'delivery_zone' => 'Ngoại thành',
                'pricing_strategy' => 'flat',
                'price' => 80000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => '1-2 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => false,
                'description' => 'Giao hàng nhanh cho khu vực ngoại thành, ưu tiên cuối tuần.',
                'priority' => 2,
            ],
            [
                'code' => 'SAME_DAY_2H',
                'type' => 'Giao hàng hỏa tốc - 2 giờ',
                'service_level' => 'Same Day',
                'delivery_zone' => 'Nội thành',
                'pricing_strategy' => 'flat',
                'price' => 100000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => 5000000,
                'estimated_time' => 'Trong 2 giờ',
                'supports_cod' => false,
                'is_recommended' => false,
                'description' => 'Hỏa tốc 2 giờ cho nhu cầu gấp, không áp dụng COD.',
                'priority' => 0,
            ],
            [
                'code' => 'INTER_PROV_SOUTH',
                'type' => 'Giao hàng liên tỉnh - Miền Nam',
                'service_level' => 'Intercity',
                'delivery_zone' => 'Miền Nam',
                'pricing_strategy' => 'flat',
                'price' => 70000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => '3-4 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => false,
                'description' => 'Luồng giao cố định các tỉnh miền Nam.',
                'priority' => 3,
            ],
            [
                'code' => 'INTER_PROV_CENTRAL',
                'type' => 'Giao hàng liên tỉnh - Miền Trung',
                'service_level' => 'Intercity',
                'delivery_zone' => 'Miền Trung',
                'pricing_strategy' => 'flat',
                'price' => 90000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => '4-5 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => false,
                'description' => 'Phục vụ các tuyến miền Trung với lịch trình cố định.',
                'priority' => 3,
            ],
            [
                'code' => 'INTER_PROV_NORTH',
                'type' => 'Giao hàng liên tỉnh - Miền Bắc',
                'service_level' => 'Intercity',
                'delivery_zone' => 'Miền Bắc',
                'pricing_strategy' => 'flat',
                'price' => 110000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => '4-6 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => false,
                'description' => 'Trung chuyển liên tỉnh miền Bắc.',
                'priority' => 3,
            ],
            [
                'code' => 'COD_STANDARD',
                'type' => 'Giao hàng COD',
                'service_level' => 'COD',
                'delivery_zone' => 'Toàn quốc',
                'pricing_strategy' => 'flat',
                'price' => 40000,
                'percentage_rate' => 0,
                'min_cart_total' => 0,
                'max_cart_total' => 2000000,
                'estimated_time' => '2-4 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => false,
                'description' => 'Thu hộ khi giao, phù hợp đơn nhỏ và trung bình.',
                'priority' => 4,
            ],
            [
                'code' => 'FREE_SHIP',
                'type' => 'Miễn phí vận chuyển',
                'service_level' => 'Free',
                'delivery_zone' => 'Toàn quốc',
                'pricing_strategy' => 'flat',
                'price' => 0,
                'percentage_rate' => 0,
                'min_cart_total' => 1500000,
                'max_cart_total' => null,
                'estimated_time' => '3-5 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => true,
                'description' => 'Áp dụng cho đơn từ 1.500.000 VNĐ trở lên.',
                'priority' => 5,
            ],
            [
                'code' => 'VALUE_BASED_20',
                'type' => 'Giao hàng theo giá trị đơn hàng',
                'service_level' => 'Delivery by Purchase',
                'delivery_zone' => 'Toàn quốc',
                'pricing_strategy' => 'percentage',
                'price' => 0,
                'percentage_rate' => 20,
                'min_cart_total' => 0,
                'max_cart_total' => null,
                'estimated_time' => '2-4 ngày làm việc',
                'supports_cod' => true,
                'is_recommended' => false,
                'description' => 'Phí vận chuyển bằng 20% giá trị đơn, linh hoạt cho đơn hàng giá trị cao.',
                'priority' => 4,
            ],
        ];

        $shippingIds = [];
        $shippingMeta = [];
        foreach ($shippings as $shipping) {
            $id = DB::table('shippings')->insertGetId([
                'code' => $shipping['code'],
                'type' => $shipping['type'],
                'service_level' => $shipping['service_level'],
                'delivery_zone' => $shipping['delivery_zone'],
                'price' => number_format($shipping['price'], 2, '.', ''),
                'pricing_strategy' => $shipping['pricing_strategy'],
                'percentage_rate' => $shipping['percentage_rate'],
                'min_cart_total' => $shipping['min_cart_total'],
                'max_cart_total' => $shipping['max_cart_total'],
                'estimated_time' => $shipping['estimated_time'],
                'supports_cod' => $shipping['supports_cod'],
                'is_recommended' => $shipping['is_recommended'],
                'description' => $shipping['description'],
                'priority' => $shipping['priority'],
                'status' => 'active',
                'created_at' => now()->subDays(rand(60, 365)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);

            $shippingIds[] = $id;
            $shippingMeta[$id] = $shipping;
        }

        // ==================== COUPONS (10 bản ghi) ====================
        $coupons = [
            ['code' => 'WELCOME2025', 'type' => 'fixed', 'value' => 200000],
            ['code' => 'SUMMER25', 'type' => 'percent', 'value' => 25],
            ['code' => 'FREESHIP100', 'type' => 'fixed', 'value' => 100000],
            ['code' => 'NEWCUSTOMER', 'type' => 'percent', 'value' => 15],
            ['code' => 'VIPSALE', 'type' => 'percent', 'value' => 30],
            ['code' => 'BLACKFRIDAY', 'type' => 'percent', 'value' => 40],
            ['code' => 'FLASHSALE', 'type' => 'fixed', 'value' => 500000],
            ['code' => 'OFFICE2025', 'type' => 'percent', 'value' => 20],
            ['code' => 'LOYALTY', 'type' => 'fixed', 'value' => 300000],
            ['code' => 'MEGASALE', 'type' => 'percent', 'value' => 35]
        ];

        $couponIds = [];
        foreach ($coupons as $coupon) {
            $couponIds[] = DB::table('coupons')->insertGetId([
                'code' => $coupon['code'],
                'type' => $coupon['type'],
                'value' => number_format($coupon['value'], 2, '.', ''),
                'status' => 'active',
                'created_at' => now()->subDays(rand(30, 180)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // ==================== ORDERS & CARTS (20 bản ghi mỗi bảng) ====================
        $orderIds = [];
        $orderStatuses = ['new', 'progress', 'process', 'delivered', 'cancel'];
        $paymentStatuses = ['paid', 'unpaid', 'pending'];
        $paymentMethods = ['cod', 'paypal', 'momo', 'vnpay', 'stripe'];

        for ($i = 0; $i < 20; $i++) {
            $userId = $userIds[$i];
            $shippingId = $shippingIds[$i % count($shippingIds)];
            $couponId = $i % 3 == 0 ? $couponIds[$i % count($couponIds)] : null;

            // Tạo 2-4 sản phẩm cho mỗi đơn hàng
            $numProducts = rand(2, 4);
            $subTotal = 0;
            $totalQuantity = 0;
            $cartData = [];

            for ($j = 0; $j < $numProducts; $j++) {
                $productIndex = ($i * $numProducts + $j) % count($productIds);
                $productId = $productIds[$productIndex];
                $productPrice = (float)$products[$productIndex]['price'];
                $quantity = rand(1, 3);

                $itemAmount = $productPrice * $quantity;
                $subTotal += $itemAmount;
                $totalQuantity += $quantity;

                $cartData[] = [
                    'product_id' => $productId,
                    'price' => number_format($productPrice, 2, '.', ''),
                    'quantity' => $quantity,
                    'amount' => number_format($itemAmount, 2, '.', '')
                ];
            }

            // Tính coupon value và tìm user coupon available
            $couponValue = 0;
            $userAvailableCoupon = null;
            if ($couponId) {
                $coupon = DB::table('coupons')->where('id', $couponId)->first();
                if ($coupon && $coupon->type === 'fixed') {
                    $couponValue = min((float)$coupon->value, $subTotal);
                } elseif ($coupon && $coupon->type === 'percent') {
                    $couponValue = ($coupon->value / 100) * $subTotal;
                }

                // Tìm user coupon available cho user này
                $userAvailableCoupon = DB::table('user_coupons')
                    ->where('user_id', $userId)
                    ->where('coupon_id', $couponId)
                    ->whereNull('used_at')
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->first();
            }

            $shippingConfig = $shippingMeta[$shippingId] ?? null;
            $shippingPrice = 0;
            if ($shippingConfig) {
                $strategy = $shippingConfig['pricing_strategy'];
                $base = (float) $shippingConfig['price'];
                $rate = (float) $shippingConfig['percentage_rate'];

                if (!is_null($shippingConfig['min_cart_total']) && $subTotal < $shippingConfig['min_cart_total']) {
                    $shippingPrice = 0;
                } elseif (!is_null($shippingConfig['max_cart_total']) && $subTotal > $shippingConfig['max_cart_total']) {
                    $shippingPrice = $base;
                } else {
                    switch ($strategy) {
                        case 'percentage':
                            $shippingPrice = $subTotal * ($rate / 100);
                            break;
                        case 'mixed':
                            $shippingPrice = $base + ($subTotal * ($rate / 100));
                            break;
                        default:
                            $shippingPrice = $base;
                            break;
                    }
                }
            }
            $totalAmount = max(0, $subTotal + $shippingPrice - $couponValue);

            $orderStatus = $orderStatuses[$i % count($orderStatuses)];
            $paymentStatus = $paymentStatuses[$i % count($paymentStatuses)];
            $paymentMethod = $paymentMethods[$i % count($paymentMethods)];

            $deliveryStatusMap = [
                'new' => 'pending',
                'progress' => 'accepted',
                'process' => 'in_transit',
                'delivered' => 'completed',
                'cancel' => 'cancelled',
            ];
            $deliveryStatus = $deliveryStatusMap[$orderStatus] ?? 'pending';

            $assignedShipperId = null;
            $assignmentType = 'self-claim';
            $assignedAt = $acceptedAt = $pickedUpAt = $completedAt = $cancelledAt = null;
            $tipAmount = 0;

            if ($deliveryStatus !== 'pending' && !empty($shipperProfileIds)) {
                $assignedShipperId = $shipperProfileIds[$i % count($shipperProfileIds)];
                $assignmentType = 'manual';

                $assignedAt = Carbon::now()->subDays(rand(1, 20))->subHours(rand(1, 8));
                $acceptedAt = (clone $assignedAt)->addHours(rand(1, 3));

                if (in_array($deliveryStatus, ['accepted', 'in_transit', 'completed'])) {
                    $pickedUpAt = (clone $acceptedAt)->addHours(rand(1, 4));
                }

                if ($deliveryStatus === 'completed') {
                    $baseCompleted = $pickedUpAt ? clone $pickedUpAt : ($acceptedAt ? clone $acceptedAt : Carbon::now());
                    $completedAt = $baseCompleted->addHours(rand(2, 5));
                    $tipAmount = rand(0, 1) ? rand(10000, 50000) : 0;
                }

                if ($deliveryStatus === 'cancelled') {
                    $cancelledAt = (clone $acceptedAt)->addHours(rand(1, 4));
                }
            }

            $orderId = DB::table('orders')->insertGetId([
                'order_number' => 'ORD-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'user_id' => $userId,
                'sub_total' => number_format($subTotal, 2, '.', ''),
                'shipping_id' => $shippingId,
                'coupon' => number_format($couponValue, 2, '.', ''),
                'coupon_id' => $couponId,
                'delivery_charge' => number_format($shippingPrice, 2, '.', ''),
                'total_amount' => number_format($totalAmount, 2, '.', ''),
                'quantity' => $totalQuantity,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'status' => $orderStatus,
                'first_name' => explode(' ', $userNames[$i])[count(explode(' ', $userNames[$i])) - 1],
                'last_name' => explode(' ', $userNames[$i])[0] . ' ' . explode(' ', $userNames[$i])[1],
                'email' => 'user' . ($i + 1) . '@fashionoffice.vn',
                'phone' => '090' . str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                'address1' => 'Số ' . ($i * 10 + 10) . ', Đường ' . ['Nguyễn Huệ', 'Lê Lợi', 'Hai Bà Trưng', 'Trần Hưng Đạo', 'Võ Văn Kiệt'][$i % 5] . ', ' . ['Phường Bến Nghé, Quận 1', 'Phường Đa Kao, Quận 1', 'Phường Tân Định, Quận 1', 'Phường Bến Thành, Quận 1', 'Phường Nguyễn Thái Bình, Quận 1'][$i % 5] . ', TP.HCM',
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            // Mark user coupon as used if applied
            if ($userAvailableCoupon && $couponId) {
                DB::table('user_coupons')
                    ->where('id', $userAvailableCoupon->id)
                    ->update([
                        'used_at' => now(),
                        'used_in_order_id' => $orderId,
                        'updated_at' => now(),
                    ]);
            }

            // Insert cart items cho order này
            $sizesClothes = ['S', 'M', 'L', 'XL', 'XXL'];
            $sizesShoes = ['38', '39', '40', '41', '42', '43'];

            foreach ($cartData as $cart) {
                $product = DB::table('products')->where('id', $cart['product_id'])->first();
                $categoryId = $product->cat_id;

                // Determine size based on category
                $size = null;
                if (in_array($categoryId, [$parentCategoryIds[4], $parentCategoryIds[8]])) {
                    // Shoes
                    $size = $sizesShoes[array_rand($sizesShoes)];
                } elseif (in_array($categoryId, [$parentCategoryIds[0], $parentCategoryIds[1], $parentCategoryIds[2], $parentCategoryIds[3], $parentCategoryIds[6], $parentCategoryIds[7]])) {
                    // Clothes
                    $size = $sizesClothes[array_rand($sizesClothes)];
                }

                DB::table('carts')->insert([
                    'product_id' => $cart['product_id'],
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'size' => $size,
                    'price' => $cart['price'],
                    'status' => $orderStatus,
                    'quantity' => $cart['quantity'],
                    'amount' => $cart['amount'],
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()->subDays(rand(0, 5)),
                ]);
            }

            $deliveryId = DB::table('order_deliveries')->insertGetId([
                'order_id' => $orderId,
                'shipper_id' => $assignedShipperId,
                'status' => $deliveryStatus,
                'assignment_type' => $assignmentType,
                'delivery_fee' => number_format($shippingPrice, 2, '.', ''),
                'tip_amount' => number_format($tipAmount, 2, '.', ''),
                'assigned_at' => $assignedAt ? $assignedAt->toDateTimeString() : null,
                'accepted_at' => $acceptedAt ? $acceptedAt->toDateTimeString() : null,
                'picked_up_at' => $pickedUpAt ? $pickedUpAt->toDateTimeString() : null,
                'completed_at' => $completedAt ? $completedAt->toDateTimeString() : null,
                'cancelled_at' => $cancelledAt ? $cancelledAt->toDateTimeString() : null,
                'cancel_reason' => $deliveryStatus === 'cancelled' ? 'Shipper báo huỷ do sự cố phương tiện' : null,
                'notes' => null,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            if ($deliveryStatus === 'completed' && $assignedShipperId) {
                $rating = rand(4, 5);
                $liked = (bool) rand(0, 1);
                $reviewTimestamp = $completedAt ? (clone $completedAt)->addHours(rand(1, 6)) : now();

                DB::table('shipper_reviews')->insert([
                    'delivery_id' => $deliveryId,
                    'order_id' => $orderId,
                    'shipper_id' => $assignedShipperId,
                    'customer_id' => $userId,
                    'rating' => $rating,
                    'is_liked' => $liked,
                    'tip_amount' => number_format($tipAmount, 2, '.', ''),
                    'comment' => $liked ? 'Shipper giao đúng hẹn và hỗ trợ nhiệt tình.' : 'Giao hàng đúng yêu cầu.',
                    'created_at' => $reviewTimestamp,
                    'updated_at' => (clone $reviewTimestamp)->addHours(1),
                ]);
            }

            $orderIds[] = $orderId;
        }

        // ==================== USER_COUPONS - Award coupons to users ====================
        // Award coupons to some users based on different sources
        foreach (array_slice($userIds, 0, 30) as $index => $userId) {
            $numCoupons = rand(1, 3);
            $selectedCouponIndices = [];
            for ($j = 0; $j < $numCoupons && $j < count($couponIds); $j++) {
                $idx = array_rand($couponIds);
                if (!in_array($idx, $selectedCouponIndices)) {
                    $selectedCouponIndices[] = $idx;
                }
            }

            foreach ($selectedCouponIndices as $couponIndex) {
                $couponId = $couponIds[$couponIndex];
                $source = ['purchase', 'livestream', 'loyalty', 'monthly', 'weekly'][rand(0, 4)];
                $expiresAt = now()->addDays(rand(7, 90));

                // Mark some as used if user has orders
                $usedInOrderId = null;
                $usedAt = null;
                if ($index % 10 == 0 && isset($orderIds[$index])) {
                    $usedAt = now()->subDays(rand(1, 5));
                    $usedInOrderId = $orderIds[$index];
                }

                DB::table('user_coupons')->insert([
                    'user_id' => $userId,
                    'coupon_id' => $couponId,
                    'source' => $source,
                    'obtained_at' => now()->subDays(rand(1, 30)),
                    'expires_at' => $expiresAt,
                    'used_at' => $usedAt,
                    'used_in_order_id' => $usedInOrderId,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(0, 5)),
                ]);
            }
        }

        foreach ($shipperProfileIds as $shipperId) {
            $completedCount = DB::table('order_deliveries')->where('shipper_id', $shipperId)->where('status', 'completed')->count();
            $cancelledCount = DB::table('order_deliveries')->where('shipper_id', $shipperId)->where('status', 'cancelled')->count();
            $averageRating = DB::table('shipper_reviews')->where('shipper_id', $shipperId)->avg('rating');

            $calculatedTrust = $averageRating ? min(10, max(0, 5 + ($averageRating - 3) * 0.7)) : 5;

            DB::table('shippers')->where('id', $shipperId)->update([
                'completed_deliveries' => $completedCount,
                'cancelled_deliveries' => $cancelledCount,
                'average_rating' => $averageRating ? number_format($averageRating, 2, '.', '') : 0,
                'trust_score' => number_format($calculatedTrust, 2, '.', ''),
            ]);
        }

        $pointsPerVnd = config('loyalty.points_per_vnd', 0.01);
        foreach ($userIds as $customerId) {
            $stats = DB::table('orders')
                ->where('user_id', $customerId)
                ->where('status', 'delivered')
                ->selectRaw('COUNT(*) as total_orders, COALESCE(SUM(total_amount),0) as total_spent, MAX(created_at) as last_order_at')
                ->first();

            $totalOrders = (int) ($stats->total_orders ?? 0);
            $totalSpent = (float) ($stats->total_spent ?? 0);
            $lastOrderAt = $stats->last_order_at;

            $tier = LoyaltyService::determineTier($totalOrders, $totalSpent);
            $points = (int) round($totalSpent * $pointsPerVnd);

            DB::table('users')->where('id', $customerId)->update([
                'total_orders' => $totalOrders,
                'total_spent' => number_format($totalSpent, 2, '.', ''),
                'last_order_at' => $lastOrderAt,
                'loyalty_points' => $points,
                'loyalty_tier' => $tier,
            ]);
        }

        // ==================== PRODUCT_REVIEWS (20 bản ghi) ====================
        $reviews = [
            ['user_id' => $userIds[0], 'product_id' => $productIds[0], 'rate' => 5, 'review' => 'Vest rất đẹp và chất lượng tốt. Mặc rất vừa vặn và thoải mái. Giao hàng nhanh!'],
            ['user_id' => $userIds[1], 'product_id' => $productIds[1], 'rate' => 5, 'review' => 'Sản phẩm chất lượng cao, thiết kế sang trọng. Rất hài lòng với sự lựa chọn này.'],
            ['user_id' => $userIds[2], 'product_id' => $productIds[5], 'rate' => 4, 'review' => 'Áo sơ mi đẹp, chất vải mềm mại. Trừ 1 sao vì màu hơi đậm hơn ảnh một chút.'],
            ['user_id' => $userIds[3], 'product_id' => $productIds[6], 'rate' => 5, 'review' => 'Chất vải cotton cao cấp, may sẵn rất đẹp. Sẽ ủng hộ shop tiếp!'],
            ['user_id' => $userIds[4], 'product_id' => $productIds[7], 'rate' => 4, 'review' => 'Áo đẹp nhưng size hơi rộng. Nên tham khảo bảng size kỹ trước khi mua.'],
            ['user_id' => $userIds[5], 'product_id' => $productIds[10], 'rate' => 5, 'review' => 'Quần âu rất đẹp, form dáng chuẩn. Chất liệu co giãn tốt, thoải mái khi mặc.'],
            ['user_id' => $userIds[6], 'product_id' => $productIds[11], 'rate' => 5, 'review' => 'Quần đẹp lắm, đúng như mô tả. Giao hàng nhanh chóng, đóng gói cẩn thận.'],
            ['user_id' => $userIds[7], 'product_id' => $productIds[15], 'rate' => 4, 'review' => 'Áo blazer đẹp, chất liệu tốt. Nhưng giá hơi cao so với mặt bằng chung.'],
            ['user_id' => $userIds[8], 'product_id' => $productIds[20], 'rate' => 5, 'review' => 'Giày da thật, chất lượng xuất sắc. Đi rất êm chân, không bị đau hay phồng rộp.'],
            ['user_id' => $userIds[9], 'product_id' => $productIds[21], 'rate' => 5, 'review' => 'Giày Oxford đẹp sang trọng. Đóng gói chuyên nghiệp, giao hàng đúng hẹn.'],
            ['user_id' => $userIds[10], 'product_id' => $productIds[22], 'rate' => 4, 'review' => 'Giày đẹp nhưng cần mang vài lần mới quen. Ban đầu hơi cứng.'],
            ['user_id' => $userIds[11], 'product_id' => $productIds[25], 'rate' => 5, 'review' => 'Cà vạt lụa đẹp, màu sắc sang trọng. Rất phù hợp cho môi trường công sở.'],
            ['user_id' => $userIds[12], 'product_id' => $productIds[27], 'rate' => 5, 'review' => 'Thắt lưng da thật 100%, khóa chắc chắn. Sản phẩm rất đáng tiền!'],
            ['user_id' => $userIds[13], 'product_id' => $productIds[30], 'rate' => 4, 'review' => 'Váy đẹp, chất liệu tốt. Nhưng cần là ủi trước khi mặc vì bị nhăn khi vận chuyển.'],
            ['user_id' => $userIds[14], 'product_id' => $productIds[31], 'rate' => 5, 'review' => 'Váy bút chì rất đẹp, ôm dáng vừa vặn. Mặc đi làm rất sang và chuyên nghiệp.'],
            ['user_id' => $userIds[15], 'product_id' => $productIds[35], 'rate' => 5, 'review' => 'Áo sơ mi nữ đẹp, chất liệu mát mẻ. Form áo vừa vặn, không bị rộng hay chật.'],
            ['user_id' => $userIds[16], 'product_id' => $productIds[40], 'rate' => 4, 'review' => 'Giày cao gót đẹp, gót cao vừa phải. Đi cả ngày không bị mỏi chân.'],
            ['user_id' => $userIds[17], 'product_id' => $productIds[41], 'rate' => 5, 'review' => 'Giày rất đẹp và chất lượng. Màu nude rất dễ phối đồ, đi làm hay đi chơi đều ok.'],
            ['user_id' => $userIds[18], 'product_id' => $productIds[45], 'rate' => 5, 'review' => 'Túi xách da thật, thiết kế sang trọng. Ngăn chia hợp lý, đựng laptop vừa khít.'],
            ['user_id' => $userIds[19], 'product_id' => $productIds[46], 'rate' => 4, 'review' => 'Cặp laptop chất lượng tốt, nhiều ngăn tiện lợi. Giá hơi cao nhưng xứng đáng.']
        ];

        foreach ($reviews as $review) {
            DB::table('product_reviews')->insert([
                'user_id' => $review['user_id'],
                'product_id' => $review['product_id'],
                'rate' => $review['rate'],
                'review' => $review['review'],
                'status' => 'active',
                'created_at' => now()->subDays(rand(1, 45)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
        }

        // ==================== POST_COMMENTS (20 bản ghi) ====================
        $comments = [
            ['user_id' => $userIds[0], 'post_id' => $postIds[0], 'comment' => 'Bài viết rất hữu ích! Cảm ơn tác giả đã chia sẻ những tips phối vest hay như vậy.', 'parent_id' => null],
            ['user_id' => $userIds[1], 'post_id' => $postIds[0], 'comment' => 'Mình hoàn toàn đồng ý! Vest đen thật sự là item không thể thiếu trong tủ đồ.', 'parent_id' => 1],
            ['user_id' => $userIds[2], 'post_id' => $postIds[1], 'comment' => 'Cảm ơn shop, mình đã biết cách chọn size áo sơ mi phù hợp rồi!', 'parent_id' => null],
            ['user_id' => $userIds[3], 'post_id' => $postIds[2], 'comment' => 'Xu hướng 2025 trông rất thú vị. Đặc biệt là phần về màu sắc pastel.', 'parent_id' => null],
            ['user_id' => $userIds[4], 'post_id' => $postIds[3], 'comment' => 'Bảo quản vest đúng cách thật quan trọng. Mình từng hỏng mấy bộ vest vì không biết cách.', 'parent_id' => null],
            ['user_id' => $userIds[5], 'post_id' => $postIds[4], 'comment' => 'Owen và Aristino là 2 thương hiệu mình tin dùng nhất!', 'parent_id' => null],
            ['user_id' => $userIds[6], 'post_id' => $postIds[5], 'comment' => 'Phong cách công sở nữ rất đa dạng. Bài viết này giúp mình có thêm ý tưởng phối đồ.', 'parent_id' => null],
            ['user_id' => $userIds[7], 'post_id' => $postIds[6], 'comment' => 'Review vest Owen rất chi tiết. Mình sẽ mua thử sản phẩm này!', 'parent_id' => null],
            ['user_id' => $userIds[8], 'post_id' => $postIds[6], 'comment' => 'Vest Owen đáng tiền lắm bạn ơi! Mình mua được 2 tháng rồi, vẫn đẹp như mới.', 'parent_id' => 8],
            ['user_id' => $userIds[9], 'post_id' => $postIds[7], 'comment' => 'Phụ kiện nam quan trọng không kém trang phục chính. Thanks for sharing!', 'parent_id' => null],
            ['user_id' => $userIds[10], 'post_id' => $postIds[8], 'comment' => 'Thời trang bền vững đang là xu hướng của thế giới. Rất vui khi shop quan tâm đến vấn đề này.', 'parent_id' => null],
            ['user_id' => $userIds[11], 'post_id' => $postIds[9], 'comment' => 'Mình cũng hay mắc 5 lỗi này khi mặc vest. Giờ biết cách khắc phục rồi!', 'parent_id' => null],
            ['user_id' => $userIds[12], 'post_id' => $postIds[10], 'comment' => 'Hướng dẫn chọn size rất chi tiết và dễ hiểu. Cảm ơn shop nhiều!', 'parent_id' => null],
            ['user_id' => $userIds[13], 'post_id' => $postIds[11], 'comment' => 'Fashion Week 2025 có nhiều xu hướng mới thú vị quá!', 'parent_id' => null],
            ['user_id' => $userIds[14], 'post_id' => $postIds[12], 'comment' => 'Mình thích quần ống côn hơn, trông hiện đại và trẻ trung hơn.', 'parent_id' => null],
            ['user_id' => $userIds[15], 'post_id' => $postIds[13], 'comment' => 'Cách làm sạch giày da rất hay! Mình sẽ thử ngay với đôi giày của mình.', 'parent_id' => null],
            ['user_id' => $userIds[16], 'post_id' => $postIds[14], 'comment' => 'So sánh 2 thương hiệu rất khách quan. Giúp mình dễ dàng lựa chọn hơn.', 'parent_id' => null],
            ['user_id' => $userIds[17], 'post_id' => $postIds[15], 'comment' => 'Màu be rất dễ phối đồ và sang trọng. Đang cân nhắc mua vest màu be.', 'parent_id' => null],
            ['user_id' => $userIds[18], 'post_id' => $postIds[16], 'comment' => 'Tips mua vest sale rất hữu ích. Mùa sale tới mình sẽ áp dụng!', 'parent_id' => null],
            ['user_id' => $userIds[19], 'post_id' => $postIds[17], 'comment' => 'Túi xách công sở thật sự quan trọng với chị em văn phòng. Cảm ơn bài viết hữu ích này!', 'parent_id' => null]
        ];

        $commentIds = [];
        foreach ($comments as $index => $comment) {
            $parentCommentId = $comment['parent_id'] ? $commentIds[$comment['parent_id'] - 1] : null;

            $commentIds[] = DB::table('post_comments')->insertGetId([
                'user_id' => $comment['user_id'],
                'post_id' => $comment['post_id'],
                'comment' => $comment['comment'],
                'status' => 'active',
                'replied_comment' => $parentCommentId ? 'Đã trả lời bình luận' : null,
                'parent_id' => $parentCommentId,
                'created_at' => now()->subDays(rand(1, 40)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
        }

        // ==================== WISHLISTS (20 bản ghi) ====================
        for ($i = 0; $i < 20; $i++) {
            $userId = $userIds[$i];
            $productIndex = ($i * 2) % count($productIds);
            $productId = $productIds[$productIndex];
            $productPrice = (float)$products[$productIndex]['price'];
            $quantity = 1;
            $amount = $productPrice * $quantity;

            DB::table('wishlists')->insert([
                'product_id' => $productId,
                'cart_id' => null,
                'user_id' => $userId,
                'price' => number_format($productPrice, 2, '.', ''),
                'amount' => number_format($amount, 2, '.', ''),
                'quantity' => $quantity,
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);
        }

        // ==================== SETTINGS ====================
        DB::table('settings')->insert([
            'description' => 'Fashion Office - Cửa hàng thời trang công sở hàng đầu Việt Nam. Chuyên cung cấp vest, áo sơ mi, quần âu, giày da và phụ kiện cao cấp cho doanh nhân và văn phòng.',
            'short_des' => 'Thời trang công sở cao cấp - Phong cách chuyên nghiệp',
            'logo' => '/storage/photos/1/logo.png',
            'photo' => '/storage/photos/1/blog3.jpg',
            'address' => 'TP. Hồ Chí Minh',
            'phone' => '0919925302',
            'email' => 'kieukienquocvn@gmail.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ==================== PASSWORD_RESETS ====================
        foreach (array_slice($userIds, 0, 5) as $index => $userId) {
            DB::table('password_resets')->insert([
                'email' => 'user' . ($index + 1) . '@fashionoffice.vn',
                'token' => Str::random(60),
                'created_at' => now()->subDays(rand(1, 7)),
            ]);
        }

        // ==================== FAILED_JOBS ====================
        $failedJobs = [
            ['payload' => ['job' => 'SendWelcomeEmail', 'data' => ['email' => 'user1@fashionoffice.vn']], 'exception' => 'Connection timeout: Could not connect to SMTP server'],
            ['payload' => ['job' => 'ProcessOrderPayment', 'data' => ['order_id' => 'ORD-00005']], 'exception' => 'Payment gateway error: Invalid API key']
        ];

        foreach ($failedJobs as $job) {
            DB::table('failed_jobs')->insert([
                'connection' => 'database',
                'queue' => 'default',
                'payload' => json_encode($job['payload']),
                'exception' => $job['exception'],
                'failed_at' => now()->subDays(rand(1, 14)),
            ]);
        }

        // ==================== JOBS ====================
        $jobs = [
            ['payload' => ['job' => 'SendOrderConfirmation', 'data' => ['order_id' => 'ORD-00018']]],
            ['payload' => ['job' => 'UpdateInventory', 'data' => ['product_id' => $productIds[10]]]],
            ['payload' => ['job' => 'SendNewsletter', 'data' => ['subscribers' => 1500]]]
        ];

        foreach ($jobs as $job) {
            DB::table('jobs')->insert([
                'queue' => 'default',
                'payload' => json_encode($job['payload']),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => time(),
                'created_at' => time(),
            ]);
        }

        // ==================== NOTIFICATIONS ====================
        $notifications = [
            ['user_id' => $userIds[0], 'message' => 'Đơn hàng ORD-00001 của bạn đã được xác nhận và đang được xử lý'],
            ['user_id' => $userIds[1], 'message' => 'Chúc mừng! Bạn nhận được mã giảm giá 20% cho đơn hàng tiếp theo'],
            ['user_id' => $userIds[2], 'message' => 'Đơn hàng ORD-00003 đã được giao thành công. Cảm ơn bạn đã mua hàng!'],
            ['user_id' => $userIds[3], 'message' => 'Sản phẩm Vest Owen VN001 vừa giảm giá 25%. Mua ngay!'],
            ['user_id' => $userIds[4], 'message' => 'Đánh giá của bạn về sản phẩm đã được duyệt. Cảm ơn phản hồi của bạn!']
        ];

        foreach ($notifications as $index => $notification) {
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => 'App\Notifications\StatusNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $notification['user_id'],
                'data' => json_encode([
                    'title' => 'Thông báo từ Fashion Office',
                    'message' => $notification['message'],
                    'url' => '/user/dashboard'
                ]),
                'read_at' => $index % 2 == 0 ? now()->subDays(rand(1, 3)) : null,
                'created_at' => now()->subDays(rand(1, 20)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
        }

        $this->command->info('✅ Đã seed thành công tất cả dữ liệu!');
        $this->command->info('📊 Tổng quan:');
        $this->command->info('   - Users: 51 (1 admin + 50 users)');
        $this->command->info('   - Brands: 50');
        $this->command->info('   - Products: 50');
        $this->command->info('   - Orders: 20');
        $this->command->info('   - Carts: ~60 (2-4 items per order)');
        $this->command->info('   - Reviews: 20');
        $this->command->info('   - Posts: 20');
        $this->command->info('   - Comments: 20');
        $this->command->info('   - Wishlists: 20');
        $this->command->info('   - Categories: 10 parent + 20 child');
        $this->command->info('   - Post Categories: 10');
        $this->command->info('   - Post Tags: 10');
        $this->command->info('   - Messages: 10');
        $this->command->info('   - Shippings: 11');
        $this->command->info('   - Shippers: ' . count($shipperProfileIds));
        $this->command->info('   - Coupons: 10');
    }
}
