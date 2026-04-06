<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_items', function (Blueprint $table) {
            $table->id();

            // Relasi ke items
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete(); // kalau item dihapus (hard delete), sub item ikut kehapus

            // Atribut sama seperti items (tanpa travel_document_id)
            $table->string('item_code', 100);
            $table->string('item_name', 255);

            $table->unsignedInteger('qty_send')->default(0);
            $table->unsignedInteger('total_send')->default(0);

            // qty_po di items kamu bisa int/string/'-' -> paling aman simpan string (nullable)
            $table->string('qty_po', 50)->nullable();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete(); // optional: cegah delete unit yang masih dipakai

            $table->string('description', 1000)->default('-');
            $table->text('information')->nullable();

            $table->timestamps();

            // Optional, kalau mau support soft delete untuk sub item
            // $table->softDeletes();

            // Index yang berguna
            $table->index(['item_id']);
            $table->index(['item_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_items');
    }
};
