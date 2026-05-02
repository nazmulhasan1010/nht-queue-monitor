<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('queue_monitor_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('job_id')->nullable();
            $table->string('queue')->nullable();
            $table->string('connection')->nullable();
            $table->string('job_name')->nullable();
            $table->string('performed_by')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'created_at']);
            $table->index(['job_id']);
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_monitor_events');
    }
};
