<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seat_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 80);
            $table->string('movie_title', 120);
            $table->string('theater', 60);
            $table->string('seat_number', 10);
            $table->date('show_date');
            $table->time('show_time');
            $table->timestamps();

            $table->unique([
                'theater',
                'movie_title',
                'show_date',
                'show_time',
                'seat_number',
            ], 'seat_reservations_unique_show_seat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_reservations');
    }
};
