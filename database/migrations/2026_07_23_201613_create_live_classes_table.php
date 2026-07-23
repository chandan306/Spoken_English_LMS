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
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('teacher_name');
            $table->date('class_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('meeting_link');
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->enum('status',['Upcoming','Live','Completed'])->default('Upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
