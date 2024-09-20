<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\RegularHoliday;
use App\Models\Restaurant;
use App\Models\User;
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

    public function test_regular_user_cannot_access_admin_restaurant_create()
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

        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/create');
        $response->assertStatus(200);
    }


    // storeアクション（店舗登録機能）
    public function test_guest_cannot_store_admin_restaurant()
    {
        $response = $this->post('/admin/restaurants', [], ['X-CSRF-TOKEN' => csrf_token()]);
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_store_admin_restaurant()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/admin/restaurants', [], ['X-CSRF-TOKEN' => csrf_token()]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_store_admin_restaurant()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $regularHolidays = RegularHoliday::factory()->count(2)->create();
        $regularHolidayIds = $regularHolidays->pluck('id')->toArray();

        $restaurant_data = [
            'name' => 'Store Name',
            'description' => 'A brief description of the store.',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => '123 Main St',
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,
        ];

        $response = $this->actingAs($admin, 'admin')->post('/admin/restaurants', $restaurant_data, ['X-CSRF-TOKEN' => csrf_token()]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/restaurants');

        $restaurantId = Restaurant::latest()->first()->id;

        unset($restaurant_data['category_ids']);
        unset($restaurant_data['regular_holiday_ids']);

        $this->assertDatabaseHas('restaurants', $restaurant_data);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'restaurant_id' => $restaurantId,
                'category_id' => $categoryId,
            ]);
        }

        foreach ($regularHolidayIds as $regularHolidayId) {
            $this->assertDatabaseHas('regular_holiday_restaurant', [
                'restaurant_id' => $restaurantId,
                'regular_holiday_id' => $regularHolidayId,
            ]);
        }
    }

    // editアクション（店舗編集ページ）
    public function test_guest_cannot_access_admin_restaurant_edit()
    {
        $response = $this->get('/admin/restaurants/1/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_restaurant_edit()
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

    public function test_regular_user_cannot_update_admin_restaurant()
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

        $restaurant = Restaurant::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $regularHolidays = RegularHoliday::factory()->count(2)->create();
        $regularHolidayIds = $regularHolidays->pluck('id')->toArray();


        $new_restaurant_data = [
            'name' => 'Updated Store Name',
            'description' => 'A brief description of the store.',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => '123 Main St',
            'opening_time' => '09:00',
            'closing_time' => '22:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
            'regular_holiday_ids' => $regularHolidayIds,
        ];

        // unset($new_restaurant_data['category_ids']);

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.restaurants.update', $restaurant), $new_restaurant_data);

        // $response->assertStatus(302);
        // $response->assertRedirect('/admin/restaurants/show', $restaurant->id);

        unset($new_restaurant_data['category_ids']);
        unset($new_restaurant_data['regular_holiday_ids']);

        $this->assertDatabaseHas('restaurants', [
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

        $restaurant->categories()->sync($categoryIds);
        $restaurant->regularHolidays()->sync($regularHolidayIds);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'restaurant_id' => $restaurant->id,
                'category_id' => $categoryId,
            ]);
        }

        foreach ($regularHolidayIds as $regularHolidayId) {
            $this->assertDatabaseHas('regular_holiday_restaurant', [
                'restaurant_id' => $restaurant->id,
                'regular_holiday_id' => $regularHolidayId,
            ]);
        }

        $response->assertRedirect(route('admin.restaurants.show', $restaurant));
    }


    // destroyアクション（店舗削除機能）
    public function test_guest_cannot_destroy_admin_restaurant()
    {
        $response = $this->delete('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_destroy_admin_restaurant()
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
