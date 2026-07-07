<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('default_package')->updateOrInsert(
            ['id' => 1],
            ['plan' => 'Basic']
        );
        
        if (!\Illuminate\Support\Facades\Schema::hasTable('email')) {
            \Illuminate\Support\Facades\Schema::create('email', function ($table) {
                $table->id();
                $table->string('email');
                $table->string('code');
            });
        }
    }

    public function test_user_can_register()
    {
        $response = $this->withSession([
            'accountType' => 'Individual',
            'custodianName' => 'Test Custodian'
        ])->postJson(route('s.post'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'phone' => '1234567890',
            'country' => 'USA',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'currency' => 'USD'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);
        
        $this->assertAuthenticated();
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->withoutExceptionHandling();
        $response = $this->postJson(route('login.post'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'login']);
        $this->assertAuthenticatedAs($user, 'web');
    }
}
