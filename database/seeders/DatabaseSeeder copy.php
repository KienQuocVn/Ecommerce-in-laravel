<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Vô hiệu hóa kiểm tra khóa ngoại và xóa sạch dữ liệu cũ
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('brands')->truncate();
        DB::table('banners')->truncate();
        DB::table('categories')->truncate();
        DB::table('products')->truncate();
        DB::table('post_categories')->truncate();
        DB::table('post_tags')->truncate();
        DB::table('posts')->truncate();
        DB::table('messages')->truncate();
        DB::table('shippings')->truncate();
        DB::table('coupons')->truncate();
        DB::table('orders')->truncate();
        DB::table('carts')->truncate();
        DB::table('wishlists')->truncate();
        DB::table('product_reviews')->truncate();
        DB::table('post_comments')->truncate();
        DB::table('settings')->truncate();
        DB::table('password_resets')->truncate();
        DB::table('failed_jobs')->truncate();
        DB::table('jobs')->truncate();
        DB::table('notifications')->truncate();
        Schema::enableForeignKeyConstraints();

        // Dữ liệu thực tế cho tên người dùng
        $realNames = [
            'Nguyễn Văn An', 'Trần Thị Bình', 'Lê Hoàng Cường', 'Phạm Minh Đức', 'Hoàng Thị Hương',
            'Vũ Văn Hùng', 'Đặng Thị Lan', 'Bùi Minh Nam', 'Ngô Thị Oanh', 'Lý Văn Phong',
            'Trương Thị Mai', 'Nguyễn Quốc Hùng', 'Phan Văn Khải', 'Đỗ Thị Hồng', 'Hoàng Minh Tuấn',
            'Lê Thị Ngọc', 'Trần Văn Long', 'Nguyễn Thị Thu', 'Võ Minh Hiếu', 'Bùi Thị Lan',
            'Phạm Văn Tâm', 'Trần Thị Hoa', 'Nguyễn Minh Hoàng', 'Lê Văn Dũng', 'Hoàng Thị Thảo',
            'Vũ Thị Hiền', 'Đặng Văn Lâm', 'Bùi Thị Yến', 'Ngô Văn Kiên', 'Lý Thị Nhung',
            'Trương Văn Bình', 'Nguyễn Thị Lan', 'Phan Minh Châu', 'Đỗ Văn Hậu', 'Hoàng Thị Linh',
            'Lê Văn Hùng', 'Trần Thị Minh', 'Nguyễn Văn Quang', 'Võ Thị Kim', 'Bùi Văn Sơn',
            'Phạm Thị Hà', 'Trần Văn Nam', 'Nguyễn Thị Ngọc', 'Lê Minh Đức', 'Hoàng Văn Phong',
            'Vũ Thị Thanh', 'Đặng Văn Hòa', 'Bùi Thị Thúy', 'Ngô Văn An', 'Lý Thị Mai'
        ];

        // Seeding bảng users
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Quản trị viên',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userIds = [];
        foreach ($realNames as $index => $name) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => 'user' . ($index + 1) . '@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng brands
        $brands = [
            'Nike', 'Adidas', 'Puma', 'Gucci', 'Zara', 'H&M', 'Levis', 'Uniqlo', 'Balenciaga', 'Vans',
            'Converse', 'Lacoste', 'Under Armour', 'Reiss', 'Mango', 'Superdry', 'Asics', 'New Balance', 'Fila', 'Tommy Hilfiger',
            'Calvin Klein', 'Ralph Lauren', 'Burberry', 'Moncler', 'Dior', 'Chanel', 'Prada', 'Versace', 'Armani', 'Hugo Boss',
            'Sandro', 'Massimo Dutti', 'Topshop', 'Topman', 'Ganni', 'Reformation', 'Everlane', 'COS', 'Arket', 'AllSaints',
            'Loro Piana', 'Brunello Cucinelli', 'Ermenegildo Zegna', 'Patagonia', 'Arc’teryx', 'Columbia', 'The North Face', 'Lululemon', 'Alo Yoga', 'On Running'
        ];
        $brandIds = [];
        foreach ($brands as $brand) {
            $baseSlug = Str::slug($brand);
            $slug = $baseSlug;
            $counter = 1;
            while (DB::table('brands')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $brandIds[] = DB::table('brands')->insertGetId([
                'title' => $brand,
                'slug' => $slug,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng banners
        $banners = [
            ['title' => 'Khuyến mãi mùa hè', 'description' => 'Giảm giá 30% cho tất cả sản phẩm mùa hè'],
            ['title' => 'Sản phẩm mới', 'description' => 'Khám phá bộ sưu tập mới nhất 2023'],
            ['title' => 'Black Friday', 'description' => 'Giảm giá lên đến 50% cho toàn bộ cửa hàng'],
            ['title' => 'Miễn phí vận chuyển', 'description' => 'Miễn phí vận chuyển cho đơn hàng trên 1 triệu VNĐ'],
            ['title' => 'Sale cuối năm', 'description' => 'Ưu đãi đặc biệt cho dịp Tết']
        ];
        foreach ($banners as $index => $banner) {
            DB::table('banners')->insert([
                'title' => $banner['title'],
                'slug' => Str::slug($banner['title']),
                'description' => $banner['description'],
                'photo' => '/photos/banner' . ($index + 1) . '.jpg',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng categories
        $parentCategories = array_fill(0, 25, null);
        foreach ($parentCategories as $index => &$category) {
            $category = [
                'title' => 'Danh mục cha ' . ($index + 1),
                'summary' => 'Bộ sưu tập thời trang ' . ($index + 1)
            ];
        }
        $parentCategoryIds = [];
        $existingCategorySlugs = DB::table('categories')->pluck('slug')->toArray();
        foreach ($parentCategories as $index => $category) {
            $baseSlug = Str::slug($category['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $existingCategorySlugs)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $existingCategorySlugs[] = $slug;
            $parentCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => $category['title'],
                'slug' => $slug,
                'summary' => $category['summary'],
                'photo' => '/photos/category' . ($index + 1) . '.jpg',
                'is_parent' => 1,
                'parent_id' => null,
                'added_by' => $adminId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $childCategories = array_fill(0, 25, null);
        foreach ($childCategories as $index => &$category) {
            $category = [
                'title' => 'Danh mục con ' . ($index + 1),
                'summary' => 'Danh mục con thời trang ' . ($index + 1),
                'parent_id' => $parentCategoryIds[$index % count($parentCategoryIds)]
            ];
        }
        $childCategoryIds = [];
        foreach ($childCategories as $index => $category) {
            $baseSlug = Str::slug($category['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $existingCategorySlugs)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $existingCategorySlugs[] = $slug;
            $childCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => $category['title'],
                'slug' => $slug,
                'summary' => $category['summary'],
                'photo' => '/photos/subcategory' . ($index + 1) . '.jpg',
                'is_parent' => 0,
                'parent_id' => $category['parent_id'],
                'added_by' => $adminId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng products
        $products = array_fill(0, 50, null);
        foreach ($products as $index => &$product) {
            $product = [
                'title' => 'Sản phẩm ' . ($index + 1),
                'cat_id' => $parentCategoryIds[$index % count($parentCategoryIds)],
                'child_cat_id' => $childCategoryIds[$index % count($childCategoryIds)],
                'brand_id' => $brandIds[$index % count($brandIds)],
                'price' => 100000 + ($index * 50000), // Giá từ 100000 đến 2550000
                'discount' => $index % 2 ? 10.00 : null,
                'stock' => 100 - ($index * 2), // Tồn kho từ 100 đến 2
                'condition' => ['new', 'hot', 'default'][$index % 3],
                'is_featured' => $index % 2
            ];
        }
        $productIds = [];
        $existingProductSlugs = DB::table('products')->pluck('slug')->toArray();
        foreach ($products as $index => $product) {
            $baseSlug = Str::slug($product['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $existingProductSlugs)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $existingProductSlugs[] = $slug;
            $productIds[] = DB::table('products')->insertGetId([
                'title' => $product['title'],
                'slug' => $slug,
                'summary' => 'Tóm tắt sản phẩm ' . $product['title'],
                'description' => 'Mô tả chi tiết sản phẩm ' . $product['title'] . '.',
                'photo' => '/photos/product' . ($index + 1) . '.jpg',
                'stock' => $product['stock'],
                'size' => 'M,L,XL',
                'condition' => $product['condition'],
                'status' => 'active',
                'price' => number_format($product['price'], 2, '.', ''),
                'discount' => $product['discount'] ? number_format($product['discount'], 2, '.', '') : null,
                'is_featured' => $product['is_featured'],
                'cat_id' => $product['cat_id'],
                'child_cat_id' => $product['child_cat_id'],
                'brand_id' => $product['brand_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_categories
        $postCategories = array_fill(0, 50, null);
        foreach ($postCategories as $index => &$category) {
            $category = 'Danh mục bài viết ' . ($index + 1);
        }
        $postCategoryIds = [];
        $existingPostCategorySlugs = DB::table('post_categories')->pluck('slug')->toArray();
        foreach ($postCategories as $category) {
            $baseSlug = Str::slug($category);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $existingPostCategorySlugs)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $existingPostCategorySlugs[] = $slug;
            $postCategoryIds[] = DB::table('post_categories')->insertGetId([
                'title' => $category,
                'slug' => $slug,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_tags
        $postTags = array_fill(0, 50, null);
        foreach ($postTags as $index => &$tag) {
            $tag = 'Thẻ bài viết ' . ($index + 1);
        }
        $postTagIds = [];
        $existingPostTagSlugs = DB::table('post_tags')->pluck('slug')->toArray();
        foreach ($postTags as $tag) {
            $baseSlug = Str::slug($tag);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $existingPostTagSlugs)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $existingPostTagSlugs[] = $slug;
            $postTagIds[] = DB::table('post_tags')->insertGetId([
                'title' => $tag,
                'slug' => $slug,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng posts
        $posts = array_fill(0, 50, null);
        foreach ($posts as $index => &$post) {
            $post = [
                'title' => 'Bài viết ' . ($index + 1),
                'post_cat_id' => $postCategoryIds[$index % count($postCategoryIds)],
                'post_tag_id' => $postTagIds[$index % count($postTagIds)],
                'added_by' => $userIds[$index % count($userIds)]
            ];
        }
        $existingPostSlugs = DB::table('posts')->pluck('slug')->toArray();
        foreach ($posts as $index => $post) {
            $baseSlug = Str::slug($post['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $existingPostSlugs)) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $existingPostSlugs[] = $slug;
            DB::table('posts')->insert([
                'title' => $post['title'],
                'slug' => $slug,
                'summary' => 'Tóm tắt bài viết ' . $post['title'],
                'description' => 'Nội dung chi tiết bài viết ' . $post['title'] . '.',
                'quote' => 'Trích dẫn từ bài viết ' . $post['title'],
                'photo' => '/photos/post' . ($index + 1) . '.jpg',
                'tags' => implode(',', array_slice($postTags, 0, 2)),
                'post_cat_id' => $post['post_cat_id'],
                'post_tag_id' => $post['post_tag_id'],
                'added_by' => $post['added_by'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng messages
        $messages = array_fill(0, 50, null);
        foreach ($messages as $index => &$message) {
            $message = [
                'name' => $realNames[$index % count($realNames)],
                'subject' => 'Hỏi về sản phẩm ' . ($index + 1),
                'email' => 'user' . ($index + 1) . '@example.com',
                'phone' => '090123456' . ($index % 10),
                'message' => 'Tôi muốn hỏi về sản phẩm ' . ($index + 1) . '.'
            ];
        }
        foreach ($messages as $index => $message) {
            DB::table('messages')->insert([
                'name' => $message['name'],
                'subject' => $message['subject'],
                'email' => $message['email'],
                'photo' => '/photos/message' . ($index + 1) . '.jpg',
                'phone' => $message['phone'],
                'message' => $message['message'],
                'read_at' => $index % 2 ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng shippings
        $shippings = [
            ['type' => 'Giao hàng tiêu chuẩn', 'price' => 50000.00],
            ['type' => 'Giao hàng nhanh', 'price' => 100000.00],
            ['type' => 'Giao hàng quốc tế', 'price' => 200000.00],
        ];
        $shippingIds = [];
        foreach ($shippings as $shipping) {
            $shippingIds[] = DB::table('shippings')->insertGetId([
                'type' => $shipping['type'],
                'price' => number_format($shipping['price'], 2, '.', ''),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng coupons
        $coupons = array_fill(0, 50, null);
        foreach ($coupons as $index => &$coupon) {
            $coupon = [
                'code' => 'COUPON' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'type' => $index % 2 ? 'percent' : 'fixed',
                'value' => $index % 2 ? 10.00 : 50000.00
            ];
        }
        $couponIds = [];
        $existingCouponCodes = DB::table('coupons')->pluck('code')->toArray();
        foreach ($coupons as $index => $coupon) {
            $baseCode = 'COUPON' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $code = $baseCode;
            $counter = 1;
            while (in_array($code, $existingCouponCodes)) {
                $code = $baseCode . '-' . $counter++;
            }
            $existingCouponCodes[] = $code;
            $couponIds[] = DB::table('coupons')->insertGetId([
                'code' => $code,
                'type' => $coupon['type'],
                'value' => number_format($coupon['value'], 2, '.', ''),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng orders và carts
        $orderIds = [];
        foreach ($userIds as $userIndex => $userId) {
            // Mỗi người dùng có 10 đơn hàng
            for ($orderNum = 0; $orderNum < 10; $orderNum++) {
                $subTotal = 0.00;
                $quantity = 0;
                $shippingId = $shippingIds[($userIndex + $orderNum) % count($shippingIds)];
                $couponId = ($userIndex + $orderNum) % 2 ? $couponIds[($userIndex + $orderNum) % count($couponIds)] : null;
                $couponValue = 0.00;

                // Tạo giỏ hàng cho đơn hàng này (1 sản phẩm)
                $cartItems = [];
                $productIndex = ($userIndex * 10 + $orderNum) % count($productIds);
                $cartItems[] = [
                    'product_id' => $productIds[$productIndex],
                    'user_id' => $userId,
                    'price' => number_format($products[$productIndex]['price'], 2, '.', ''),
                    'status' => 'delivered',
                    'quantity' => 2,
                    'amount' => number_format($products[$productIndex]['price'] * 2, 2, '.', ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $subTotal = (float)$cartItems[0]['amount'];
                $quantity = $cartItems[0]['quantity'];

                // Tính couponValue
                if ($couponId) {
                    $coupon = DB::table('coupons')->where('id', $couponId)->first();
                    $couponValue = $coupon->type === 'fixed'
                        ? min((float)$coupon->value, $subTotal)
                        : min(($coupon->value / 100) * $subTotal, $subTotal);
                    $couponValue = number_format($couponValue, 2, '.', '');
                }

                // Tính total_amount
                $shippingPrice = DB::table('shippings')->where('id', $shippingId)->value('price');
                $totalAmount = number_format(max(0, $subTotal + $shippingPrice - $couponValue), 2, '.', '');

                // Tạo đơn hàng
                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => 'ORD-' . str_pad($userIndex * 10 + $orderNum + 1, 5, '0', STR_PAD_LEFT),
                    'user_id' => $userId,
                    'sub_total' => number_format($subTotal, 2, '.', ''),
                    'shipping_id' => $shippingId,
                    'coupon' => $couponValue,
                    'total_amount' => $totalAmount,
                    'quantity' => $quantity,
                    'payment_method' => ($userIndex + $orderNum) % 2 ? 'cod' : 'paypal',
                    'payment_status' => 'paid',
                    'status' => 'delivered',
                    'first_name' => explode(' ', $realNames[$userIndex])[1],
                    'last_name' => explode(' ', $realNames[$userIndex])[0],
                    'email' => 'order' . ($userIndex * 10 + $orderNum + 1) . '@example.com',
                    'phone' => '090123456' . (($userIndex + $orderNum) % 10),
                    'country' => 'Việt Nam',
                    'post_code' => '10000' . (($userIndex + $orderNum) % 10),
                    'address1' => 'Số ' . ($userIndex + $orderNum + 1) . ', Đường Lê Lợi, Hà Nội',
                    'address2' => 'Phường Hàng Trống, Quận Hoàn Kiếm',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Thêm order_id vào cart và insert
                foreach ($cartItems as $cart) {
                    DB::table('carts')->insert(array_merge($cart, ['order_id' => $orderId]));
                }

                $orderIds[] = $orderId;
            }
        }

        // Seeding bảng wishlists
        $wishlists = [];
        foreach ($userIds as $userIndex => $userId) {
            // Mỗi người dùng yêu thích 10 sản phẩm
            for ($i = 0; $i < 10; $i++) {
                $productIndex = ($userIndex * 10 + $i) % count($productIds);
                $wishlists[] = [
                    'user_id' => $userId,
                    'product_id' => $productIds[$productIndex],
                    'quantity' => 1,
                    'price' => $products[$productIndex]['price'],
                    'amount' => $products[$productIndex]['price'] * 1
                ];
            }
        }
        foreach ($wishlists as $index => $wishlist) {
            DB::table('wishlists')->insert([
                'product_id' => $wishlist['product_id'],
                'cart_id' => null,
                'user_id' => $wishlist['user_id'],
                'price' => number_format($wishlist['price'], 2, '.', ''),
                'amount' => number_format($wishlist['amount'], 2, '.', ''),
                'quantity' => $wishlist['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng product_reviews
        $reviews = array_fill(0, 50, null);
        foreach ($reviews as $index => &$review) {
            $review = [
                'user_id' => $userIds[$index % count($userIds)],
                'product_id' => $productIds[$index % count($productIds)],
                'rate' => 4 + ($index % 2),
                'review' => 'Đánh giá sản phẩm ' . ($index + 1) . ' rất tốt.'
            ];
        }
        foreach ($reviews as $index => $review) {
            DB::table('product_reviews')->insert([
                'user_id' => $review['user_id'],
                'product_id' => $review['product_id'],
                'rate' => $review['rate'],
                'review' => $review['review'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_comments
        $comments = array_fill(0, 50, null);
        foreach ($comments as $index => &$comment) {
            $comment = [
                'user_id' => $userIds[$index % count($userIds)],
                'post_id' => ($index % 50) + 1,
                'comment' => 'Bình luận bài viết ' . ($index + 1) . ' rất hữu ích.'
            ];
        }
        foreach ($comments as $index => $comment) {
            DB::table('post_comments')->insert([
                'user_id' => $comment['user_id'],
                'post_id' => $comment['post_id'],
                'comment' => $comment['comment'],
                'status' => 'active',
                'replied_comment' => $index % 2 ? 'Trả lời bình luận trước' : null,
                'parent_id' => $index > 0 && $index % 2 ? $index : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng settings
        DB::table('settings')->insert([
            'description' => 'Cửa hàng thời trang trực tuyến hàng đầu Việt Nam',
            'short_des' => 'Cung cấp quần áo và phụ kiện thời trang chất lượng cao',
            'logo' => '/logos/logo.png',
            'photo' => '/photos/setting.jpg',
            'address' => '123 Đường Lê Lợi, Quận 1, TP.HCM',
            'phone' => '0901234567',
            'email' => 'contact@shop.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seeding bảng password_resets
        foreach (array_slice($userIds, 0, 50) as $index => $userId) {
            DB::table('password_resets')->insert([
                'email' => 'user' . ($index + 1) . '@example.com',
                'token' => Str::random(60),
                'created_at' => now(),
            ]);
        }

        // Seeding bảng failed_jobs
        $failedJobs = array_fill(0, 50, null);
        foreach ($failedJobs as $index => &$job) {
            $job = [
                'payload' => ['data' => 'Công việc thất bại ' . ($index + 1)],
                'exception' => 'Lỗi giả định ' . ($index + 1)
            ];
        }
        foreach ($failedJobs as $index => $job) {
            DB::table('failed_jobs')->insert([
                'connection' => 'database',
                'queue' => 'default',
                'payload' => json_encode($job['payload']),
                'exception' => $job['exception'],
                'failed_at' => now(),
            ]);
        }

        // Seeding bảng jobs
        $jobs = array_fill(0, 50, null);
        foreach ($jobs as $index => &$job) {
            $job = ['payload' => ['data' => 'Công việc ' . ($index + 1)]];
        }
        foreach ($jobs as $index => $job) {
            DB::table('jobs')->insert([
                'queue' => 'default',
                'payload' => json_encode($job['payload']),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => time(),
                'created_at' => time(),
            ]);
        }

        // Seeding bảng notifications
        $notifications = array_fill(0, 50, null);
        foreach ($notifications as $index => &$notification) {
            $notification = [
                'user_id' => $userIds[$index % count($userIds)],
                'message' => 'Thông báo ' . ($index + 1) . ' cho người dùng.'
            ];
        }
        foreach ($notifications as $index => $notification) {
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => 'App\Notifications\GeneralNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $notification['user_id'],
                'data' => json_encode(['message' => $notification['message']]),
                'read_at' => $index % 2 ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}