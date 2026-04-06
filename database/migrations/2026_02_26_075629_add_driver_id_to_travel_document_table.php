<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('travel_document', function (Blueprint $table) {
            // taruh setelah kolom yang masuk akal, misal send_to atau status
            $table->unsignedBigInteger('driver_id')->nullable()->after('send_to');

            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete(); // kalau user driver dihapus, travel_document tetap ada, driver_id jadi null
        });
    }

    public function down(): void
    {
        Schema::table('travel_document', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });
    }
};
