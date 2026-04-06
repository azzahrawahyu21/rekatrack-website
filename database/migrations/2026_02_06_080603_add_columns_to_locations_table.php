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
        Schema::table('location', function (Blueprint $table) {
            $table->boolean('is_checkpoint')->default(false)->after('longitude');
            $table->decimal('distance_from_last', 8, 2)->default(0)->after('is_checkpoint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location', function (Blueprint $table) {
            $table->dropColumn(['is_checkpoint', 'distance_from_last']);
        });
    }
};
