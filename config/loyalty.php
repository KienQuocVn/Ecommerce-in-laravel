<?php

return [
    'points_per_vnd' => 0.01, // 1 point cho mỗi 100 VNĐ chi tiêu

    'tiers' => [
        'bronze' => [
            'name' => 'Bronze',
            'min_orders' => 0,
            'min_spent' => 0,
            'benefits' => 'Ưu đãi dành cho thành viên mới',
        ],
        'silver' => [
            'name' => 'Silver',
            'min_orders' => 3,
            'min_spent' => 3000000,
            'benefits' => 'Giảm 3% cho mỗi đơn hàng + ưu tiên xử lý',
        ],
        'gold' => [
            'name' => 'Gold',
            'min_orders' => 6,
            'min_spent' => 7000000,
            'benefits' => 'Giảm 5% + giao nhanh miễn phí nội thành',
        ],
        'platinum' => [
            'name' => 'Platinum',
            'min_orders' => 10,
            'min_spent' => 12000000,
            'benefits' => 'Giảm 8% + quà sinh nhật + CSKH ưu tiên',
        ],
    ],
];
