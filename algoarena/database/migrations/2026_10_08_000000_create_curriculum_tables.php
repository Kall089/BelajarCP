<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('track'); // graph | dp
            $table->unsignedInteger('position');
            $table->string('title');
            $table->string('subtitle');
            $table->text('summary');
            $table->string('viz'); // jenis visualizer
            $table->unsignedInteger('minutes')->default(15);
            $table->timestamps();
        });

        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at');
            $table->unique(['user_id', 'lesson_id']);
        });

        Schema::create('problems', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('position');
            $table->string('title');
            $table->string('difficulty'); // Mudah | Sedang | Sulit
            $table->json('tags');
            $table->text('statement');
            $table->text('input_format');
            $table->text('output_format');
            $table->text('constraints');
            $table->json('starter');   // kode awal per bahasa
            $table->text('editorial'); // pembahasan
            $table->json('solutions'); // solusi per bahasa
            $table->unsignedInteger('time_limit')->default(2000); // ms, untuk JavaScript
            $table->timestamps();
        });

        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->boolean('is_sample')->default(false);
            $table->longText('input');
            $table->longText('output');
            $table->text('explanation')->nullable();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('problem_id')->constrained()->cascadeOnDelete();
            $table->string('language');
            $table->longText('code');
            $table->string('verdict'); // AC | WA | TLE | RE | PARTIAL
            $table->unsignedTinyInteger('score');
            $table->unsignedInteger('passed');
            $table->unsignedInteger('total');
            $table->unsignedInteger('time_ms')->default(0);
            $table->json('results');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('test_cases');
        Schema::dropIfExists('problems');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('lessons');
    }
};
