<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'full_name');
            $table->string('username', 50)->nullable()->unique();
            $table->string('role', 20)->default('Staff');
        });

        // Preserve existing names and assign unique usernames when upgrading.
        DB::table('users')->orderBy('id')->each(function (object $user) {
            DB::table('users')->where('id', $user->id)->update([
                'username' => 'user_'.$user->id,
                'created_at' => $user->created_at ?? now(),
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name', 100)->change();
            $table->string('username', 50)->nullable(false)->change();
            $table->dateTime('created_at')->nullable(false)->change();
            $table->dropUnique(['email']);
            $table->dropColumn(['email', 'email_verified_at', 'password', 'remember_token', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('full_name', 'name');
            $table->string('name')->change();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default('');
            $table->rememberToken();
            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable();
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'role']);
        });

        DB::table('users')->orderBy('id')->each(function (object $user) {
            DB::table('users')->where('id', $user->id)->update([
                'email' => 'user_'.$user->id.'@example.com',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
