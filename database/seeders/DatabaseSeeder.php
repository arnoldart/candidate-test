<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('123123123'),
            ]
        );

        
        User::factory(20)->create();

        if (Supplier::count() === 0) {
            $suppliers = Supplier::factory(20)->create();

            foreach ($suppliers as $supplier) {
                $layups = CltLayup::factory(rand(1, 3))->create([
                    'supplier_id' => $supplier->id,
                ]);

                foreach ($layups as $layup) {
                    $plyCountPattern = '/(\d+)-Ply/';
                    preg_match($plyCountPattern, $layup->name, $matches);
                    $plyCount = isset($matches[1]) ? (int)$matches[1] : 3;

                    for ($i = 1; $i <= $plyCount; $i++) {
                        $angle = ($i % 2 === 0) ? 90.0 : 0.0;
                        
                        $width = ($angle === 90.0) ? 100.0 : 1500.0;

                        CltLayer::factory()->create([
                            'layup_id' => $layup->id,
                            'layer_order' => $i,
                            'thickness' => fake()->randomElement([20.0, 30.0, 40.0]),
                            'width' => $width,
                            'angle' => $angle,
                        ]);
                    }
                }
            }
        }
    }
}
