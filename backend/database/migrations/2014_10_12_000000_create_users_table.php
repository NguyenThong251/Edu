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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 255)->unique();
            $table->string('email', 255)->unique();
            $table->string('role', 50);
            $table->string('password');
            $table->boolean('email_verified')->default(false);
            $table->string('reset_token')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->softDeletes();
            $table->is_deleted()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
