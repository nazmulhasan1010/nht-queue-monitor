<?php
/*
 * Created by Antigravity AI
 */

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
        if (Schema::hasTable('queue_monitor_jobs')) {
            Schema::table('queue_monitor_jobs', function (Blueprint $table) {
                if (! Schema::hasColumn('queue_monitor_jobs', 'batch_id')) {
                    $table->string('batch_id')->nullable()->after('uuid')->index();
                }

                if (! Schema::hasColumn('queue_monitor_jobs', 'parent_id')) {
                    $table->string('parent_id')->nullable()->after('batch_id')->index();
                }
            });
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('queue_monitor_jobs', function (Blueprint $table) {
            foreach (['batch_id', 'parent_id'] as $column) {
                if (Schema::hasColumn('queue_monitor_jobs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
