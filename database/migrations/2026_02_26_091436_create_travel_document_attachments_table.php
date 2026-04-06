<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_document_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_document_id');
            $table->string('name', 255);
            $table->timestamps();

            $table->foreign('travel_document_id')
                ->references('id')
                ->on('travel_document')
                ->cascadeOnDelete(); // kalau surat jalan dihapus, lampiran ikut kehapus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_document_attachments');
    }
};
