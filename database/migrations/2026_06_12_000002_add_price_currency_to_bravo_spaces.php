<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bravo_spaces') || Schema::hasColumn('bravo_spaces', 'price_currency')) {
            return;
        }

        Schema::table('bravo_spaces', function (Blueprint $table) {
            $table->string('price_currency', 10)->nullable();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('bravo_spaces') || !Schema::hasColumn('bravo_spaces', 'price_currency')) {
            return;
        }

        Schema::table('bravo_spaces', function (Blueprint $table) {
            $table->dropColumn('price_currency');
        });
    }
};
