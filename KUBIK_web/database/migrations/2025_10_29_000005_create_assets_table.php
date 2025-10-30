<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id_asset');
            $table->string('id_master', 10);
            $table->enum('asset_condition', ['Good','Damaged','Lost'])->default('Good');
            $table->enum('status', ['Available','Borrowed','Maintenance'])->default('Available');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('id_master');
            $table->foreign('id_master')->references('id_master')->on('asset_masters')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
