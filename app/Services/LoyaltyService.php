<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class LoyaltyService
{
    public static function syncForOrder(Order $order): void
    {
        $order->loadMissing('user');

        if ($order->status !== 'delivered' || !$order->user) {
            return;
        }

        self::syncForUser($order->user);
    }

    public static function syncForUser(User $user): void
    {
        $completedOrders = $user->orders()
            ->where('status', 'delivered')
            ->get(['id', 'total_amount', 'created_at']);

        $totalOrders = $completedOrders->count();
        $totalSpent = (float) $completedOrders->sum('total_amount');
        $lastOrderAt = $completedOrders->max('created_at');

        $pointsPerVnd = config('loyalty.points_per_vnd', 0.01);
        $loyaltyPoints = (int) round($totalSpent * $pointsPerVnd);
        $tier = self::determineTier($totalOrders, $totalSpent);

        $user->update([
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'last_order_at' => $lastOrderAt,
            'loyalty_points' => $loyaltyPoints,
            'loyalty_tier' => $tier,
        ]);
    }

    public static function determineTier(int $totalOrders, float $totalSpent): string
    {
        $tiers = config('loyalty.tiers', []);
        $currentTier = 'bronze';

        foreach ($tiers as $key => $tier) {
            $minOrders = $tier['min_orders'] ?? 0;
            $minSpent = $tier['min_spent'] ?? 0;

            if ($totalOrders >= $minOrders || $totalSpent >= $minSpent) {
                $currentTier = $key;
            }
        }

        return $currentTier;
    }

    public static function tierMeta(?string $tierKey): array
    {
        $tiers = config('loyalty.tiers', []);
        $fallback = [
            'name' => ucfirst($tierKey ?? 'bronze'),
            'benefits' => 'Ưu đãi thành viên',
            'min_orders' => 0,
            'min_spent' => 0,
        ];

        $tierKey = $tierKey ?? 'bronze';
        $tier = $tiers[$tierKey] ?? $fallback;

        return array_merge(['key' => $tierKey], $tier);
    }
}
