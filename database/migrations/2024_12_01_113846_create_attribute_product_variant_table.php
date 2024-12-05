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
        Schema::create('attribute_product_variant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade'); // Variant ID
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade'); // Attribute ID
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade'); // Attribute Value ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_product_variant');
    }
};
