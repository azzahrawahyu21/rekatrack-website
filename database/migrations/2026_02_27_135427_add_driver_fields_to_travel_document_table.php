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
            $table->string('driver_name')->nullable()->after('driver_id');
            $table->string('vehicle_number')->nullable()->after('driver_name');
        });
    }

    public function down()
    {
        Schema::table('travel_document', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'vehicle_number']);
        });
    }
};
