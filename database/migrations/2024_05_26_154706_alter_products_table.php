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
        Schema::table('products', function(Blueprint $table) {
            $table->text('short_description')->after('description')->nullable();
            $table->text('shipping_returns')->after('short_description')->nullable();
            $table->text('related_products')->after('shipping_returns')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function(Blueprint $table) {
            $table->dropColumn('short_description');
            $table->dropColumn('shipping_returns');
            $table->dropColumn('related_products');
        });
    }
};
