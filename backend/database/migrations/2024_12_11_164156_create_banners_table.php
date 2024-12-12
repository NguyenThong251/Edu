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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('position'); // header, footer, sidebar, etc.
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->boolean('status')->default(1); // Trạng thái: 1 (hiện), 0 (ẩn)
            $table->integer('priority')->default(0); // Độ ưu tiên hiển thị
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
