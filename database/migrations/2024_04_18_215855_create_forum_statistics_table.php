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
        Schema::create('forum_statistics', function (Blueprint $table) {
            $table->id();
            $table->integer('members')->default(0);
            $table->integer('posts')->default(0);
            $table->string('forum_id');
            $table->string('forum_name');
            $table->longText('forum_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_statistics');
    }
};
