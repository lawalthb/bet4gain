<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->decimal('amount', 10, 2);
            $table->decimal('cashout_multiplier', 10, 2)->nullable();
            $table->decimal('profit', 10, 2)->default(0);
            $table->boolean('is_demo')->default(false);
            $table->boolean('is_bot')->default(false);
            $table->string('bot_name')->nullable();
            $table->decimal('auto_cashout', 10, 2)->nullable();
            $table->enum('status', ['pending', 'won', 'lost'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bets');
    }
};
