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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rater_id'); // Who gives the rating
            $table->unsignedBigInteger('rated_id'); // Who receives the rating
            $table->unsignedBigInteger('job_id'); // Which job this rating is for
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable(); // Optional comment
            $table->enum('type', ['worker_to_employer', 'employer_to_worker']); // Type of rating
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('rater_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rated_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('pekerjaans')->onDelete('cascade');

            // Ensure one rating per person per job per type
            $table->unique(['rater_id', 'rated_id', 'job_id', 'type'], 'unique_rating_per_job');
            
            // Add indexes for better performance
            $table->index(['rated_id', 'type']);
            $table->index(['job_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
