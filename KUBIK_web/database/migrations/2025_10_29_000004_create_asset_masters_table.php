<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_masters', function (Blueprint $table) {
            $table->string('id_master', 10)->primary();
            $table->string('id_category', 10);
            $table->string('id_type', 10);
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('stock_total')->default(0);
            $table->integer('stock_available')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('id_category');
            $table->index('id_type');

            $table->foreign('id_category')->references('id_category')->on('categories')->onDelete('cascade');
            $table->foreign('id_type')->references('id_type')->on('types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_masters');
    }
};
