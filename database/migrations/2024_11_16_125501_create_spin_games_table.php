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
        Schema::create('spin_games', function (Blueprint $table) {
            $table->id();
            $table->string('result_color')->nullable();
            $table->decimal('multiplier', 8, 2)->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('started_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spin_games');
    }
};
