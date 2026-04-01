<?php

namespace App\Services;

use App\Enums\LoyaltyTier;
use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public function earnPoints(User $user, Order $order): int
    {
        $tier = LoyaltyTier::fromPoints($user->total_points_earned ?? 0);
        $basePoints = (int) floor((float) $order->total / 10000);
        $earnedPoints = (int) floor($basePoints * $tier->multiplier());

        if ($earnedPoints <= 0) {
            return 0;
        }

        DB::transaction(function () use ($user, $order, $earnedPoints, $tier) {
            PointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'earn',
                'points' => $earnedPoints,
                'description' => "Tích điểm đơn hàng #{$order->order_number} (x{$tier->multiplier()} hạng {$tier->label()})",
            ]);

            $user->increment('loyalty_points', $earnedPoints);
            $user->increment('total_points_earned', $earnedPoints);
            $order->update(['points_earned' => $earnedPoints]);
        });

        return $earnedPoints;
    }

    public function redeemPoints(User $user, int $points, Order $order): int
    {
        if ($points <= 0 || $user->loyalty_points < $points) {
            return 0;
        }

        $maxRedeemable = $this->getMaxRedeemable($user, (int) ((float) $order->subtotal));
        $points = min($points, $maxRedeemable);

        if ($points <= 0) {
            return 0;
        }

        $discount = $points * 1000;

        DB::transaction(function () use ($user, $order, $points, $discount) {
            PointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'redeem',
                'points' => $points,
                'description' => "Đổi điểm đơn hàng #{$order->order_number}",
            ]);

            $user->decrement('loyalty_points', $points);
            $order->update([
                'points_used' => $points,
                'points_discount' => $discount,
            ]);
        });

        return $discount;
    }

    public function getUserTier(User $user): LoyaltyTier
    {
        return LoyaltyTier::fromPoints($user->total_points_earned ?? 0);
    }

    public function getPointsHistory(User $user, int $perPage = 15)
    {
        return $user->pointTransactions()
            ->with('order:id,order_number')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function calculateEarnablePoints(int $orderTotal, LoyaltyTier $tier): int
    {
        $basePoints = (int) floor($orderTotal / 10000);
        return (int) floor($basePoints * $tier->multiplier());
    }

    public function getMaxRedeemable(User $user, int $orderSubtotal): int
    {
        $maxDiscount = (int) floor($orderSubtotal * 0.3);
        $maxPointsByDiscount = (int) floor($maxDiscount / 1000);
        return min($user->loyalty_points, $maxPointsByDiscount);
    }

    public function getLoyaltySummary(User $user): array
    {
        $tier = $this->getUserTier($user);
        $nextTier = $tier->nextTier();

        return [
            'points' => $user->loyalty_points ?? 0,
            'total_earned' => $user->total_points_earned ?? 0,
            'tier' => $tier->value,
            'tier_label' => $tier->label(),
            'tier_icon' => $tier->icon(),
            'tier_color' => $tier->color(),
            'multiplier' => $tier->multiplier(),
            'next_tier' => $nextTier ? [
                'label' => $nextTier->label(),
                'icon' => $nextTier->icon(),
                'min_points' => $nextTier->minPoints(),
                'points_needed' => max(0, $nextTier->minPoints() - ($user->total_points_earned ?? 0)),
            ] : null,
        ];
    }
}
