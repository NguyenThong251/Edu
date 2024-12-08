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
        Schema::create('discussion_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Người dùng
            $table->unsignedBigInteger('discussion_thread_id'); // Câu hỏi hoặc câu trả lời
            $table->timestamps();

            // Khóa ngoại và chỉ mục
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('discussion_thread_id')->references('id')->on('discussion_threads')->onDelete('cascade');
            $table->unique(['user_id', 'discussion_thread_id']); // Mỗi người dùng chỉ yêu thích 1 lần
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_likes');
    }
};
