<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Reservaton;
use App\Models\Rrestaurant;
use App\Models\User;
use App\Models\Admin;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_top_page()
    {
        $response = $this->get('/admin/home');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_top_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/home');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_top_page()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/home');
        $response->assertStatus(200);
    }
}
