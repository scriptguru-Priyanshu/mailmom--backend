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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->json('key_points');
            $table->json('decisions');
            $table->enum('status',[
                'pending',
                'approved'
            ]);
            $table->datetime('scheduled_at');     
            $table->longText('transcript');
            $table->longText('summary')->nullable();
            $table->json('action_items')->nullable();
            $table->json('participants')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
