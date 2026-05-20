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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Student
    
            // The "meat" of the report
            $table->text('ai_summary'); // A short 1-2 sentence overview
            $table->longText('detailed_feedback'); // The full breakdown from the AI
            
            // Meta-data for quick filtering
            $table->string('performance_level')->nullable(); // e.g., 'Excellent', 'Struggling', 'Improving'
            $table->json('suggested_lessons')->nullable(); // AI can suggest IDs of lessons to review
            
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
