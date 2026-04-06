<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // Ubah kolom menjadi nullable
            $table->integer('qty_send')->nullable()->change();
            $table->integer('qty_po')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('qty_send')->nullable(false)->change();
            $table->integer('qty_po')->nullable(false)->change();
        });
    }
};
