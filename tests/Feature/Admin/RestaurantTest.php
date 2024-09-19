<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（店舗一覧ページ）
    public function test_guest_cannot_access_admin_restaurant_index()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_restaurant_index() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_restaurant_index()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');
        $response->assertStatus(200);
    }

    // showアクション（店舗詳細ページ）
    public function test_guest_cannot_access_admin_restaurant_show()
    {
        $response = $this->get('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_restaurant_show() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_restaurant_show()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = Restaurant::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/1');
        $response->assertStatus(200);
    }

    // createアクション（店舗登録ページ）
    public function test_guest_cannot_access_admin_restaurant_create()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_cannot_access_admin_restaurant_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_restaurant_create()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');
        $response->assertStatus(200);
    }


    // storeアクション（店舗登録機能）
    public function test_guest_cannot_store_admin_restaurant()
    {
        $response = $this->post('/admin/restaurants', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_cannot_store_admin_restaurant()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/admin/restaurants', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_store_admin_restaurant()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        
        $response = $this->actingAs($admin, 'admin')->post('/admin/restaurants', [
            'name' => 'Store Name',
            'description' => 'A brief description of the store.',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => '123 Main St',
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'seating_capacity' => 50,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/restaurants');
    }

    // editアクション（店舗編集ページ）
    public function test_guest_cannot_access_admin_restaurant_edit()
    {
        $response = $this->get('/admin/restaurants/1/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_cannot_access_admin_restaurant_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/restaurants/1/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_restaurant_edit()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = Restaurant::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/1/edit');
        $response->assertStatus(200);
    }

    // updateアクション（店舗更新機能）
    public function test_guest_cannot_update_admin_restaurant()
    {
        $response = $this->put('/admin/restaurants/1', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_cannot_update_admin_restaurant()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/admin/restaurants/1', []);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_update_admin_restaurant()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = Restaurant::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin, 'admin')->patch('/admin/restaurants/1', [
            'name' => 'Updated Store Name',
            'description' => 'A brief description of the store.',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => '123 Main St',
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'seating_capacity' => 50,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/restaurants/1');
    }


    // destroyアクション（店舗削除機能）
    public function test_guest_cannot_destroy_admin_restaurant()
    {
        $response = $this->delete('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_cannot_destroy_admin_restaurant()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_destroy_admin_restaurant()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = Restaurant::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin, 'admin')->delete('/admin/restaurants/1');
        $response->assertStatus(302);
        $response->assertRedirect('/admin/restaurants');
    }
}
