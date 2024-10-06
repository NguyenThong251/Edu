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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('course_id');
            $table->double('price');
            $table->softDeletes();
            //$table->boolean('is_deleted')->default(0);
            $table->timestamps();

            // Primary key
            $table->primary(['cart_id', 'course_id']);

            // Foreign keys
            $table->foreign('cart_id')->references('user_id')->on('carts')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
