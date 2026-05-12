<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showtimes', function (Blueprint $table) {
            $table->increments('show_id');
            $table->string('movie_title', 100);
            $table->string('genre', 50);
            $table->unsignedInteger('hall_number');
            $table->date('show_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('available_seats');
            $table->decimal('ticket_price', 6, 2);
            $table->string('movie_status', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
