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
            // Each reservation belongs to one showtime (the "movie" being booked).
            $table->unsignedInteger('show_id');
            $table->string('customer_name', 80);
            $table->string('seat_number', 2);
            $table->timestamps();

            $table->foreign('show_id')
                ->references('show_id')
                ->on('showtimes')
                ->cascadeOnDelete();

            // A given seat can only be booked once per showtime.
            $table->unique(['show_id', 'seat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_reservations');
    }
};
