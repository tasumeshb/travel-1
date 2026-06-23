<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bravo_spaces') || !Schema::hasColumn('bravo_spaces', 'booking_url')) {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM bravo_spaces WHERE Field = 'booking_url'"))->first();
        if ($column && stripos($column->Type, 'int') !== false) {
            DB::statement('ALTER TABLE bravo_spaces MODIFY booking_url VARCHAR(500) NULL');
        }
    }

    public function down(): void
    {
        // Irreversible without data loss — leave column as VARCHAR.
    }
};
