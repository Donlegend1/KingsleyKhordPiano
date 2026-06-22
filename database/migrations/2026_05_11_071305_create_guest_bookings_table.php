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
        if (! Schema::hasTable('guest_bookings')) {
        Schema::create('guest_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->date('date');
            $table->string('time');
            $table->text('focus')->nullable();
            $table->string('skill_level')->default('Beginner');
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->string('payment_method')->nullable(); // stripe, paystack
            $table->string('stripe_session_id')->nullable();
            $table->string('paystack_reference')->nullable();
            $table->string('zoom_meeting_id')->nullable();
            $table->string('zoom_join_url')->nullable();
            $table->string('google_meet_link')->nullable();
            $table->string('google_calendar_event_id')->nullable();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_bookings');
    }
};
