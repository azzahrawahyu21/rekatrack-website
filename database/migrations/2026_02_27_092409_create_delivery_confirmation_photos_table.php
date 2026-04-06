<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_confirmation_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_confirmation_id');
            $table->string('photo_path', 255);
            $table->timestamps();

            $table->foreign('delivery_confirmation_id')
                ->references('id')
                ->on('delivery_confirmations')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_confirmation_photos');
    }
};
