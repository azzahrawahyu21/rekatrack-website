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
        Schema::create('tracking_system', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained('track'); // Foreign key ke track
            $table->foreignId('travel_document_id')->constrained('travel_document'); // Foreign key ke travel_document
            $table->timestamp('time_stamp');
            $table->enum('status', ['active', 'non-active']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_system');
    }
};
