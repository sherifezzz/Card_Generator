<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // This creates a column named `id` (BIGINT UNSIGNED)
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password_hash');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('full_name', 100);
            $table->string('phone', 15)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->dateTime('joined_date')->useCurrent();
            $table->dateTime('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
