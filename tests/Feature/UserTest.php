<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // 会員情報ページ
    public function test_guest_cannot_access_user_index_page()
    {
        $response = $this->get(route('user.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_can_access_user_index_page() 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.index'));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_user_index_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('user.index'));
        
        $response->assertRedirect(route('admin.home'));
    }

    // 会員情報編集ページ
    public function test_guest_cannot_access_user_edit_page()
    {
        $response = $this->get(route('user.edit', ['user' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_other_user_edit_page() 
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('user.edit', ['user' => $otherUser->id]));
        $response->assertRedirect(route('user.index'));
    }

    public function test_regular_user_can_access_own_user_edit_page() 
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get(route('user.edit', ['user' => $user->id]));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_user_edit_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('user.edit', ['user' => $user->id]));
        $response->assertRedirect(route('admin.home'));
    }

    // 会員情報更新機能
    public function test_guest_cannot_update_user_edit_page()
    {
        $response = $this->put(route('user.update', ['user' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_update_other_user_edit_page() 
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);
        $response = $this->put(route('user.update', ['user' => $otherUser->id],),[
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'new@example.com',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'phone_number' => '12345678910',
            'birthday' => '20001111',
            'occupation' => 'テスト更新',
        ]);

        $response->assertRedirect(route('user.index'));
    }

    public function test_regular_user_can_update_own_user_edit_page() 
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->put(route('user.update', ['user' => $user->id],),[
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'new@example.com',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'phone_number' => '12345678910',
            'birthday' => '20001111',
            'occupation' => 'テスト更新',
        ]);

        $response->assertRedirect(route('user.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'new@example.com',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'phone_number' => '12345678910',
            'birthday' => '20001111',
            'occupation' => 'テスト更新',
        ]);
    }

    public function test_admin_user_cannot_update_user_edit_page() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);

        $user = User::factory()->create();
        
        $this->actingAs($admin, 'admin');

        $response = $this->put(route('user.update', ['user' => $user->id],),[
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'new@example.com',
            'postal_code' => '11111111',
            'address' => 'テスト更新',
            'phone_number' => '123455678910',
            'birthday' => '20001111',
            'occupation' => 'テスト更新',
        ]);

        $response->assertRedirect(route('admin.home'));
    }
}
