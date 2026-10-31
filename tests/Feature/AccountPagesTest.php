<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_accounts_render_in_the_existing_tables(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);

        $customers = $this->get(route('customers'))->assertOk()
            ->assertSee('<th>ID</th><th>Name</th><th>Email</th><th>Phone</th>', false);

        foreach (Customer::all() as $customer) {
            $customers->assertSeeInOrder([
                '<td>'.$customer->id.'</td>',
                '<td>'.$customer->full_name.'</td>',
                '<td>'.$customer->email.'</td>',
                '<td>'.$customer->phone.'</td>',
            ], false);
        }

        $users = $this->get(route('users'))->assertOk()
            ->assertSee('<th>ID</th><th>Username</th><th>Full Name</th><th>Role</th>', false);

        foreach (User::all() as $user) {
            $users->assertSeeInOrder([
                '<td>'.$user->id.'</td>',
                '<td>'.$user->username.'</td>',
                '<td>'.$user->full_name.'</td>',
                '<td>'.$user->role.'</td>',
            ], false);
        }
    }

    public function test_pages_show_database_changes_and_escape_names(): void
    {
        $customer = Customer::create([
            'full_name' => '<script>Customer</script>',
            'email' => 'new@example.com',
        ]);
        $user = User::factory()->create(['full_name' => '<script>User</script>']);

        $this->assertNotNull($customer->created_at);
        $this->assertNotNull($user->created_at);

        $this->get(route('customers'))->assertOk()
            ->assertSee($customer->full_name)->assertDontSee($customer->full_name, false);
        $this->get(route('users'))->assertOk()
            ->assertSee($user->full_name)->assertDontSee($user->full_name, false);

        $customer->update(['full_name' => 'Changed Customer']);
        $user->update(['full_name' => 'Changed User']);

        $this->get(route('customers'))->assertSee('Changed Customer');
        $this->get(route('users'))->assertSee('Changed User');
    }

    public function test_empty_account_tables_render_successfully(): void
    {
        $this->get(route('customers'))->assertOk()->assertSee('Customer Accounts');
        $this->get(route('users'))->assertOk()->assertSee('User Accounts');
    }

    public function test_users_default_to_staff_and_usernames_are_unique(): void
    {
        $user = User::create(['username' => 'staff', 'full_name' => 'Staff Member']);
        $this->assertSame('Staff', $user->fresh()->role);

        $this->expectException(QueryException::class);

        User::create(['username' => 'staff', 'full_name' => 'Duplicate Staff']);
    }

    public function test_users_migration_can_roll_back_and_upgrade_existing_names(): void
    {
        $account = User::factory()->create();
        $migration = require database_path('migrations/2026_10_31_000001_match_users_to_tfa2_schema.php');
        $migration->down();

        $this->assertDatabaseHas('users', ['id' => $account->id, 'name' => $account->full_name]);

        $id = DB::table('users')->insertGetId([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => 'unused',
        ]);

        $migration->up();

        $user = User::findOrFail($id);
        $this->assertSame('Existing User', $user->full_name);
        $this->assertSame('user_'.$id, $user->username);
        $this->assertSame('Staff', $user->role);
        $this->assertNotNull($user->created_at);
    }
}
