<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\TravelDocument;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;   
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitPcs = Unit::where('id', 'pcs')->first();

        $travelDocument1 = TravelDocument::where('no_travel_document', '0001/001/REKA/I/2025')->first();
        $travelDocument2 = TravelDocument::where('no_travel_document', '0002/002/REKA/I/2025')->first();
        $travelDocument3 = TravelDocument::where('no_travel_document', '0003/003/REKA/I/2025')->first();
        $travelDocument4 = TravelDocument::where('no_travel_document', '0004/004/REKA/I/2025')->first();
        $travelDocument5 = TravelDocument::where('no_travel_document', '0005/005/REKA/I/2025')->first();
        $travelDocument6 = TravelDocument::where('no_travel_document', '0006/006/REKA/I/2025')->first();
        $travelDocument7 = TravelDocument::where('no_travel_document', '0007/007/REKA/I/2025')->first();
        $travelDocument8 = TravelDocument::where('no_travel_document', '0008/008/REKA/I/2025')->first();
        $travelDocument9 = TravelDocument::where('no_travel_document', '0009/009/REKA/I/2025')->first();
        $travelDocument10 = TravelDocument::where('no_travel_document', '0010/010/REKA/I/2025')->first();

        // Travel Document 1
        Items::create([
            'travel_document_id' => $travelDocument1->id,
            'item_code' => 'ITEM001_1',
            'item_name' => 'Slot Wago 715-1405',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Description for Slot Wago 715-1405',
            'information' => 'Pengganti Rusak Solo K3 024 68'
        ]);
    
        Items::create([
            'travel_document_id' => $travelDocument1->id,
            'item_code' => 'ITEM001_2',
            'item_name' => 'Slot Wago 750-1405',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Description for Slot Wago 750-1405',
            'information' => 'Pengganti Rusak Solo K3 024'
        ]);
    
        Items::create([
            'travel_document_id' => $travelDocument1->id,
            'item_code' => 'ITEM001_3',
            'item_name' => 'Slot Wago 750-1504',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Description for Slot Wago 750-1504',
            'information' => ''
        ]);
        
        // Travel Document 2
        Items::create([
            'travel_document_id' => $travelDocument2->id,
            'item_code' => 'ITEM002_1',
            'item_name' => 'Monitor, Touchscreen 10inch (HDMI Input)',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 1,
            'unit_id' => $unitPcs,
            'description' => 'Description for Monitor, Touchscreen 10inch (HDMI Input)',
            'information' => 'Lubang tidak terdapat drat berdasarkan BA 04/REKA/BA/PPC/XII/2024'
        ]);
    
        Items::create([
            'travel_document_id' => $travelDocument2->id,
            'item_code' => 'ITEM002_2',
            'item_name' => 'Keyboard Logitech K380',
            'qty_send' => 2,
            'total_send' => 2,
            'qty_po' => 1,
            'unit_id' => $unitPcs,
            'description' => 'Wireless Keyboard',
            'information' => 'Model terbaru'
        ]);
    
        Items::create([
            'travel_document_id' => $travelDocument2->id,
            'item_code' => 'ITEM002_3',
            'item_name' => 'Mouse Logitech M330',
            'qty_send' => 3,
            'total_send' => 3,
            'qty_po' => 1,
            'unit_id' => $unitPcs,
            'description' => 'Wireless Mouse',
            'information' => 'Desain ergonomis'
        ]);
        
        // Travel Document 3
        Items::create([
            'travel_document_id' => $travelDocument3->id,
            'item_code' => 'ITEM003_1',
            'item_name' => 'CCTV Camera Model A100',
            'qty_send' => 2,
            'total_send' => 2,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'HD CCTV Camera',
            'information' => 'Untuk instalasi proyek'
        ]);
        
        // Travel Document 4
        Items::create([
            'travel_document_id' => $travelDocument4->id,
            'item_code' => 'ITEM004_1',
            'item_name' => 'Modem 4G Huawei B311',
            'qty_send' => 5,
            'total_send' => 5,
            'qty_po' => 2,
            'unit_id' => $unitPcs,
            'description' => 'Modem 4G LTE',
            'information' => 'Speed 150 Mbps'
        ]);
    
        Items::create([
            'travel_document_id' => $travelDocument4->id,
            'item_code' => 'ITEM004_2',
            'item_name' => 'Router TP-Link Archer AX50',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 1,
            'unit_id' => $unitPcs,
            'description' => 'Wi-Fi 6 Router',
            'information' => 'Desain minimalis'
        ]);
    
        // Travel Document 5
        Items::create([
            'travel_document_id' => $travelDocument5->id,
            'item_code' => 'ITEM005_1',
            'item_name' => 'Laptop Dell XPS 13',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Laptop ultrabook',
            'information' => 'Core i7, 16GB RAM'
        ]);
    
        Items::create([
            'travel_document_id' => $travelDocument5->id,
            'item_code' => 'ITEM005_2',
            'item_name' => 'Mouse Logitech MX Master 3',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Wireless Mouse',
            'information' => 'Ergonomis untuk produktivitas tinggi'
        ]);

        Items::create([
            'travel_document_id' => $travelDocument5->id,
            'item_code' => 'ITEM005_3',
            'item_name' => 'Docking Station Lenovo ThinkPad',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 1,
            'unit_id' => $unitPcs,
            'description' => 'Docking Station untuk laptop',
            'information' => 'Mendukung multiple display'
        ]);

        Items::create([
            'travel_document_id' => $travelDocument5->id,
            'item_code' => 'ITEM005_4',
            'item_name' => 'Headphone Sony WH-1000XM4',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Noise-canceling Headphone',
            'information' => 'Baterai tahan lama'
        ]);

        // Travel Document 6
        Items::create([
            'travel_document_id' => $travelDocument6->id,
            'item_code' => 'ITEM006_1',
            'item_name' => 'Projector Epson EB-X41',
            'qty_send' => 3,
            'total_send' => 3,
            'qty_po' => 2,
            'unit_id' => $unitPcs,
            'description' => 'Portable Projector',
            'information' => 'Resolution 1024x768'
        ]);
    
        Items::create([
            'travel_document_id' => $travelDocument6->id,
            'item_code' => 'ITEM006_2',
            'item_name' => 'Screen Projection 80 inch',
            'qty_send' => 2,
            'total_send' => 2,
            'qty_po' => 2,
            'unit_id' => $unitPcs,
            'description' => 'Screen Projection untuk presentasi',
            'information' => 'Memudahkan presentasi'
        ]);

        Items::create([
            'travel_document_id' => $travelDocument6->id,
            'item_code' => 'ITEM006_3',
            'item_name' => 'Kabel HDMI 10 meter',
            'qty_send' => 5,
            'total_send' => 5,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Kabel HDMI 10 meter',
            'information' => 'Untuk koneksi projector'
        ]);
    
        // Travel Document 7
        Items::create([
            'travel_document_id' => $travelDocument7->id,
            'item_code' => 'ITEM007_1',
            'item_name' => 'Portable Speaker JBL GO 3',
            'qty_send' => 2,
            'total_send' => 2,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Bluetooth speaker',
            'information' => 'Waterproof'
        ]);
    
        // Travel Document 8
        Items::create([
            'travel_document_id' => $travelDocument8->id,
            'item_code' => 'ITEM008_1',
            'item_name' => 'Headphones Sony WH-1000XM4',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 1,
            'unit_id' => $unitPcs,
            'description' => 'Noise-canceling headphones',
            'information' => 'Baterai tahan lama'
        ]);
    
        // Travel Document 9
        Items::create([
            'travel_document_id' => $travelDocument9->id,
            'item_code' => 'ITEM009_1',
            'item_name' => 'Smartphone Samsung Galaxy S21',
            'qty_send' => 4,
            'total_send' => 4,
            'qty_po' => 0,
            'unit_id' => $unitPcs,
            'description' => 'Smartphone Android',
            'information' => '6.2 inch display'
        ]);
    
        // Travel Document 10
        Items::create([
            'travel_document_id' => $travelDocument10->id,
            'item_code' => 'ITEM010_1',
            'item_name' => 'Printer Canon Pixma G2010',
            'qty_send' => 1,
            'total_send' => 1,
            'qty_po' => 1,
            'unit_id' => $unitPcs,
            'description' => 'Inkjet Printer',
            'information' => 'Termasuk scanner'
        ]);
    }
}
