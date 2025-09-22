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
        // Seeding bảng users
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Quản trị viên',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userIds = [];
        for ($i = 1; $i <= 49; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => 'Người dùng ' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng brands
        $brandIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $brandIds[] = DB::table('brands')->insertGetId([
                'title' => 'Thương hiệu ' . $i,
                'slug' => Str::slug('Thương hiệu ' . $i),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng banners
        for ($i = 1; $i <= 5; $i++) {
            DB::table('banners')->insert([
                'title' => 'Biểu ngữ ' . $i,
                'slug' => Str::slug('Biểu ngữ ' . $i),
                'description' => 'Mô tả biểu ngữ ' . $i ,
                'photo' => '/photos/banner' . $i . '.jpg',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng categories
        $parentCategoryIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $parentCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => 'Danh mục cha ' . $i,
                'slug' => Str::slug('Danh mục cha ' . $i),
                'summary' => 'Tóm tắt danh mục cha ' . $i,
                'photo' => '/photos/category' . $i . '.jpg',
                'is_parent' => 1,
                'parent_id' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $childCategoryIds = [];
        for ($i = 1; $i <= 20; $i++) {
            $parentIndex = ($i - 1) % count($parentCategoryIds); 
            $childCategoryIds[] = DB::table('categories')->insertGetId([
                'title' => 'Danh mục con ' . $i,
                'slug' => Str::slug('Danh mục con ' . $i),
                'summary' => 'Tóm tắt danh mục con ' . $i,
                'photo' => '/photos/subcategory' . $i . '.jpg',
                'is_parent' => 0,
                'parent_id' => $parentCategoryIds[$parentIndex],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng products
        $productIds = [];
        for ($i = 1; $i <= 50; $i++) {
            $parentCatIndex = ($i - 1) % count($parentCategoryIds);
            $childCatIndex = ($i - 1) % count($childCategoryIds);
            $brandIndex = ($i - 1) % count($brandIds);
            $price = rand(100000, 9999999) / 100;
            $discount = rand(0, 1) ? rand(0, 9999) / 100 : null;
            $productIds[] = DB::table('products')->insertGetId([
                'title' => 'Sản phẩm ' . $i,
                'slug' => Str::slug('Sản phẩm ' . $i),
                'summary' => 'Tóm tắt sản phẩm ' . $i ,
                'description' => 'Mô tả chi tiết sản phẩm ' . $i . ' với nội dung dài.',
                'photo' => '/photos/product' . $i . '.jpg',
                'stock' => rand(50, 200),
                'size' => 'M,L,XL',
                'condition' => ['default', 'new', 'hot'][rand(0, 2)],
                'status' => 'active',
                'price' => number_format($price, 2, '.', ''),
                'discount' => $discount ? number_format($discount, 2, '.', '') : null,
                'is_featured' => (bool)rand(0, 1),
                'cat_id' => $parentCategoryIds[$parentCatIndex],
                'child_cat_id' => rand(0, 1) ? $childCategoryIds[$childCatIndex] : null,
                'brand_id' => $brandIds[$brandIndex],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_categories
        $postCategoryIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $postCategoryIds[] = DB::table('post_categories')->insertGetId([
                'title' => 'Danh mục bài viết ' . $i,
                'slug' => Str::slug('Danh mục bài viết ' . $i),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_tags
        $postTagIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $postTagIds[] = DB::table('post_tags')->insertGetId([
                'title' => 'Thẻ bài viết ' . $i,
                'slug' => Str::slug('Thẻ bài viết ' . $i),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng posts
        for ($i = 1; $i <= 20; $i++) {
            $postCatIndex = ($i - 1) % count($postCategoryIds);
            $postTagIndex = ($i - 1) % count($postTagIds);
            $userIndex = ($i - 1) % count($userIds);
            DB::table('posts')->insert([
                'title' => 'Bài viết ' . $i,
                'slug' => Str::slug('Bài viết ' . $i),
                'summary' => 'Tóm tắt bài viết ' . $i,
                'description' => 'Nội dung bài viết ' . $i . ' chi tiết.',
                'quote' => 'Trích dẫn từ bài viết ' . $i,
                'photo' => '/photos/post' . $i . '.jpg',
                'tags' => 'thẻ1,thẻ2',
                'post_cat_id' => $postCategoryIds[$postCatIndex],
                'post_tag_id' => $postTagIds[$postTagIndex],
                'added_by' => $userIds[$userIndex],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng messages
        for ($i = 1; $i <= 10; $i++) {
            $userIndex = ($i - 1) % count($userIds);
            DB::table('messages')->insert([
                'name' => 'Người gửi ' . $i,
                'subject' => 'Chủ đề tin nhắn ' . $i,
                'email' => 'message' . $i . '@example.com',
                'photo' => '/photos/message' . $i . '.jpg',
                'phone' => '012345678' . ($i % 10),
                'message' => 'Nội dung tin nhắn ' . $i . ' bằng tiếng Việt.',
                'read_at' => rand(0, 1) ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng shippings
        $shippingIds = [];
        $shippingTypes = ['Giao hàng tiêu chuẩn', 'Giao hàng nhanh', 'Giao hàng quốc tế'];
        foreach ($shippingTypes as $index => $type) {
            $shippingIds[] = DB::table('shippings')->insertGetId([
                'type' => $type,
                'price' => number_format(($index + 1) * 50000 / 100, 2, '.', ''),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng coupons
        $couponIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $couponIds[] = DB::table('coupons')->insertGetId([
                'code' => 'COUPON' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'type' => ['fixed', 'percent'][rand(0, 1)],
                'value' => number_format(rand(1000, 50000) / 100, 2, '.', ''),
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
            // Chỉ áp dụng coupon cho 50% đơn hàng để tránh discount quá lớn
            $couponId = rand(0, 1) ? $couponIds[$index % count($couponIds)] : null;
            $couponValue = 0.00;

            // Tạo giỏ hàng trước để tính sub_total và quantity
            $cartItems = [];
            $numCartItems = rand(1, 5); // Mỗi đơn hàng có 1-5 sản phẩm
            for ($j = 1; $j <= $numCartItems; $j++) {
                $productIndex = ($index * $j) % count($productIds);
                $productId = $productIds[$productIndex];
                $quantityCart = rand(1, 3);
                $quantity += $quantityCart;
                $product = DB::table('products')->where('id', $productId)->first();
                $price = $product->price;
                $amount = number_format($price * $quantityCart, 2, '.', '');
                $subTotal += (float)$amount;

                $cartItems[] = [
                    'product_id' => $productId,
                    'user_id' => $userId,
                    'price' => number_format($price, 2, '.', ''),
                    'status' => 'new',
                    'quantity' => $quantityCart,
                    'amount' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Tính couponValue nếu có coupon
            if ($couponId) {
                $coupon = DB::table('coupons')->where('id', $couponId)->first();
                $couponValue = $coupon->type === 'fixed'
                    ? min((float)$coupon->value, $subTotal) // Đảm bảo coupon không vượt quá sub_total
                    : min(($coupon->value / 100) * $subTotal, $subTotal); // Giới hạn phần trăm giảm
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
                'payment_method' => ['cod', 'paypal'][rand(0, 1)],
                'payment_status' => 'unpaid',
                'status' => 'new',
                'first_name' => 'Tên ' . ($index + 1),
                'last_name' => 'Họ ' . ($index + 1),
                'email' => 'order' . ($index + 1) . '@example.com',
                'phone' => '012345678' . (($index + 1) % 10),
                'country' => 'Việt Nam',
                'post_code' => '10000' . (($index + 1) % 10),
                'address1' => 'Địa chỉ 1 ' . ($index + 1),
                'address2' => 'Địa chỉ 2 ' . ($index + 1),
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
        for ($i = 1; $i <= 20; $i++) {
            $userIndex = ($i - 1) % count($userIds);
            $productIndex = ($i - 1) % count($productIds);
            DB::table('product_reviews')->insert([
                'user_id' => $userIds[$userIndex],
                'product_id' => $productIds[$productIndex],
                'rate' => rand(1, 5),
                'review' => 'Đánh giá sản phẩm ' . $i . ' bằng tiếng Việt.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_comments
        for ($i = 1; $i <= 20; $i++) {
            $userIndex = ($i - 1) % count($userIds);
            $postIndex = ($i - 1) % 20; // Giả sử có 20 bài viết
            DB::table('post_comments')->insert([
                'user_id' => $userIds[$userIndex],
                'post_id' => $postIndex + 1,
                'comment' => 'Bình luận bài viết ' . $i,
                'status' => 'active',
                'replied_comment' => rand(0, 1) ? 'Trả lời bình luận trước' : null,
                'parent_id' => ($i > 1 && rand(0, 1)) ? rand(1, $i - 1) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng wishlists
        for ($i = 1; $i <= 20; $i++) {
            $userIndex = ($i - 1) % count($userIds);
            $productIndex = ($i - 1) % count($productIds);
            $productId = $productIds[$productIndex];
            $product = DB::table('products')->where('id', $productId)->first();
            $quantity = rand(1, 5);
            $amount = number_format($product->price * $quantity, 2, '.', '');
            DB::table('wishlists')->insert([
                'product_id' => $productId,
                'cart_id' => null, // Không liên kết với cart để đơn giản
                'user_id' => $userIds[$userIndex],
                'price' => number_format($product->price, 2, '.', ''),
                'amount' => $amount,
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng settings (chỉ cần 1 bản ghi)
        DB::table('settings')->insert([
            'description' => 'Mô tả cài đặt hệ thống',
            'short_des' => 'Tóm tắt ngắn về hệ thống',
            'logo' => '/logos/logo.png',
            'photo' => '/photos/setting.jpg',
            'address' => 'Địa chỉ công ty',
            'phone' => '0123456789',
            'email' => 'contact@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seeding bảng password_resets
        for ($i = 1; $i <= 5; $i++) {
            $userIndex = ($i - 1) % count($userIds);
            DB::table('password_resets')->insert([
                'email' => 'user' . $userIds[$userIndex] . '@example.com',
                'token' => Str::random(60),
                'created_at' => now(),
            ]);
        }

        // Seeding bảng failed_jobs
        for ($i = 1; $i <= 5; $i++) {
            DB::table('failed_jobs')->insert([
                'connection' => 'database',
                'queue' => 'default',
                'payload' => json_encode(['data' => 'Công việc thất bại ' . $i]),
                'exception' => 'Lỗi giả định ' . $i,
                'failed_at' => now(),
            ]);
        }

        // Seeding bảng jobs
        for ($i = 1; $i <= 5; $i++) {
            DB::table('jobs')->insert([
                'queue' => 'default',
                'payload' => json_encode(['data' => 'Công việc ' . $i]),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => time(),
                'created_at' => time(),
            ]);
        }

        // Seeding bảng notifications
        for ($i = 1; $i <= 20; $i++) {
            $userIndex = ($i - 1) % count($userIds);
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => 'App\Notifications\GeneralNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $userIds[$userIndex],
                'data' => json_encode(['message' => 'Thông báo ' . $i . ' bằng tiếng Việt.']),
                'read_at' => rand(0, 1) ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}