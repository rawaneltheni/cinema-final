<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('showtime_id');
            $table->string('customer_name', 100);
            $table->string('customer_email', 150);
            $table->string('chair_type', 20);
            $table->unsignedInteger('chair_count');
            $table->string('seat_numbers', 255)->nullable();
            $table->string('snacks')->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('payment_status', 20)->default('unpaid');
            $table->decimal('payment_amount', 8, 2);
            $table->string('payment_method', 40)->nullable();
            $table->timestamps();

            $table->foreign('showtime_id')
                ->references('show_id')
                ->on('showtimes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
