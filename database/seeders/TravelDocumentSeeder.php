<?php

namespace Database\Seeders;

use App\Models\TravelDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravelDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
   
        TravelDocument::create([
            'no_travel_document' => '0001/001/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'Purna Jual PT INKA (Persero)',
            'po_number' => '4500000011223',
            'reference_number' => '0001/001/REKA/GEN/PPC/I/2025',
            'project' => '612 Card replacement PT KAI',
            'status' => 'terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0002/002/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'PT DUTA ATAYA ANUGERAH',
            'po_number' => '4500000011223',
            'reference_number' => '3615/205/REKA/GEN/PPC/XII/2024',
            'project' => '612',
            'status' => 'terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0003/003/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'PT MITRA BERKAH MAPAN',
            'po_number' => '4500000011223',
            'reference_number' => '3613/203/REKA/GEN/PPC/XII/2024',
            'project' => '612',
            'status' => 'terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0004/004/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'CV. ROHA ELEKTRIK BERSAUDARA',
            'po_number' => '4500000011223',
            'reference_number' => '0003/003/REKA/GEN/PPC/I/2025',
            'project' => '612',
            'status' => 'terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0005/005/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'Polytron Service Center',
            'po_number' => '4500000011223',
            'reference_number' => '0004/005/REKA/GEN/PPC/I/2025',
            'project' => '612',
            'status' => 'terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0006/006/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'CV. GRAFIKA ABADI',
            'po_number' => '4500000011224',
            'reference_number' => '0005/005/REKA/GEN/PPC/I/2025',
            'project' => '613',
            'status' => 'terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0007/007/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'PT. KARYA SEJAHTERA',
            'po_number' => '4500000011225',
            'reference_number' => '0006/006/REKA/GEN/PPC/I/2025',
            'project' => '614',
            'status' => 'terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0008/008/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'PT. SINDO TEKNOLOGI',
            'po_number' => '4500000011226',
            'reference_number' => '0007/007/REKA/GEN/PPC/I/2025',
            'project' => '615',
            'status' => 'belum terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0009/009/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'PT. Sumber Daya Teknik',
            'po_number' => '4500000011227',
            'reference_number' => '0008/0008/REKA/GEN/PPC/I/2025',
            'project' => '616',
            'status' => 'belum terkirim',
        ]);

        TravelDocument::create([
            'no_travel_document' => '0010/010/REKA/I/2025',
            'date_no_travel_document' => now(),
            'send_to' => 'PT. MANFAAT BERSAMA',
            'po_number' => '4500000011228',
            'reference_number' => '0009/009/REKA/GEN/PPC/I/2025',
            'project' => '617',
            'status' => 'belum terkirim',
        ]);

   
    }
}
