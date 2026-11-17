<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_account_routes_redirect_guests_without_changing_records(): void
    {
        $customer = Customer::create(['full_name' => 'Customer', 'email' => 'customer@example.com']);
        $user = User::factory()->create();

        foreach (['customers' => $customer, 'users' => $user] as $resource => $record) {
            foreach ([
                ['GET', route("$resource.index")],
                ['GET', route("$resource.create")],
                ['POST', route("$resource.store")],
                ['GET', route("$resource.edit", $record)],
                ['PUT', route("$resource.update", $record)],
                ['PATCH', route("$resource.update", $record)],
            ] as [$method, $url]) {
                $this->call($method, $url, ['full_name' => 'Unauthorized change'])
                    ->assertRedirect(route('login'));
            }
        }

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('Customer', $customer->fresh()->full_name);
        $this->assertSame($user->full_name, $user->fresh()->full_name);
    }

    public function test_login_returns_to_each_requested_resource_and_logout_protects_it_again(): void
    {
        $user = User::factory()->create();

        foreach (['customers.index', 'users.index'] as $route) {
            $url = route($route);
            $this->get($url)->assertRedirect(route('login'));
            $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
                ->assertSessionHasNoErrors()->assertRedirect($url);
            $this->assertAuthenticatedAs($user);
            $this->get($url)->assertOk();
            $this->post(route('logout'))->assertRedirect('/');
            $this->assertGuest();
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_registration_preserves_pos_fields_and_does_not_accept_an_elevated_role(): void
    {
        $this->post(route('register'), [
            'username' => 'newcashier',
            'full_name' => 'New Cashier',
            'email' => 'cashier@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role' => 'Admin',
        ])->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));

        $user = User::sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('newcashier', $user->username);
        $this->assertSame('New Cashier', $user->full_name);
        $this->assertSame('Staff', $user->role);
        $this->assertTrue(Hash::check('secure-password', $user->password));
        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->get(route('customers.index'))->assertOk();
        $this->get(route('users.index'))->assertOk();
    }

    public function test_registration_rejects_duplicate_credentials_and_invalid_passwords(): void
    {
        $user = User::factory()->create();
        $this->post(route('register'), [
            'username' => $user->username,
            'full_name' => str_repeat('a', 101),
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['username', 'full_name', 'email', 'password']);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_authentication_migration_preserves_existing_accounts(): void
    {
        $migration = require database_path('migrations/2026_11_17_000000_add_authentication_to_users_table.php');
        $migration->down();
        $user = User::create(['username' => 'legacy', 'full_name' => 'Legacy User']);
        $migration->up();

        $user->refresh();
        $this->assertSame('legacy', $user->username);
        $this->assertSame('Legacy User', $user->full_name);
        $this->assertNull($user->email);
        $this->assertNull($user->password);
        $this->post(route('login'), ['email' => 'legacy@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
