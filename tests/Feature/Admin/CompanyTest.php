<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // 会社概要ページ
    public function test_guest_user_cannot_access_admin_company_index()
    {
        $response = $this->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_company_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_admin_company_index()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
    
        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/company');
        $response->assertStatus(200);
    }


    // 会社概要編集ページ
    public function test_guest_user_cannot_access_admin_company_edit()
    {
        $company = Company::factory()->create();

        $response = $this->get(route('admin.company.edit', $company));
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_company_edit()
    {
        $user = User::factory()->create();

        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.edit', $company));
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_admin_company_edit()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
    
        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company));
        $response->assertStatus(200);
    }


    // 会社概要更新機能
    public function test_guest_user_cannot_update_admin_company()
    {
        $company = Company::factory()->create();

        $response = $this->put(route('admin.company.update', $company), [
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_update_company()
    {
        $user = User::factory()->create();

        $company = Company::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.company.update', $company),[
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_update_admin_company()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
    
        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.company.update', $company), [
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ]);
        $response->assertRedirect(route('admin.company.index'));

        $this->assertDatabaseHas('companies', [
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ]);
    }

}
