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
        Schema::table('forums', function (Blueprint $table) {
            $table->dropColumn('post_count');
            $table->integer('threads')->default(0);
            $table->integer('messages')->default(0);
            $table->integer('participants')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            $table->integer('post_count')->default(0);
            $table->dropColumn('threads');
            $table->dropColumn('messages');
            $table->dropColumn('participants');
        });
    }
};
