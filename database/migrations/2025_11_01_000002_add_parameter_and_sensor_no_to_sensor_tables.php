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
        Schema::table('sensor_data', function (Blueprint $table) {
            if (!Schema::hasColumn('sensor_data', 'parameter')) {
                $table->string('parameter', 50)->default('ph')->after('id');
            }
            if (!Schema::hasColumn('sensor_data', 'sensor_no')) {
                $table->unsignedInteger('sensor_no')->default(1)->after('parameter');
            }
            if (!Schema::hasColumn('sensor_data', 'value')) {
                $table->float('value')->nullable()->after('sensor_no');
            }

            if (!Schema::hasColumn('sensor_data', 'status_pump_ph')) {
                $table->boolean('status_pump_ph')->default(false)->after('value');
            }
            if (!Schema::hasColumn('sensor_data', 'status_pump_ppm')) {
                $table->boolean('status_pump_ppm')->default(false)->after('status_pump_ph');
            }

            // Index untuk pencarian cepat per parameter + sensor
            $table->index(['parameter', 'sensor_no'], 'sensor_data_param_sensor_idx');
        });

        Schema::table('sensor_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('sensor_histories', 'parameter')) {
                $table->string('parameter', 50)->default('ph')->after('id');
            }
            if (!Schema::hasColumn('sensor_histories', 'sensor_no')) {
                $table->unsignedInteger('sensor_no')->default(1)->after('parameter');
            }
            if (!Schema::hasColumn('sensor_histories', 'value')) {
                $table->float('value')->nullable()->after('sensor_no');
            }
            if (!Schema::hasColumn('sensor_histories', 'status_pump_ph')) {
                $table->boolean('status_pump_ph')->default(false)->after('value');
            }
            if (!Schema::hasColumn('sensor_histories', 'status_pump_ppm')) {
                $table->boolean('status_pump_ppm')->default(false)->after('status_pump_ph');
            }

            $table->index(['parameter', 'sensor_no'], 'sensor_histories_param_sensor_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            if (Schema::hasColumn('sensor_data', 'parameter')) {
                $table->dropColumn('parameter');
            }
            if (Schema::hasColumn('sensor_data', 'sensor_no')) {
                $table->dropColumn('sensor_no');
            }
            if (Schema::hasColumn('sensor_data', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('sensor_data', 'status_pump_ph')) {
                $table->dropColumn('status_pump_ph');
            }
            if (Schema::hasColumn('sensor_data', 'status_pump_ppm')) {
                $table->dropColumn('status_pump_ppm');
            }
            $table->dropIndex('sensor_data_param_sensor_idx');
        });

        Schema::table('sensor_histories', function (Blueprint $table) {
            if (Schema::hasColumn('sensor_histories', 'parameter')) {
                $table->dropColumn('parameter');
            }
            if (Schema::hasColumn('sensor_histories', 'sensor_no')) {
                $table->dropColumn('sensor_no');
            }
            if (Schema::hasColumn('sensor_histories', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('sensor_histories', 'status_pump_ph')) {
                $table->dropColumn('status_pump_ph');
            }
            if (Schema::hasColumn('sensor_histories', 'status_pump_ppm')) {
                $table->dropColumn('status_pump_ppm');
            }
            $table->dropIndex('sensor_histories_param_sensor_idx');
        });
    }
};
