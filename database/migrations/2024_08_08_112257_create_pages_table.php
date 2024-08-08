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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->mediumText('name');
            $table->mediumText('content');
            $table->string('page_type')->nullable();
            $table->string('order_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->string('link')->nullable();
            $table->string('img')->nullable();
            $table->string('video')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
