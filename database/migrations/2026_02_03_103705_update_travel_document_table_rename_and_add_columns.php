<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('travel_document', function (Blueprint $table) {
            // Rename kolom
            $table->renameColumn('date_no_travel_document', 'posting_date');

            // Tambah kolom baru
            $table->date('document_date')->nullable()->after('posting_date');
        });
    }

    public function down()
    {
        Schema::table('travel_document', function (Blueprint $table) {
            // Kembalikan nama kolom
            $table->renameColumn('posting_date', 'date_no_travel_document');

            // Hapus kolom baru
            $table->dropColumn('document_date');
        });
    }
};
