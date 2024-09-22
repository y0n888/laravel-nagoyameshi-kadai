<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    // create　有料プラン登録ページ
    public function test_guest_cannot_access_subscribed_create_page() 
    {
        $response = $this->get(route('subscription.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_can_access_subscribed_create_page() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertStatus(200);
    }

    public function test_subscribed_user_cannot_access_subscribed_create_page() 
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertRedirect(route('subscription.edit'));
    }

    public function test_admin_cannot_access_subscribed_create_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $this->actingAs($admin, 'admin');
        $response = $this->get(route('subscription.create'));

        $response->assertRedirect(route('admin.home'));
    }

    // store 有料プラン登録機能
    public function test_guest_cannot_register_subscription() 
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->post(route('subscription.store'), $request_parameter);

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_can_register_subscription() 
    {
        $user = User::factory()->create();

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->actingAs($user)->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('home'));

        $user = $user->fresh();

        $this->assertTrue($user->subscribed('premium_plan'));
    }

    public function test_subscribed_user_cannot_register_subscription() 
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->actingAs($user)->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('subscription.edit'));
    }

    public function test_admin_cannot_register_subscribed_subscription() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $this->actingAs($admin, 'admin');

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->post(route('subscription.store'), $request_parameter);

        $response->assertRedirect(route('admin.home'));
    }


    // edit お支払い方法編集ページ
    public function test_guest_cannot_access_payment_edit_page() 
    {
        $response = $this->get(route('subscription.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_payment_edit_page() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.edit'));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_subscribed_user_can_access_payment_edit_page() 
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('subscription.edit'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_payment_edit_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $this->actingAs($admin, 'admin');
        $response = $this->get(route('subscription.edit'));

        $response->assertRedirect(route('admin.home'));
    }

    // update お支払い方法更新機能
    public function test_guest_cannot_update_payment() 
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $response = $this->post(route('subscription.store'), $request_parameter);

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_update_payment() 
    {
        $user = User::factory()->create();

        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        $response = $this->actingAs($user)->post(route('subscription.update'), $request_parameter);
        $response->assertRedirect(route('home'));
        // $response->assertStatus(403);
    }

    public function test_subscribed_user_can_update_payment() 
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');

        $default_payment_method_id = $user->defaultPaymentMethod()->id;

        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        $response = $this->actingAs($user)->post(route('subscription.update'), $request_parameter);
        // $response->assertRedirect(route('home'));
        $response->assertRedirect(route('subscription.edit'));

        $updated_payment_method_id = $user->refresh()->defaultPaymentMethod()->id;

        $this->assertNotEquals($default_payment_method_id, $updated_payment_method_id);
    }

    public function test_admin_cannot_update_payment() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $this->actingAs($admin, 'admin');

        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        $response = $this->post(route('subscription.update'), $request_parameter);

        $response->assertRedirect(route('admin.home'));
    }


    // cancel 有料プラン解約ページ
    public function test_guest_cannot_access_subscribe_cancel_page() 
    {
        $response = $this->get(route('subscription.cancel'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_subscribe_cancel_page() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.cancel'));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_subscribed_user_can_access_subscribe_cancel_page() 
    {
        $user = User::factory()->create();

        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('subscription.cancel'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_subscribe_cancel_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $this->actingAs($admin, 'admin');
        $response = $this->get(route('subscription.cancel'));

        $response->assertRedirect(route('admin.home'));
    }

    // destroy 有料プラン解約機能
    public function test_guest_cannot_cancel_subscription() 
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->post(route('subscription.destroy'), $request_parameter);

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_cancel_subscription() 
    {
        $user = User::factory()->create();

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->actingAs($user)->post(route('subscription.destroy'), $request_parameter);
        $response->assertRedirect(route('home'));

        // $user = $user->fresh();

        // $this->assertTrue($user->subscribed('premium_plan'));
    }

    public function test_subscribed_user_can_cancel_subscription() 
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Q1NrDJJw88cR1MUmJBD3cCV')->create('pm_card_visa');

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->actingAs($user)->post(route('subscription.destroy'), $request_parameter);
        // $response->assertRedirect(route('home'));
        $response->assertRedirect(route('subscription.edit'));

        $user = $user->fresh();
        $this->assertFalse($user->subscribed('premium_plan'));
    }

    public function test_admin_cannot_cancel_subscription() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $this->actingAs($admin, 'admin');

        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->post(route('subscription.destroy'), $request_parameter);

        $response->assertRedirect(route('admin.home'));
    }
}
