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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('plexy_link_id')->nullable()->after('transaction_id');
            $table->string('plexy_checkout_url')->nullable()->after('plexy_link_id');
            $table->string('order_reference')->nullable()->unique()->after('plexy_checkout_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['plexy_link_id', 'plexy_checkout_url', 'order_reference']);
        });
    }
};
