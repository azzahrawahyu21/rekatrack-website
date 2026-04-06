<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id')->nullable()->after('qty_po');

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');

            $table->dropColumn('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('items', function (Blueprint $table) {
            $table->string('unit')->after('qty_po');
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });

    }
};
