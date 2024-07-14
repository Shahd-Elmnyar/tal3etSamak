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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address', 255);
            $table->text('details')->nullable();
            $table->string('building', 100)->nullable();
            $table->string('floor', 50)->nullable();
            $table->string('apartment', 50)->nullable();
            $table->string('type');
            $table->text('information')->nullable();
            $table->string('city_of_residence', 100);
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
