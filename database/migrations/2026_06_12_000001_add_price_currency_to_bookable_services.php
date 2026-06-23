<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'bravo_hotels',
            'bravo_tours',
            'bravo_cars',
            'bravo_events',
            'bravo_boats',
            'bravo_spaces',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'price_currency')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('price_currency', 10)->nullable();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'bravo_hotels',
            'bravo_tours',
            'bravo_cars',
            'bravo_events',
            'bravo_boats',
            'bravo_spaces',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'price_currency')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('price_currency');
            });
        }
    }
};
