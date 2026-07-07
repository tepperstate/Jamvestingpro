<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $with = ['balance', 'package'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'type',
        'custodia',
        'last_name',
        'email',
        'phone',
        'country',
        'package_plan',
        'password',
        'image',
        'indentify_verified',
        'email_verified',
        'residency_verified',
        'last_login',
        'email_verified_at',
        'status',
        'is_demo',
        'preferred_wallet',
        'wallet_holding',
        'traded',
        'highest_investment',
        'trades',
        'daily_trade',
        'weekly_trade',
        'traded_date',
        'user_id',
        'package_id',
        'withdrawal',
        'google_aut',
        'is_2fa_enabled',
        'exit_trade',
        'question',
        'security',
        'code_one',
        'code_two',
        'code_three',
        'currency',
        'custom',
        'custom_message',
        'custom_header',
        'upgrade_code',
        'tax_code',
        'demorage',
        'upgrade_code_check',
        'tax_code_check',
        'demorage_check',
        'is_2fa_exempt',
        'basic_plan_approved',
        'hip_pro_override',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'hip_pro_override' => 'boolean',
    ];

    public function balance()
    {
        return $this->hasOne(Balance::class)->orderByRaw("symbol = 'USD' DESC");
    }

    public function wallets()
    {
        return $this->hasMany(UserWallet::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function hasHipProAccess()
    {
        if ($this->hip_pro_override !== null) {
            return (bool) $this->hip_pro_override;
        }

        $plan = $this->package->name ?? $this->package_plan ?? '';

        return strtolower($plan) === 'diamond';
    }

    public function hasFeature($feature)
    {
        if ($this->is_demo) {
            return true;
        } // Demo users have all features

        $planId = $this->package_id ?? 0;

        $deposit = DB::table('deposits')->whereUserId($this->id)->sum('amount');
        $balance = $this->balance ? $this->balance->amount : 0;
        $totalCapital = max($deposit, $balance);

        if ($totalCapital >= 1000000) {
            $planId = max($planId, 6);
        } elseif ($totalCapital >= 100000) {
            $planId = max($planId, 5);
        } elseif ($totalCapital >= 50000) {
            $planId = max($planId, 4);
        } elseif ($totalCapital >= 10000) {
            $planId = max($planId, 3);
        } elseif ($totalCapital >= 5000) {
            $planId = max($planId, 2);
        } else {
            $planId = max($planId, 1);
        }

        $featureMap = [
            'basic_trading' => 1,
            'mutual_funds' => 2,
            'signal' => 3,
            'vip_stocks' => 4,
            'bot' => 5,
            'copy_trading' => 5,
            'high_yield' => 5,
            'advanced_controls' => 6,
            'high_leverage' => 6,
        ];

        $requiredPlan = $featureMap[$feature] ?? 1;
        if ($planId >= $requiredPlan) {
            return true;
        }

        if (! $this->package) {
            return false;
        }
        $features = is_string($this->package->features) ? json_decode($this->package->features, true) : ($this->package->features ?? []);
        if (! is_array($features)) {
            return false;
        }

        return in_array($feature, $features);
    }

    public function onboardingResponses()
    {
        return $this->hasMany(OnboardingResponse::class);
    }
}
