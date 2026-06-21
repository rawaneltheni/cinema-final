<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'seat_numbers')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('seat_numbers', 255)->nullable()->after('chair_count');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'seat_numbers')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('seat_numbers');
            });
        }
    }
};
