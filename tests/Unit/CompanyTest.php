<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected Collection|Model $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        $this->token = JWTAuth::fromUser($this->user);
    }


    #[Test] public function an_authorized_user_can_create_a_company()
    {
        $this->withHeader('Authorization', "Bearer $this->token");

        // Define company data
        $companyData = [
            'name' => 'Test Company',
            'email' => 'test@company.com',
        ];

        // Send request to create a company
        $response = $this->postJson('/api/companies', $companyData);

        // Assert response status is 201 (Created)
        $response->assertStatus(201);

        // Assert the database has the new company
        $this->assertDatabaseHas('companies', $companyData);
    }
}

