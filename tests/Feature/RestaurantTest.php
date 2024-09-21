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
}
