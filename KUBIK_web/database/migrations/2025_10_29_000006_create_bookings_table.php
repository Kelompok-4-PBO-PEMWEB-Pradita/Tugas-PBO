<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id_booking');
            $table->integer('id_user')->unsigned();
            $table->integer('id_admin')->unsigned()->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->dateTime('return_at')->nullable();
            $table->integer('late_return')->default(0);
            $table->enum('status', ['Pending','Approved','Rejected','Completed'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_user');
            $table->index('id_admin');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_admin')->references('id_admin')->on('admins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
