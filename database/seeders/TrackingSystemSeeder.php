<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\TrackingSystem;
use App\Models\TravelDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrackingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = Track::all();
        $documents = TravelDocument::take(7)->get();

        $count = min($tracks->count(), $documents->count());

        for ($i = 0; $i < $count; $i++) {
            TrackingSystem::create([
                'track_id' => $tracks[$i]->id,
                'travel_document_id' => $documents[$i]->id,
                'time_stamp' => $tracks[$i]->time_stamp,
                'status' => 'active',
            ]);
        }
    }
}
