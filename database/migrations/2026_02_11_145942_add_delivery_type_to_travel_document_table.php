<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('travel_document', function (Blueprint $table) {
            $table->enum('delivery_type', ['Dalam Kota', 'Luar Kota'])
                ->default('Dalam Kota')
                ->after('project');
        });
    }

    public function down()
    {
        Schema::table('travel_document', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
        });
    }

};
