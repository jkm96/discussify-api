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
            $table->boolean('is_system')->default(0);
            $table->integer('views')->default(0);
            $table->integer('post_count')->default(0);
            $table->integer('likes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forums', function (Blueprint $table) {
            $table->dropColumn('is_system');
            $table->dropColumn('views');
            $table->dropColumn('post_count');
            $table->dropColumn('likes');
        });
    }
};
