<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
        
            $table->timestamp('email_verified_at')->nullable();
            $table->string('image')->nullable();
            $table->string('referral_code')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->enum('is_active', ['Yes', 'No'])->default('Yes');
            $table->enum('is_ban', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('user_role_id')->default(2);
            $table->decimal('wallet_balance', 10, 2)->default(0);
            $table->string('withdraw_pin')->default('1234');

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
