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
        Schema::create('marketplace_payout_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketplace_id')->constrained()->cascadeOnDelete();
            $table->string('account_type');
            $table->string('country', 2);
            $table->string('stripe_account_id')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'marketplace_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_payout_settings');
    }
};
