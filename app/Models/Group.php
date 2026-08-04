<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    protected $table = 'groups';
    protected $guarded = ['id'];



    public function getFinalPriceFor(User $user): float
    {
        $basePrice = (float) ($this->price_reseler ?? 0);

        $chain = [];
        $currentUser = $user;
        $visited = [];

        while ($currentUser) {
            if (in_array($currentUser->id, $visited)) {
                break;
            }
            $visited[] = $currentUser->id;

            array_unshift($chain, $currentUser);

            $currentUser = ($currentUser->creator && $currentUser->creator != $currentUser->id)
                ? User::find($currentUser->creator)
                : null;
        }

        $currentPrice = $basePrice;

        foreach ($chain as $index => $node) {
            if ($index === 0 && !in_array($node->role, ['customer', 'user'])) {
                $discountPercent = $node->discount_percent ?? 0;
                if ($discountPercent > 0) {
                    $currentPrice = $currentPrice - ($currentPrice * $discountPercent / 100);
                }
            }

            if ($node->role === 'sub_agent' && $index > 0) {
                $parent = $chain[$index - 1];
                $markup = $parent->sub_agent_markup ?? 0;

                if ($markup > 0) {
                    $currentPrice = $currentPrice + ($currentPrice * $markup / 100);
                }
            }
        }

        return round($currentPrice, 2);
    }


    public function getSellingPriceFor(?User $seller): float
    {
        $defaultPrice = (float) ($this->price ?? 0);

        if (!$seller || in_array($seller->role, ['admin', 'manager', 'superadmin'])) {
            return $defaultPrice;
        }

        $purchasePrice = $this->getFinalPriceFor($seller);

        $currentUser = $seller;
        $visited = [];
        $foundSellingPrice = null;

        while ($currentUser) {
            if (in_array($currentUser->id, $visited)) {
                break;
            }
            $visited[] = $currentUser->id;

            $customPrice = DB::table('agent_plan_prices')
                ->where('agent_id', $currentUser->id)
                ->where('group_id', $this->id)
                ->value('selling_price');

            if ($customPrice && $customPrice > 0) {
                $foundSellingPrice = (float) $customPrice;
                break;
            }

            $currentUser = ($currentUser->creator && $currentUser->creator != $currentUser->id)
                ? User::find($currentUser->creator)
                : null;
        }

        $finalSellingPrice = $foundSellingPrice ?? $defaultPrice;


        return max($finalSellingPrice, $purchasePrice);
    }

    /**
     * استخراج هوشمند سرعت میکروتیک از روی نام Charge
     */
    public function getMikrotikSpeedAttribute()
    {
        $defaultSpeed = '10M/10M';

        if (!$this->charge_id) {
            return $defaultSpeed;
        }

        $charge = \App\Models\Charge::find($this->charge_id);

        if ($charge && str_contains($charge->name, '-')) {
            $parts = explode('-', $charge->name);
            $speedValue = trim(end($parts));
            return "{$speedValue}/{$speedValue}";
        }

        return $defaultSpeed;
    }



}
