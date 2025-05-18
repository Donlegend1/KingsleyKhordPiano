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
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['admin', 'superadmin', 'member'])->default('member');
            $table->string('get_started')->default('no');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
          

            // Subscription-related fields
            $table->string('plan')->nullable(); // Add plan column
            $table->string('amount')->nullable(); // Add amount column

            // Payment details
            $table->string('payment_status')->default('pending'); // e.g., 'pending', 'completed'
            $table->string('payment_method')->nullable(); // e.g., 'card', 'crypto', 'bank'
            $table->string('last_payment_reference')->nullable(); // e.g., Stripe or Paystack reference
            $table->decimal('last_payment_amount', 10, 2)->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
