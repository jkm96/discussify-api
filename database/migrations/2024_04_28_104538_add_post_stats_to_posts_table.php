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
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('post_replies_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('views')->default(0);
            $table->integer('participants')->default(0);
            $table->integer('likes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('post_replies_count');
            $table->dropColumn('comments_count');
            $table->dropColumn('views');
            $table->dropColumn('participants');
            $table->dropColumn('likes');
        });
    }
};
