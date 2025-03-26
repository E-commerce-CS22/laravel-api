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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->boolean('is_parent')->default(false);
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->dateTime('discount_start_date')->nullable();
            $table->dateTime('discount_end_date')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
