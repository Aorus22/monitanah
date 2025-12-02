<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            if (Schema::hasColumn('sensor_data', 'ph')) {
                $table->dropColumn('ph');
            }
            if (Schema::hasColumn('sensor_data', 'suhu')) {
                $table->dropColumn('suhu');
            }
            if (Schema::hasColumn('sensor_data', 'tds')) {
                $table->dropColumn('tds');
            }
        });

        Schema::table('sensor_histories', function (Blueprint $table) {
            if (Schema::hasColumn('sensor_histories', 'ph')) {
                $table->dropColumn('ph');
            }
            if (Schema::hasColumn('sensor_histories', 'suhu')) {
                $table->dropColumn('suhu');
            }
            if (Schema::hasColumn('sensor_histories', 'tds')) {
                $table->dropColumn('tds');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            if (!Schema::hasColumn('sensor_data', 'ph')) {
                $table->float('ph')->nullable()->after('value');
            }
            if (!Schema::hasColumn('sensor_data', 'suhu')) {
                $table->float('suhu')->nullable()->after('ph');
            }
            if (!Schema::hasColumn('sensor_data', 'tds')) {
                $table->float('tds')->nullable()->after('suhu');
            }
        });

        Schema::table('sensor_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('sensor_histories', 'ph')) {
                $table->float('ph')->nullable()->after('value');
            }
            if (!Schema::hasColumn('sensor_histories', 'suhu')) {
                $table->float('suhu')->nullable()->after('ph');
            }
            if (!Schema::hasColumn('sensor_histories', 'tds')) {
                $table->float('tds')->nullable()->after('suhu');
            }
        });
    }
};
