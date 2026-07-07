<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Noti;
use App\Models\Package;
use App\Models\User;

class TierService
{
    /**
     * Check if user qualifies for a tier upgrade based on total successful deposits.
     */
    public static function checkAndUpgrade(User $user)
    {
        $totalDeposits = Deposit::where('user_id', $user->id)
            ->where('status', 'success')
            ->sum('amount');

        // Find the best matching package (highest min_deposit <= totalDeposits)
        $newPackage = Package::where('min_deposit', '<=', $totalDeposits)
            ->where('min_deposit', '>', 0)
            ->orderBy('min_deposit', 'desc')
            ->first();

        // 3. Only upgrade if the new package has a higher threshold than current
        $currentPackage = Package::find($user->package_id);
        $currentThreshold = $currentPackage ? $currentPackage->min_deposit : 0;

        if ($newPackage && $newPackage->min_deposit > $currentThreshold) {
            $user->update([
                'package_id' => $newPackage->id,
                'package_plan' => $newPackage->name,
                'trades' => $newPackage->trade, // reset trades to new package limit
                'daily_trade' => $newPackage->daily_trade,
                'weekly_trade' => $newPackage->weekly_trade,
            ]);

            // Log notification
            Noti::create([
                'user_id' => $user->id,
                'title' => 'Account Upgraded!',
                'message' => "Congratulations! Your account has been upgraded to {$newPackage->name} based on your deposit history.",
                'status' => 'unread',
            ]);

            return true;
        }

        return false;
    }
}
