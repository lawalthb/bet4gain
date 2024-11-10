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
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('game_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->decimal('cashout_multiplier', 10, 2)->nullable();
            $table->decimal('won_amount', 10, 2)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bets');
    }
};
