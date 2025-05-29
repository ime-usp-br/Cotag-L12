<?php

namespace Tests\Unit\Seeders;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached roles and permissions before each test
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_role_seeder_creates_all_expected_roles_with_web_guard(): void
    {
        // Execute the seeder
        $seeder = new RoleSeeder();
        $seeder->run();

        // Define expected roles
        $expectedRoles = [
            'Admin',
            'Operator',
            'User',
            'usp_user',
            'external_user',
        ];

        foreach ($expectedRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            $this->assertNotNull($role, "Role '{$roleName}' with guard 'web' was not created.");
            $this->assertEquals($roleName, $role->name);
            $this->assertEquals('web', $role->guard_name);
        }

        // Verify the total count of roles created by this seeder (assuming these are the only ones)
        // This count could be adjusted if other seeders or default roles are involved.
        // For now, we check that at least these 5 exist.
        $this->assertGreaterThanOrEqual(count($expectedRoles), Role::count());
    }

    public function test_role_seeder_does_not_create_duplicate_roles_on_multiple_runs(): void
    {
        // Execute the seeder once
        $seeder = new RoleSeeder();
        $seeder->run();

        $countAfterFirstRun = Role::count();

        // Execute the seeder again
        $seeder->run();

        $this->assertEquals($countAfterFirstRun, Role::count(), 'RoleSeeder created duplicate roles on a second run.');
    }

    public function test_admin_role_is_created(): void
    {
        $seeder = new RoleSeeder();
        $seeder->run();
        $this->assertDatabaseHas('roles', ['name' => 'Admin', 'guard_name' => 'web']);
    }

    public function test_operator_role_is_created(): void
    {
        $seeder = new RoleSeeder();
        $seeder->run();
        $this->assertDatabaseHas('roles', ['name' => 'Operator', 'guard_name' => 'web']);
    }

    public function test_user_role_is_created(): void
    {
        $seeder = new RoleSeeder();
        $seeder->run();
        $this->assertDatabaseHas('roles', ['name' => 'User', 'guard_name' => 'web']);
    }

    public function test_usp_user_role_is_created(): void
    {
        $seeder = new RoleSeeder();
        $seeder->run();
        $this->assertDatabaseHas('roles', ['name' => 'usp_user', 'guard_name' => 'web']);
    }

    public function test_external_user_role_is_created(): void
    {
        $seeder = new RoleSeeder();
        $seeder->run();
        $this->assertDatabaseHas('roles', ['name' => 'external_user', 'guard_name' => 'web']);
    }
}