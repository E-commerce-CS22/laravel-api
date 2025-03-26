<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2)->nullable(); // Variant-specific price (overrides parent if set)
            $table->integer('stock')->default(0);
            $table->boolean('is_default')->default(false);
            $table->string('variant_title')->nullable();
            $table->json('images')->nullable(); // JSON array of image paths specific to this variant
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};