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
        Schema::create('delivery_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_document_id');  // <-- ini penting! pakai unsignedBigInteger
            $table->string('receiver_name', 255);
            $table->dateTime('received_at');
            $table->text('note')->nullable();
            $table->string('photo_path', 255)->nullable();

            $table->timestamps();

            $table->foreign('travel_document_id')
                ->references('id')
                ->on('travel_document')  // sesuaikan nama tabel asli
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_confirmations');
    }
};
