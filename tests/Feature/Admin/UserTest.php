<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannnot_admin_user_index_page()
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_user_index_page() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_admin_user_index_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        
        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin/users');
        
        $response->assertStatus(200);
    }

    public function test_guest_cannnot_admin_user_show_page()
    {
        $response = $this->get('/admin/users/1');

        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_user_show_page() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_admin_user_show_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create(['id' => 1]);

        $this->actingAs($admin, 'admin');
        $response = $this->get('/admin/users/1');
        $response->assertStatus(200);
    }
}
