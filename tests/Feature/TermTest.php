<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Term;

class TermTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_term_index() 
    {
        $term = Term::factory()->create();
        $response = $this->get(route('terms.index'));
        $response->assertStatus(200);
    }

    public function test_regular_user_can_access_term_index() 
    {
        $user = User::factory()->create();
        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('terms.index'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_term_index() 
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('nagoyameshi'),
        ]);
        $term = Term::factory()->create();
        
        $this->actingAs($admin, 'admin');
        $response = $this->get(route('terms.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
