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
        Schema::create('travel_document', function (Blueprint $table) {
            $table->id();
            $table->string('no_travel_document');
            $table->date('date_no_travel_document');
            $table->string('send_to');
            $table->string('po_number');
            $table->string('reference_number');
            $table->string('project');
            $table->enum('status', ['Terkirim','Sedang dikirim', 'Belum terkirim']);
            $table->timestamps();
            $table->softDeletes();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_document');
    }
};
