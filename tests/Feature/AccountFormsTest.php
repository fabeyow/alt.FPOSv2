<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class AccountFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_created_and_edited_with_prefilled_values(): void
    {
        $this->get('/customers/create')->assertOk()->assertSee('Create Customer');
        $data = ['full_name' => 'Customer One', 'email' => 'one@example.com', 'phone' => '123'];
        $this->post('/customers', $data)->assertRedirect(route('customers'));
        $customer = Customer::sole();
        $this->assertDatabaseHas('customers', $data);
        $this->get(route('customers.edit', $customer))->assertOk()
            ->assertSee('value="Customer One"', false)->assertSee('value="one@example.com"', false)
            ->assertSee('value="123"', false);

        $this->put(route('customers.update', $customer), [
            'full_name' => 'Changed Customer', 'email' => 'changed@example.com', 'phone' => '',
        ])->assertRedirect(route('customers'));
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id, 'full_name' => 'Changed Customer', 'email' => 'changed@example.com', 'phone' => null,
        ]);
    }

    public function test_customer_validation_preserves_input_and_does_not_write_invalid_data(): void
    {
        $this->from('/customers/create')->post('/customers', ['full_name' => 'Retained', 'email' => 'invalid'])
            ->assertRedirect('/customers/create')->assertSessionHasErrors('email')->assertSessionHasInput('full_name', 'Retained');
        $this->get('/customers/create')->assertSee('value="Retained"', false)->assertSee('Please correct the following:');
        $this->post('/customers', [])->assertSessionHasErrors(['full_name', 'email']);
        $this->assertDatabaseCount('customers', 0);

        $customer = Customer::create(['full_name' => 'Original', 'email' => 'original@example.com']);
        $this->put(route('customers.update', $customer), ['full_name' => '', 'email' => 'bad'])
            ->assertSessionHasErrors(['full_name', 'email']);
        $this->assertSame('Original', $customer->fresh()->full_name);
    }

    public function test_user_creation_and_editing_enforce_unique_usernames(): void
    {
        $this->get('/users/create')->assertOk()->assertSee('Create User');
        $this->post('/users', [])->assertSessionHasErrors(['username', 'full_name']);
        $this->post('/users', ['username' => 'first', 'full_name' => 'First User', 'avatar' => 'untrusted.jpg'])
            ->assertRedirect(route('users'));
        $user = User::sole();
        $this->assertSame('Staff', $user->role);
        $this->assertNull($user->avatar);
        $this->post('/users', ['username' => 'first', 'full_name' => 'Duplicate'])
            ->assertSessionHasErrors('username');
        $this->assertDatabaseCount('users', 1);
        $this->flushSession();
        $this->get(route('users.edit', $user))->assertOk()
            ->assertSee('value="first"', false)->assertSee('value="First User"', false)
            ->assertSee('enctype="multipart/form-data"', false);
        $this->put(route('users.update', $user), [
            'username' => 'first', 'full_name' => 'Changed User', 'role' => 'Manager',
        ])->assertRedirect(route('users'));
        $this->assertSame('Changed User', $user->fresh()->full_name);
        $other = User::factory()->create();
        $this->put(route('users.update', $user), ['username' => $other->username, 'full_name' => 'Bad'])
            ->assertSessionHasErrors('username');
        $this->put(route('users.update', $user), ['username' => 'first', 'full_name' => ''])
            ->assertSessionHasErrors('full_name');
        $this->assertSame('first', $user->fresh()->username);
    }

    public function test_avatar_is_prepared_replaced_and_preserved_without_a_new_upload(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $data = ['username' => $user->username, 'full_name' => $user->full_name];
        $this->get('/users')->assertSee(asset('images/avatar-placeholder.svg'));
        $this->put(route('users.update', $user), $data + [
            'avatar' => UploadedFile::fake()->image('portrait.png', 800, 400),
        ])->assertRedirect(route('users'));

        $path = $user->fresh()->avatar;
        $this->assertStringStartsWith('avatars/', $path);
        $this->assertStringEndsWith('.jpg', $path);
        Storage::disk('public')->assertExists($path);
        $image = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame([256, 256, 'image/jpeg'], [$image[0], $image[1], $image['mime']]);
        $this->get('/users')->assertSee(Storage::disk('public')->url($path));
        $this->put(route('users.update', $user), $data)->assertRedirect(route('users'));
        $this->assertSame($path, $user->fresh()->avatar);
        Storage::disk('public')->assertExists($path);

        $this->put(route('users.update', $user), $data + [
            'avatar' => UploadedFile::fake()->image('replacement.jpg', 100, 200),
        ])->assertRedirect(route('users'));
        $newPath = $user->fresh()->avatar;
        $this->assertNotSame($path, $newPath);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_invalid_avatars_do_not_change_the_account_or_existing_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/existing.jpg', 'existing');
        $user = User::factory()->create(['avatar' => 'avatars/existing.jpg']);
        foreach ([
            UploadedFile::fake()->create('document.txt', 1, 'text/plain'),
            UploadedFile::fake()->image('large.jpg')->size(2049),
            UploadedFile::fake()->createWithContent('script.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
        ] as $file) {
            $this->put(route('users.update', $user), [
                'username' => $user->username, 'full_name' => 'Invalid change', 'avatar' => $file,
            ])->assertSessionHasErrors('avatar');
        }
        $this->assertSame($user->full_name, $user->fresh()->full_name);
        $this->assertSame('avatars/existing.jpg', $user->fresh()->avatar);
        $this->assertSame(['avatars/existing.jpg'], Storage::disk('public')->allFiles());
    }

    public function test_missing_accounts_return_not_found(): void
    {
        foreach (['customers', 'users'] as $resource) {
            $this->get("/$resource/999/edit")->assertNotFound();
            $this->put("/$resource/999", [])->assertNotFound();
        }
    }

    public function test_failed_account_update_removes_new_avatar_and_keeps_old_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/existing.jpg', 'existing');
        $user = User::factory()->create(['avatar' => 'avatars/existing.jpg']);
        User::updating(function () {
            throw new RuntimeException('Database update failed.');
        });

        try {
            $this->put(route('users.update', $user), [
                'username' => $user->username, 'full_name' => 'Changed',
                'avatar' => UploadedFile::fake()->image('new.png', 300, 300),
            ])->assertStatus(500);
        } finally {
            User::flushEventListeners();
        }

        $this->assertSame('avatars/existing.jpg', $user->fresh()->avatar);
        $this->assertSame(['avatars/existing.jpg'], Storage::disk('public')->allFiles());
    }

    public function test_avatar_migration_can_roll_back_without_removing_accounts(): void
    {
        $user = User::factory()->create();
        $migration = require database_path('migrations/2026_11_12_000000_add_avatar_to_users_table.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('users', 'avatar'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'username' => $user->username]);
        $migration->up();
        $this->assertNull($user->fresh()->avatar);
    }
}
