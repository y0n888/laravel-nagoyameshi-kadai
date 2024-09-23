<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Admin;
use App\Models\Company;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_company_index() 
    {
        Company::factory()->create();

        $response = $this->get(route('company.index'));
        $response->assertStatus(200);
    }

    public function test_regular_user_can_access_company_index() 
    {
        $user = User::factory()->create();
        Company::factory()->create();

        $response = $this->actingAs($user)->get(route('company.index'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_company_index() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        
        $this->actingAs($admin, 'admin');
        $response = $this->get(route('company.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
