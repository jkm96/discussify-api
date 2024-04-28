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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('posts_count')->default(0);
            $table->integer('post_replies_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('points_earned')->default(0);
            $table->integer('reaction_score')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('posts_count');
            $table->dropColumn('post_replies_count');
            $table->dropColumn('comments_count');
            $table->dropColumn('points_earned');
            $table->dropColumn('reaction_score');
        });
    }
};
