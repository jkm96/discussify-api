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
        Schema::create('entity_counts', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // Type of entity (e.g., forum, post, post_reply, comment)
            $table->unsignedBigInteger('entity_id'); // ID of the entity
            $table->string('interaction_type'); // Type of interaction (e.g., like, view)
            $table->integer('count')->default(0); // Count of interactions
            $table->timestamps();

            $table->index(['entity_type', 'entity_id', 'interaction_type']); // Composite index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_counts');
    }
};
