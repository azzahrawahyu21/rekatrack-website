<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration ini mengubah kolom qty_po dari integer menjadi string
     * agar bisa menyimpan symbol seperti '-' (dash/minus)
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Ubah kolom qty_po dari integer menjadi string
            $table->string('qty_po', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Kembalikan ke integer (hati-hati, data non-numeric akan hilang!)
            $table->integer('qty_po')->nullable()->change();
        });
    }
};
