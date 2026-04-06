<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_document', function (Blueprint $table) {
            $table->date('reference_date')->nullable()->after('reference_number');
        });
    }

    public function down(): void
    {
        Schema::table('travel_document', function (Blueprint $table) {
            $table->dropColumn('reference_date');
        });
    }
};
