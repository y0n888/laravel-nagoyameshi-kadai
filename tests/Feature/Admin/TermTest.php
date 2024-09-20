<?php

namespace Tests\Feature\Admin;

use App\Models\Term;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    // 利用規約ページ
    public function test_guest_user_cannot_access_admin_term_index()
    {
        $response = $this->get('/admin/terms');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_term_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/terms');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_admin_term_index()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
    
        $term = Term::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/terms');
        $response->assertStatus(200);
    }


    // 利用規約編集ページ
    public function test_guest_user_cannot_access_admin_term_edit()
    {
        $term = Term::factory()->create();

        $response = $this->get(route('admin.terms.edit', $term));
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_term_edit()
    {
        $user = User::factory()->create();

        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.terms.edit', $term));
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_admin_term_edit()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
    
        $term = Term::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.edit', $term));
        $response->assertStatus(200);
    }

    // 利用規約更新機能
    public function test_guest_user_cannot_update_admin_term()
    {
        $term = Term::factory()->create();

        $response = $this->put(route('admin.terms.update', $term), [
            'content' => 'テスト更新',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_update_term()
    {
        $user = User::factory()->create();

        $term = Term::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.terms.update', $term),[
            'content' => 'テスト更新',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_update_admin_term()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
    
        $term = Term::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.terms.update', $term), [
            'content' => 'テスト更新',
        ]);
        
        // $response->assertRedirect('/admin/terms');

        $response->assertStatus(302);
        $this->assertDatabaseHas('terms', [
            'content' => 'テスト更新',
        ]);
    }
}
