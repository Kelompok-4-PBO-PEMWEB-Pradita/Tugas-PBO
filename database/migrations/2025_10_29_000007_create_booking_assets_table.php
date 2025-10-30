<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_assets', function (Blueprint $table) {
            $table->integer('id_booking')->unsigned();
            $table->integer('id_asset')->unsigned();
            $table->timestamp('created_at')->useCurrent();

            $table->index('id_booking');
            $table->index('id_asset');

            $table->foreign('id_booking')->references('id_booking')->on('bookings')->onDelete('cascade');
            $table->foreign('id_asset')->references('id_asset')->on('assets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_assets');
    }
};
