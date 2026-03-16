<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Enums\LoyaltyTier;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoyaltyController extends Controller
{
    public function index(LoyaltyService $loyaltyService)
    {
        $user = Auth::user();
        $summary = $loyaltyService->getLoyaltySummary($user);
        $history = $loyaltyService->getPointsHistory($user);

        $tiers = collect(LoyaltyTier::cases())->map(fn($t) => [
            'value' => $t->value,
            'label' => $t->label(),
            'icon' => $t->icon(),
            'color' => $t->color(),
            'min_points' => $t->minPoints(),
            'multiplier' => $t->multiplier(),
        ]);

        return Inertia::render('Loyalty/Index', [
            'summary' => $summary,
            'tiers' => $tiers,
            'history' => $history->through(fn($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'points' => $t->points,
                'description' => $t->description,
                'order_number' => $t->order?->order_number,
                'created_at' => $t->created_at->format('d/m/Y H:i'),
            ]),
        ]);
    }
}
