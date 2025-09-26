<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Dữ liệu thực tế cho tên người dùng
        $realNames = [
            'Nguyễn Văn An', 'Trần Thị Bình', 'Lê Hoàng Cường', 'Phạm Minh Đức', 'Hoàng Thị Hương',
            'Vũ Văn Hùng', 'Đặng Thị Lan', 'Bùi Minh Nam', 'Ngô Thị Oanh', 'Lý Văn Phong',
            // Thêm đủ 50 tên thực tế
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
            'Nike', 'Adidas', 'Puma', 'Gucci', 'Zara', 'H&M', 'Levis', 'Uniqlo', 'Balenciaga', 'Vans'
        ];
        $brandIds = [];
        foreach ($brands as $brand) {
            $brandIds[] = DB::table('brands')->insertGetId([
                'title' => $brand,
                'slug' => Str::slug($brand),
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
        $parentCategories = [
            ['title' => 'Quần áo nam', 'summary' => 'Bộ sưu tập quần áo dành cho nam'],
            ['title' => 'Quần áo nữ', 'summary' => 'Bộ sưu tập quần áo dành cho nữ'],
            ['title' => 'Giày dép', 'summary' => 'Các loại giày dép thời trang'],
            ['title' => 'Phụ kiện', 'summary' => 'Phụ kiện thời trang cao cấp'],
            ['title' => 'Túi xách', 'summary' => 'Túi xách thời trang và tiện dụng'],
            ['title' => 'Đồng hồ', 'summary' => 'Đồng hồ cao cấp cho nam và nữ'],
            ['title' => 'Trang sức', 'summary' => 'Trang sức tinh xảo và sang trọng'],
            ['title' => 'Thể thao', 'summary' => 'Trang phục và phụ kiện thể thao'],
            ['title' => 'Trẻ em', 'summary' => 'Quần áo và phụ kiện cho trẻ em'],
            ['title' => 'Đồ lót', 'summary' => 'Đồ lót thoải mái và thời trang']
        ];
        $parentCategoryIds = [];
        foreach ($parentCategories as $index => $category) {
            $parentCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => $category['title'],
                'slug' => Str::slug($category['title']),
                'summary' => $category['summary'],
                'photo' => '/photos/category' . ($index + 1) . '.jpg',
                'is_parent' => 1,
                'parent_id' => null,
                'added_by' => $adminId, // Gán admin là người tạo
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $childCategories = [
            ['title' => 'Áo thun nam', 'parent_id' => $parentCategoryIds[0], 'summary' => 'Áo thun nam đa dạng phong cách'],
            ['title' => 'Quần jeans nam', 'parent_id' => $parentCategoryIds[0], 'summary' => 'Quần jeans nam thời trang'],
            ['title' => 'Áo sơ mi nam', 'parent_id' => $parentCategoryIds[0], 'summary' => 'Áo sơ mi nam lịch lãm'],
            ['title' => 'Váy nữ', 'parent_id' => $parentCategoryIds[1], 'summary' => 'Váy nữ thanh lịch'],
            ['title' => 'Áo khoác nữ', 'parent_id' => $parentCategoryIds[1], 'summary' => 'Áo khoác nữ thời trang'],
            ['title' => 'Quần tây nữ', 'parent_id' => $parentCategoryIds[1], 'summary' => 'Quần tây nữ công sở'],
            ['title' => 'Giày thể thao', 'parent_id' => $parentCategoryIds[2], 'summary' => 'Giày thể thao năng động'],
            ['title' => 'Giày cao gót', 'parent_id' => $parentCategoryIds[2], 'summary' => 'Giày cao gót thanh lịch'],
            ['title' => 'Dép sandal', 'parent_id' => $parentCategoryIds[2], 'summary' => 'Dép sandal thời trang'],
            ['title' => 'Mũ lưỡi trai', 'parent_id' => $parentCategoryIds[3], 'summary' => 'Mũ lưỡi trai phong cách'],
            ['title' => 'Kính râm', 'parent_id' => $parentCategoryIds[3], 'summary' => 'Kính râm thời trang'],
            ['title' => 'Thắt lưng', 'parent_id' => $parentCategoryIds[3], 'summary' => 'Thắt lưng da cao cấp'],
            ['title' => 'Túi đeo chéo', 'parent_id' => $parentCategoryIds[4], 'summary' => 'Túi đeo chéo tiện dụng'],
            ['title' => 'Túi xách tay', 'parent_id' => $parentCategoryIds[4], 'summary' => 'Túi xách tay sang trọng'],
            ['title' => 'Đồng hồ nam', 'parent_id' => $parentCategoryIds[5], 'summary' => 'Đồng hồ nam lịch lãm'],
            ['title' => 'Đồng hồ nữ', 'parent_id' => $parentCategoryIds[5], 'summary' => 'Đồng hồ nữ tinh tế'],
            ['title' => 'Nhẫn bạc', 'parent_id' => $parentCategoryIds[6], 'summary' => 'Nhẫn bạc thời trang'],
            ['title' => 'Dây chuyền', 'parent_id' => $parentCategoryIds[6], 'summary' => 'Dây chuyền sang trọng'],
            ['title' => 'Quần áo thể thao', 'parent_id' => $parentCategoryIds[7], 'summary' => 'Quần áo thể thao năng động'],
            ['title' => 'Quần áo trẻ em', 'parent_id' => $parentCategoryIds[8], 'summary' => 'Quần áo trẻ em dễ thương']
        ];
        $childCategoryIds = [];
        foreach ($childCategories as $index => $category) {
            $childCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => $category['title'],
                'slug' => Str::slug($category['title']),
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
        $products = [
            ['title' => 'Áo thun nam Nike', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[0], 'brand_id' => $brandIds[0], 'price' => 350000.00, 'discount' => 10.00, 'stock' => 100, 'condition' => 'new', 'is_featured' => true],
            ['title' => 'Quần jeans nam Levis', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[1], 'brand_id' => $brandIds[6], 'price' => 1200000.00, 'discount' => 15.00, 'stock' => 80, 'condition' => 'hot', 'is_featured' => false],
            ['title' => 'Áo sơ mi nam Zara', 'cat_id' => $parentCategoryIds[0], 'child_cat_id' => $childCategoryIds[2], 'brand_id' => $brandIds[4], 'price' => 850000.00, 'discount' => null, 'stock' => 60, 'condition' => 'default', 'is_featured' => true],
            // Thêm đủ 50 sản phẩm với dữ liệu thực tế
        ];
        $productIds = [];
        foreach ($products as $index => $product) {
            $productIds[] = DB::table('products')->insertGetId([
                'title' => $product['title'],
                'slug' => Str::slug($product['title']),
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
        $postCategories = [
            'Thời trang', 'Công nghệ', 'Sức khỏe', 'Du lịch', 'Ẩm thực',
            'Giáo dục', 'Phong cách sống', 'Kinh doanh', 'Thể thao', 'Văn hóa'
        ];
        $postCategoryIds = [];
        foreach ($postCategories as $category) {
            $postCategoryIds[] = DB::table('post_categories')->insertGetId([
                'title' => $category,
                'slug' => Str::slug($category),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_tags
        $postTags = [
            'thời trang nam', 'thời trang nữ', 'giày dép', 'phụ kiện', 'xu hướng',
            'sức khỏe', 'du lịch', 'ẩm thực', 'công nghệ', 'giáo dục'
        ];
        $postTagIds = [];
        foreach ($postTags as $tag) {
            $postTagIds[] = DB::table('post_tags')->insertGetId([
                'title' => $tag,
                'slug' => Str::slug($tag),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng posts
        $posts = [
            ['title' => 'Top 10 xu hướng thời trang 2023', 'post_cat_id' => $postCategoryIds[0], 'post_tag_id' => $postTagIds[0], 'added_by' => $userIds[0]],
            ['title' => 'Cách chăm sóc sức khỏe mùa đông', 'post_cat_id' => $postCategoryIds[2], 'post_tag_id' => $postTagIds[5], 'added_by' => $userIds[1]],
            // Thêm đủ 20 bài viết
        ];
        foreach ($posts as $index => $post) {
            DB::table('posts')->insert([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'summary' => 'Tóm tắt bài viết ' . $post['title'],
                'description' => 'Nội dung chi tiết bài viết ' . $post['title'] . '.',
                'quote' => 'Trích dẫn từ bài viết ' . $post['title'],
                'photo' => '/photos/post' . ($index + 1) . '.jpg',
                'tags' => implode(',', array_slice($postTags, 0, 2)), // Gán 2 thẻ đầu tiên
                'post_cat_id' => $post['post_cat_id'],
                'post_tag_id' => $post['post_tag_id'],
                'added_by' => $post['added_by'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng messages
        $messages = [
            ['name' => 'Nguyễn Văn An', 'subject' => 'Hỏi về sản phẩm', 'email' => 'an@example.com', 'phone' => '0901234567', 'message' => 'Tôi muốn hỏi về sản phẩm áo thun nam.'],
            // Thêm đủ 10 tin nhắn
        ];
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
        $coupons = [
            ['code' => 'SUMMER2023', 'type' => 'percent', 'value' => 10.00],
            ['code' => 'FREESHIP', 'type' => 'fixed', 'value' => 50000.00],
            ['code' => 'SALE2023', 'type' => 'percent', 'value' => 15.00],
            ['code' => 'WELCOME', 'type' => 'fixed', 'value' => 100000.00],
            ['code' => 'VIP2023', 'type' => 'percent', 'value' => 20.00],
        ];
        $couponIds = [];
        foreach ($coupons as $coupon) {
            $couponIds[] = DB::table('coupons')->insertGetId([
                'code' => $coupon['code'],
                'type' => $coupon['type'],
                'value' => number_format($coupon['value'], 2, '.', ''),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng orders và carts
        $orderIds = [];
        foreach ($userIds as $index => $userId) {
            $subTotal = 0.00;
            $quantity = 0;
            $shippingId = $shippingIds[$index % count($shippingIds)];
            $couponId = $index % 2 ? $couponIds[$index % count($couponIds)] : null; // Áp dụng coupon cho 50% đơn hàng
            $couponValue = 0.00;

            // Tạo giỏ hàng
            $cartItems = [];
            $cartItems[] = [
                'product_id' => $productIds[$index % count($productIds)],
                'user_id' => $userId,
                'price' => number_format($products[$index % count($products)]['price'], 2, '.', ''),
                'status' => 'new',
                'quantity' => 2, // Số lượng cố định
                'amount' => number_format($products[$index % count($products)]['price'] * 2, 2, '.', ''),
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
                'order_number' => 'ORD-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'user_id' => $userId,
                'sub_total' => number_format($subTotal, 2, '.', ''),
                'shipping_id' => $shippingId,
                'coupon' => $couponValue,
                'total_amount' => $totalAmount,
                'quantity' => $quantity,
                'payment_method' => $index % 2 ? 'cod' : 'paypal',
                'payment_status' => 'unpaid',
                'status' => 'new',
                'first_name' => explode(' ', $realNames[$index])[1],
                'last_name' => explode(' ', $realNames[$index])[0],
                'email' => 'order' . ($index + 1) . '@example.com',
                'phone' => '090123456' . ($index % 10),
                'country' => 'Việt Nam',
                'post_code' => '10000' . ($index % 10),
                'address1' => 'Số ' . ($index + 1) . ', Đường Lê Lợi, Hà Nội',
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

        // Seeding bảng product_reviews
        $reviews = [
            ['user_id' => $userIds[0], 'product_id' => $productIds[0], 'rate' => 5, 'review' => 'Sản phẩm chất lượng, rất đáng tiền!'],
            ['user_id' => $userIds[1], 'product_id' => $productIds[1], 'rate' => 4, 'review' => 'Quần jeans đẹp, nhưng hơi chật.'],
            // Thêm đủ 20 đánh giá
        ];
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
        $comments = [
            ['user_id' => $userIds[0], 'post_id' => 1, 'comment' => 'Bài viết rất hữu ích, cảm ơn tác giả!'],
            ['user_id' => $userIds[1], 'post_id' => 2, 'comment' => 'Tôi rất thích nội dung này.'],
            // Thêm đủ 20 bình luận
        ];
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

        // Seeding bảng wishlists
        $wishlists = [
            ['user_id' => $userIds[0], 'product_id' => $productIds[0], 'quantity' => 1, 'price' => $products[0]['price'], 'amount' => $products[0]['price'] * 1],
            // Thêm đủ 20 danh sách yêu thích
        ];
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
        foreach (array_slice($userIds, 0, 5) as $index => $userId) {
            DB::table('password_resets')->insert([
                'email' => 'user' . ($index + 1) . '@example.com', // Khớp với email trong users
                'token' => Str::random(60),
                'created_at' => now(),
            ]);
        }

        // Seeding bảng failed_jobs
        $failedJobs = [
            ['payload' => ['data' => 'Gửi email thất bại'], 'exception' => 'Lỗi kết nối SMTP'],
            // Thêm đủ 5 công việc thất bại
        ];
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
        $jobs = [
            ['payload' => ['data' => 'Gửi email chào mừng']],
            // Thêm đủ 5 công việc
        ];
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
        $notifications = [
            ['user_id' => $userIds[0], 'message' => 'Đơn hàng của bạn đã được xác nhận'],
            // Thêm đủ 20 thông báo
        ];
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