<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_monitor_jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('queue_monitor_jobs', 'node_name')) {
                $table->string('node_name')->nullable()->after('queue')->index();
            }

            if (! Schema::hasColumn('queue_monitor_jobs', 'tenant_id')) {
                $table->string('tenant_id')->nullable()->after('node_name')->index();
            }

            if (! Schema::hasColumn('queue_monitor_jobs', 'tags')) {
                $table->json('tags')->nullable()->after('payload');
            }

            if (! Schema::hasColumn('queue_monitor_jobs', 'insight')) {
                $table->text('insight')->nullable()->after('exception');
            }
        });
    }

    public function down(): void
    {
        Schema::table('queue_monitor_jobs', function (Blueprint $table) {
            foreach (['node_name', 'tenant_id', 'tags', 'insight'] as $column) {
                if (Schema::hasColumn('queue_monitor_jobs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
