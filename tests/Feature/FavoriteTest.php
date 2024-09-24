<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    
    // お気に入り一覧ページ
    public function test_guest_cannot_access_favorite_index()
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_can_access_favorite_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_can_access_favorite_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $response = $this->get(route('favorites.index'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_favorite_index()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('admin.home'));
    }

    // お気に入り追加機能
    public function test_guest_cannot_add_favorite()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('favorites.store', ['restaurant_id' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_add_favorite()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('favorites.store', ['restaurant_id' => $restaurant->id]));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_can_add_favorite()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('favorites.store', ['restaurant_id' => $restaurant->id]));
        $response->assertStatus(302);
    }

    public function test_admin_cannot_add_favorite()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin');

        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('favorites.store', ['restaurant_id' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }

    // お気に入り解除機能
    public function test_guest_cannot_remove_favorite()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', ['restaurant_id' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_remove_favorite()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', ['restaurant_id' => $restaurant->id]));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_can_remove_favorite()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', ['restaurant_id' => $restaurant->id]));
        $response->assertStatus(302);
    }

    public function test_admin_cannot_remove_favorite()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin');

        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', ['restaurant_id' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }
}
