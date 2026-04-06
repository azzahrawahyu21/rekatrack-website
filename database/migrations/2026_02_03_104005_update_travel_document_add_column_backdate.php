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
            //
            $table->boolean('is_backdate')->default(false)->after('document_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_document', function (Blueprint $table) {
            //
            $table->dropColumn(['document_date', 'is_backdate']);
        });
    }
};
