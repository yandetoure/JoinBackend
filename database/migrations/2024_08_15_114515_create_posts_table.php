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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->text('image')->nullable();
            $table->unsignedBigInteger('user_id'); // Pour la relation avec un utilisateur
            $table->boolean('is_deleted')->default(false);
            $table->text('updated_message')->nullable();
            $table->timestamp('modified_at')->nullable();
            $table->softDeletes();  // Soft delete column
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
