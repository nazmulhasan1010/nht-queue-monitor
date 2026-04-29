
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_pulse_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_key')->index();
            $table->string('level')->default('warning')->index();
            $table->string('title');
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_pulse_alerts');
    }
};
