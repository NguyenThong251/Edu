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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('title', 100);
            $table->text('description');
            $table->string('thumbnail');
            $table->double('price');
            $table->enum('type_sale', ['percent', 'price'])->default('price');
            $table->double('sale_value');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->softDeletes();
            $table->bigInteger('deleted_by')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Foreign key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
