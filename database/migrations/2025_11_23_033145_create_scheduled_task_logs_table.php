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
        Schema::create('scheduled_task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_task_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['running', 'success', 'failed', 'skipped']);
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in milliseconds');
            $table->text('output')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('exit_code')->nullable();
            $table->timestamps();

            $table->index('scheduled_task_id');
            $table->index('status');
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_task_logs');
    }
};
