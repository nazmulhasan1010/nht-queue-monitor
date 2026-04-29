<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_pulse_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable()->index();
            $table->string('connection')->nullable()->index();
            $table->string('queue')->nullable()->index();
            $table->string('job_name')->nullable()->index();
            $table->string('status')->default('processed')->index();
            $table->unsignedInteger('attempts')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->longText('exception')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('finished_at')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'finished_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_pulse_jobs');
    }
};
