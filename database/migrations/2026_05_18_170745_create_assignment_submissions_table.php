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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ang Student na nag-submit
            $table->text('submission_text')->nullable(); // Kung text submission lang
            $table->string('file_path')->nullable(); // Kung may pinasang PDF/Docx/Zip file
            $table->integer('grade')->nullable(); // Nullable kasi hihintayin pa si Teacher mag-grade
            $table->text('teacher_feedback')->nullable();
            $table->dateTime('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
