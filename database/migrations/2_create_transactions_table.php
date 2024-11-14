
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference', 20)->unique();
            $table->decimal('amount', 20, 2);
            $table->string('gateway_response', 100)->nullable();
            $table->string('paid_at', 150)->nullable();
            $table->timestamps();
            $table->string('channel', 100)->nullable();
            $table->string('currency', 5)->default('USD');
            $table->string('ip_address', 60);
            $table->mediumText('metadata')->nullable();
            $table->string('fees', 150)->nullable();
            $table->mediumText('authorization_url')->nullable();
            $table->enum('status', ['Pending', 'Success', 'Failed'])->default('Pending');
            $table->mediumText('others')->nullable();
            $table->string('domain', 20)->nullable();
            $table->string('email', 100);
            $table->string('phone', 100);
            $table->string('callback_url', 255)->nullable();
            $table->enum('type', ['Deposit', 'Withdrawal', 'Bonus'])->default('Deposit');
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_hash', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('account_name', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
