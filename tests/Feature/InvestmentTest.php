<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\MutualFund;
use App\Models\Balance;

class InvestmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_invest_in_mutual_fund()
    {
        $user = User::factory()->create([
            'is_demo' => 0,
            'package_id' => 6 // gives all features
        ]);
        
        $fund = MutualFund::create([
            'name' => 'Seed Growth Fund',
            'status' => 'active',
            'min_investment' => 24000,
            'nav_price' => 10,
            'buffer_percent' => 12.5,
            'annual_return' => 0.15 * 365,
            'risk_level' => 'Low'
        ]);

        Balance::create([
            'user_id' => $user->id,
            'amount' => 50000,
            'symbol' => 'USD'
        ]);

        $response = $this->actingAs($user, 'web')->postJson(route('user.mutual_fund.invest'), [
            'fund_id' => $fund->id,
            'amount' => '30000'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'Successfully invested $30,000 in Seed Growth Fund']);
        
        $this->assertDatabaseHas('mutual_fund_investments', [
            'user_id' => $user->id,
            'fund_id' => $fund->id,
            'amount' => 30000,
            'units' => 3000, // 30000 / 10
            'status' => 'active'
        ]);

        // Balance should be decremented
        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'symbol' => 'USD',
            'amount' => 20000 // 50000 - 30000
        ]);
    }

    public function test_user_can_redeem_mutual_fund_investment()
    {
        $user = User::factory()->create([
            'is_demo' => 0,
            'package_id' => 6
        ]);
        
        $fund = MutualFund::create([
            'name' => 'Seed Growth Fund',
            'status' => 'active',
            'min_investment' => 24000,
            'nav_price' => 10,
            'buffer_percent' => 12.5,
            'annual_return' => 0.15 * 365,
            'risk_level' => 'Low'
        ]);

        Balance::create([
            'user_id' => $user->id,
            'amount' => 20000,
            'symbol' => 'USD'
        ]);

        $investment = \App\Models\MutualFundInvestment::create([
            'user_id' => $user->id,
            'fund_id' => $fund->id,
            'amount' => 30000,
            'units' => 3000,
            'nav_at_purchase' => 10,
            'status' => 'active',
            'is_demo' => 0,
            'invested_at' => now()
        ]);

        $response = $this->actingAs($user, 'web')->postJson(route('user.mutual_fund.redeem'), [
            'id' => $investment->id
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('mutual_fund_investments', [
            'id' => $investment->id,
            'status' => 'redeemed'
        ]);

        // Balance should be incremented
        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'symbol' => 'USD',
            'amount' => 50000 // 20000 + (3000 * 10)
        ]);
    }
}
