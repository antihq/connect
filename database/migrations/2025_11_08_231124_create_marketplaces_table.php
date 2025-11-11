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
        Schema::create('marketplaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('sender_email_name')->nullable();
            $table->boolean('is_private')->default(false);
$table->boolean('require_user_approval')->default(false);
$table->string('require_user_approval_action')->default('none');
$table->string('require_user_approval_internal_link')->nullable();
$table->string('require_user_approval_internal_text')->nullable();
$table->string('require_user_approval_external_link')->nullable();
$table->string('require_user_approval_external_text')->nullable();
$table->boolean('restrict_view_listings')->default(false);
$table->string('restrict_view_listings_action')->default('none');
$table->string('restrict_view_listings_internal_link')->nullable();
$table->string('restrict_view_listings_internal_text')->nullable();
$table->string('restrict_view_listings_external_link')->nullable();
$table->string('restrict_view_listings_external_text')->nullable();

$table->boolean('restrict_posting')->default(false);
$table->string('restrict_posting_action')->default('none');
$table->string('restrict_posting_internal_link')->nullable();
$table->string('restrict_posting_internal_text')->nullable();
$table->string('restrict_posting_external_link')->nullable();
$table->string('restrict_posting_external_text')->nullable();

$table->boolean('restrict_transactions')->default(false);
$table->string('restrict_transactions_action')->default('none');
$table->string('restrict_transactions_internal_link')->nullable();
$table->string('restrict_transactions_internal_text')->nullable();
$table->string('restrict_transactions_external_link')->nullable();
$table->string('restrict_transactions_external_text')->nullable();

$table->boolean('require_listing_approval')->default(false);
$table->string('require_listing_approval_action')->default('none');
$table->string('require_listing_approval_internal_link')->nullable();
$table->string('require_listing_approval_internal_text')->nullable();
$table->string('require_listing_approval_external_link')->nullable();
$table->string('require_listing_approval_external_text')->nullable();
$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplaces');
    }
};
