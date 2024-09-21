<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Restaurant;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // 店舗一覧
    public function test_guest_can_access_restaurants_index_page() 
    {
        $response = $this->get(route('restaurants.index'));

        $response->assertStatus(200);
    }

    public function test_regular_user_can_access_restaurants_index_page() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.index'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_restaurants_index_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('restaurants.index'));
        
        $response->assertRedirect(route('admin.home'));
    }

    // 店舗詳細ページ
    public function test_guest_can_access_restaurants_show_page() 
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.show', $restaurant));

        $response->assertStatus(200);
    }

    public function test_regular_user_can_access_restaurants_show_page() 
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.show', $restaurant));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_restaurants_show_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        
        $this->actingAs($admin, 'admin');

        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.show', $restaurant));
        
        $response->assertRedirect(route('admin.home'));
    }

}
