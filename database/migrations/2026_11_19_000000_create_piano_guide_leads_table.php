<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piano_guide_leads', function (Blueprint $table) {
            $table->id();
            $table->string('guide_slug', 100);
            $table->string('email', 254);
            $table->timestamp('created_at');
            $table->unique(['guide_slug', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piano_guide_leads');
    }
};
