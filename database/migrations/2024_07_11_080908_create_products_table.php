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
            $table->mediumText('name');
            $table->mediumText('content');
            $table->double('price');
            $table->double('offer_price')->nullable();
            $table->double('start')->nullable();
            $table->double('skip')->nullable();
            $table->string('discount_type');
            $table->double('discount')->nullable();
            $table->boolean('is_offer')->default(0);
            $table->boolean('is_sale')->default(0);
            $table->tinyInteger('active')->default(1);
            // $table->foreignId('addition_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            // $table->foreignId('category_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            // $table->foreignId('size_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            // $table->foreignId('image_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
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
