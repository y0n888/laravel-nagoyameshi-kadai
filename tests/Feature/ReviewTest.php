<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // レビュー一覧ページ
    public function test_guest_cannot_access_review_index()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.index', ['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_can_access_review_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.index', ['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_paid_user_can_access_review_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.index', ['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_review_index()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin');

        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.index', ['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }

    // レビュー投稿ページ
    public function test_guest_cannot_access_review_create()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.create', ['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_review_create()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.create', ['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_can_access_review_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.create', ['restaurant' => $restaurant->id]));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_review_create()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin');

        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('restaurants.reviews.create', ['restaurant' => $restaurant->id]));
        $response->assertRedirect(route('admin.home'));
    }



    // レビュー投稿機能
    public function test_guest_cannot_store_review()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('restaurants.reviews.store', ['restaurant' => $restaurant->id]), [
            'score' => 1,
            'content' => 'テスト',
        ]);
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannotstore_review()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('restaurants.reviews.store', ['restaurant' => $restaurant->id]), [
            'score' => 1,
            'content' => 'テスト',
        ]);
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_can_store_review()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('restaurants.reviews.store', ['restaurant' => $restaurant->id]), [
            'score' => 1,
            'content' => 'テスト',
        ]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_admin_cannot_store_review()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $this->actingAs($admin, 'admin');

        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('restaurants.reviews.store', ['restaurant' => $restaurant->id]), [
            'score' => 1,
            'content' => 'テスト',
        ]);
        $response->assertRedirect(route('admin.home'));
    }

    // レビュー編集ページ
    public function test_guest_cannot_access_review_edit() {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_review_edit() {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_cannot_access_other_review_edit() {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $otherUser = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_paid_user_can_access_own_review_edit() {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
       
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_review_edit() {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('admin.home'));
    }

    // レビュー更新機能
    public function test_guest_cannot_update_review() {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->put(route('restaurants.reviews.update', [$restaurant, $review]),[
            'score' => 5,
            'content' => 'Updated content'
        ]);
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_update_review() {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->put(route('restaurants.reviews.update', [$restaurant, $review]),[
            'score' => 5,
            'content' => 'Updated content'
        ]);
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_cannot_update_other_review() {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $otherUser = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->put(route('restaurants.reviews.update', [$restaurant, $review]),[
            'score' => 5,
            'content' => 'Updated content'
        ]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_paid_user_can_update_review() {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
       
        $response = $this->put(route('restaurants.reviews.update', [$restaurant, $review]),[
            'score' => 5,
            'content' => 'Updated content'
        ]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_admin_cannot_update_review() {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($admin, 'admin');

        $response = $this->put(route('restaurants.reviews.update', [$restaurant, $review]),[
            'score' => 5,
            'content' => 'Updated content'
        ]);
        $response->assertRedirect(route('admin.home'));
    }

    // レビュー削除機能
    public function test_guest_cannot_delete_review() {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_delete_review() {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_paid_user_cannot_delete_other_review() {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $otherUser = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_paid_user_can_delete_review() {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
       
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_admin_cannot_delete_review() {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($admin, 'admin');

        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('admin.home'));
    }
}
