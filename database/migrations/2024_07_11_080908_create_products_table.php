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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('img');
            $table->decimal('price', 8, 2);
            $table->decimal('offer_price', 8, 2);
            $table->enum('discount_type', ['percent', 'value']);
            $table->integer('discount')->nullable();
            $table->boolean('offer')->default(0);
            $table->boolean('sale')->default(0);
            $table->boolean('active')->default(1);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
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
