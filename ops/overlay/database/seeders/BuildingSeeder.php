<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Gedung Kolaboratif',
            'Gerbang Utama',
            'AWI',
            'Shelter Maintenance Area 1',
            'Shelter Maintenance Area 2',
            'Shelter Maintenance Area 3',
            'Shelter Maintenance Area 4',
            'Shelter White OM',
            'Pintu Masuk Area Kilang',
            'Marine Region III',
            'Main Control Room',
            'Tank Farm Area 1',
            'Gedung EXOR',
            'Produksi CDU',
            'HSSE Demo Room',
            'Gedung Amanah',
            'POC',
            'JGC',
        ];

        foreach ($names as $name) {
            DB::table('buildings')->updateOrInsert(
                ['name' => $name],
                ['lat' => null, 'lng' => null, 'icon' => null, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}

