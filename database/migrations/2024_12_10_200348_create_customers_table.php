<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('address');
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->string('country')->default('اليمن');
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};