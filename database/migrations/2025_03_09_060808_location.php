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
        Schema::create('location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained('track'); // Foreign key ke track
            $table->decimal('latitude', 10, 8); // Menyimpan koordinat latitude
            $table->decimal('longitude', 11, 8); // Menyimpan koordinat longitude
            $table->timestamp('time_stamp');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location');
    }
};
