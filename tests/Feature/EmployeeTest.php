<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    use RefreshDatabase;

    public function test_can_create_company()
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($admin);

        $response = $this->post('/api/company', [
            'name' => 'Test Company',
            'email' => 'company@example.com',
            'phone_number' => '1234567890',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ])
            ->assertJson([
                'status' => true,
                'message' => 'Company created successfully'
            ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'email' => 'company@example.com',
            'phone_number' => '1234567890'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Manager_ Test Company',
            'email' => 'company@example.com',
            'role' => 'manager'
        ]);

        $this->assertDatabaseHas('employees', [
            'name' => 'Employee_ Test Company',
            'role' => 'manager'
        ]);
    }

    public function test_cannot_create_company_if_not_super_admin()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $this->actingAs($user);

        $response = $this->post('/api/company', [
            'name' => 'Test Company',
            'email' => 'company@example.com',
            'phone_number' => '1234567890',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => false,
                'message' => 'Unauthorized access. Only super admin can access this function.'
            ]);
    }

    public function test_cannot_create_company_with_invalid_data()
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($admin);

        $response = $this->post('/api/company', [
            'name' => '',
            'email' => 'invalid-email',
            'phone_number' => 'invalid-phone',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors'
            ]);
    }
}