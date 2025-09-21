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
        // Seeding bảng users: 1 admin + 49 users
        DB::table('users')->insert([
            'name' => 'Quản trị viên',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 49; $i++) {
            DB::table('users')->insert([
                'name' => 'Người dùng ' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng brands: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('brands')->insert([
                'title' => 'Thương hiệu ' . $i,
                'slug' => Str::slug('Thương hiệu ' . $i),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng banners: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('banners')->insert([
                'title' => 'Biểu ngữ ' . $i,
                'slug' => Str::slug('Biểu ngữ ' . $i),
                'description' => 'Mô tả biểu ngữ ' . $i . ' bằng tiếng Việt.',
                'photo' => '/photos/banner' . $i . '.jpg',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng categories: 50 hàng (25 parent, 25 child)
        for ($i = 1; $i <= 25; $i++) {
            DB::table('categories')->insert([
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

        for ($i = 1; $i <= 25; $i++) {
            DB::table('categories')->insert([
                'title' => 'Danh mục con ' . $i,
                'slug' => Str::slug('Danh mục con ' . $i),
                'summary' => 'Tóm tắt danh mục con ' . $i,
                'photo' => '/photos/subcategory' . $i . '.jpg',
                'is_parent' => 0,
                'parent_id' => rand(1, 25),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng products: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('products')->insert([
                'title' => 'Sản phẩm ' . $i,
                'slug' => Str::slug('Sản phẩm ' . $i),
                'summary' => 'Tóm tắt sản phẩm ' . $i . ' bằng tiếng Việt.',
                'description' => 'Mô tả chi tiết sản phẩm ' . $i . ' với nội dung dài.',
                'photo' => '/photos/product' . $i . '.jpg',
                'stock' => rand(50, 200),
                'size' => 'M,L,XL',
                'condition' => ['default', 'new', 'hot'][rand(0, 2)],
                'status' => 'active',
                'price' => number_format(rand(100000, 999999999) / 100, 2, '.', ''), // DECIMAL(12,2), unsigned
                'discount' => rand(0, 1) ? number_format(rand(0, 9999) / 100, 2, '.', '') : null, // DECIMAL(5,2), nullable
                'is_featured' => (bool)rand(0, 1),
                'cat_id' => rand(1, 25),
                'child_cat_id' => rand(0, 1) ? rand(26, 50) : null,
                'brand_id' => rand(1, 50),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_categories: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('post_categories')->insert([
                'title' => 'Danh mục bài viết ' . $i,
                'slug' => Str::slug('Danh mục bài viết ' . $i),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_tags: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('post_tags')->insert([
                'title' => 'Thẻ bài viết ' . $i,
                'slug' => Str::slug('Thẻ bài viết ' . $i),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng posts: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('posts')->insert([
                'title' => 'Bài viết ' . $i,
                'slug' => Str::slug('Bài viết ' . $i),
                'summary' => 'Tóm tắt bài viết ' . $i,
                'description' => 'Nội dung bài viết ' . $i . ' chi tiết.',
                'quote' => 'Trích dẫn từ bài viết ' . $i,
                'photo' => '/photos/post' . $i . '.jpg',
                'tags' => 'thẻ1,thẻ2',
                'post_cat_id' => rand(1, 50),
                'post_tag_id' => rand(1, 50),
                'added_by' => rand(1, 50),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng messages: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
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

        // Seeding bảng shippings: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('shippings')->insert([
                'type' => 'Phương thức vận chuyển ' . $i,
                'price' => number_format(rand(50000, 200000) / 100, 2, '.', ''), // DECIMAL(12,2), unsigned
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng coupons: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('coupons')->insert([
                'code' => 'COUPON' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'type' => ['fixed', 'percent'][rand(0, 1)],
                'value' => number_format(rand(1000, 50000) / 100, 2, '.', ''), // DECIMAL(20,2)
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng notifications: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => 'App\Notifications\GeneralNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => rand(1, 50),
                'data' => json_encode(['message' => 'Thông báo ' . $i . ' bằng tiếng Việt.']),
                'read_at' => rand(0, 1) ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng orders: 50 hàng (1 cho admin + 49 cho users)
        $orderIds = [];
        for ($i = 1; $i <= 50; $i++) {
            $subTotal = 0.00;
            $quantity = 10;
            $totalAmount = 0.00;
            $orderNumber = 'ORD-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $shippingId = rand(1, 50);
            $couponId = rand(0, 1) ? rand(1, 50) : null;
            $couponValue = 0.00;

            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'user_id' => $i,
                'sub_total' => $subTotal,
                'shipping_id' => $shippingId,
                'coupon' => $couponValue,
                'total_amount' => $totalAmount,
                'quantity' => $quantity,
                'payment_method' => ['cod', 'paypal'][rand(0, 1)],
                'payment_status' => 'unpaid',
                'status' => 'new',
                'first_name' => 'Tên ' . $i,
                'last_name' => 'Họ ' . $i,
                'email' => 'order' . $i . '@example.com',
                'phone' => '012345678' . ($i % 10),
                'country' => 'Việt Nam',
                'post_code' => '10000' . ($i % 10),
                'address1' => 'Địa chỉ 1 ' . $i,
                'address2' => 'Địa chỉ 2 ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $orderIds[$i] = $orderId;

            // Tạo 10 carts cho mỗi order
            $subTotal = 0.00;
            for ($j = 1; $j <= 10; $j++) {
                $productId = rand(1, 50);
                $quantityCart = rand(1, 5);
                $price = DB::table('products')->where('id', $productId)->value('price');
                $amount = number_format($price * $quantityCart, 2, '.', '');
                $subTotal += $amount;

                DB::table('carts')->insert([
                    'product_id' => $productId,
                    'order_id' => $orderId,
                    'user_id' => $i,
                    'price' => number_format($price, 2, '.', ''), // DECIMAL(12,2), unsigned
                    'status' => 'new',
                    'quantity' => $quantityCart,
                    'amount' => $amount, // DECIMAL(15,2), unsigned
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Tính couponValue nếu có coupon
            if ($couponId) {
                $coupon = DB::table('coupons')->where('id', $couponId)->first();
                if ($coupon) {
                    $couponValue = $coupon->type === 'fixed'
                        ? number_format($coupon->value, 2, '.', '')
                        : number_format(($coupon->value / 100) * $subTotal, 2, '.', '');
                }
            }

            // Lấy shippingPrice
            $shippingPrice = DB::table('shippings')->where('id', $shippingId)->value('price');
            $shippingPrice = number_format($shippingPrice, 2, '.', '');

            // Tính totalAmount
            $totalAmount = number_format($subTotal + $shippingPrice - $couponValue, 2, '.', '');

            // Cập nhật order
            DB::table('orders')->where('id', $orderId)->update([
                'sub_total' => number_format($subTotal, 2, '.', ''),
                'coupon' => $couponValue,
                'total_amount' => $totalAmount,
            ]);
        }

        // Seeding bảng product_reviews: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('product_reviews')->insert([
                'user_id' => rand(1, 50),
                'product_id' => rand(1, 50),
                'rate' => rand(1, 5),
                'review' => 'Đánh giá sản phẩm ' . $i . ' bằng tiếng Việt.',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng post_comments: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('post_comments')->insert([
                'user_id' => rand(1, 50),
                'post_id' => rand(1, 50),
                'comment' => 'Bình luận bài viết ' . $i,
                'status' => 'active',
                'replied_comment' => rand(0, 1) ? 'Trả lời bình luận trước' : null,
                'parent_id' => ($i > 1 && rand(0, 1)) ? rand(1, $i - 1) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng wishlists: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            $productId = rand(1, 50);
            $price = DB::table('products')->where('id', $productId)->value('price');
            $quantity = rand(1, 5);
            $amount = number_format($price * $quantity, 2, '.', '');
            DB::table('wishlists')->insert([
                'product_id' => $productId,
                'cart_id' => rand(0, 1) ? rand(1, 500) : null,
                'user_id' => rand(1, 50),
                'price' => number_format($price, 2, '.', ''), // DECIMAL(15,2), unsigned
                'amount' => $amount, // DECIMAL(15,2), unsigned
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng settings: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('settings')->insert([
                'description' => 'Mô tả cài đặt ' . $i,
                'short_des' => 'Tóm tắt ngắn ' . $i,
                'logo' => '/logos/logo' . $i . '.png',
                'photo' => '/photos/setting' . $i . '.jpg',
                'address' => 'Địa chỉ ' . $i,
                'phone' => '012345678' . ($i % 10),
                'email' => 'setting' . $i . '@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seeding bảng password_resets: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('password_resets')->insert([
                'email' => 'reset' . $i . '@example.com',
                'token' => Str::random(60),
                'created_at' => now(),
            ]);
        }

        // Seeding bảng failed_jobs: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('failed_jobs')->insert([
                'connection' => 'database',
                'queue' => 'default',
                'payload' => json_encode(['data' => 'Công việc thất bại ' . $i]),
                'exception' => 'Lỗi giả định ' . $i,
                'failed_at' => now(),
            ]);
        }

        // Seeding bảng jobs: 50 hàng
        for ($i = 1; $i <= 50; $i++) {
            DB::table('jobs')->insert([
                'queue' => 'default',
                'payload' => json_encode(['data' => 'Công việc ' . $i]),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => time(),
                'created_at' => time(),
            ]);
        }
    }
}