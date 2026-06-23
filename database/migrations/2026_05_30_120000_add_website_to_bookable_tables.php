<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'bravo_spaces',
            'bravo_tours',
            'bravo_hotels',
            'bravo_cars',
            'bravo_boats',
            'bravo_events',
            'bravo_flights',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || Schema::hasColumn($table, 'website')) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (Schema::hasColumn($table, 'booking_url')) {
                    $blueprint->string('website', 500)->nullable()->after('booking_url');
                } else {
                    $blueprint->string('website', 500)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'bravo_spaces',
            'bravo_tours',
            'bravo_hotels',
            'bravo_cars',
            'bravo_boats',
            'bravo_events',
            'bravo_flights',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'website')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('website');
                });
            }
        }
    }
};
