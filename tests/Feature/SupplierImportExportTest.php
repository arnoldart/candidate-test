<?php

namespace Tests\Feature;

use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupplierImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_supplier_export_returns_valid_json()
    {
        $supplier = Supplier::create(['name' => 'Nordic Structures Test']);
        
        $layup = CltLayup::create([
            'supplier_id' => $supplier->id,
            'name' => 'L-Test-1',
        ]);

        CltLayer::create([
            'layup_id' => $layup->id,
            'thickness' => 35,
            'width' => 1200,
            'angle' => 0,
            'layer_order' => 0,
        ]);

        $response = $this->actingAs($this->user)->get(route('suppliers.export', $supplier));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        
        $response->assertJsonFragment(['name' => 'Nordic Structures Test']);
        $response->assertJsonFragment(['thickness' => "35.00", 'angle' => "0.00"]);
        
        $json = $response->json();
        $this->assertArrayHasKey('clt_layups', $json);
        $this->assertCount(1, $json['clt_layups']);
        $this->assertArrayHasKey('clt_layers', $json['clt_layups'][0]);
    }

    public function test_import_creates_new_layups_and_layers()
    {
        $supplier = Supplier::create(['name' => 'Nordic Structures Test']);

        $importData = [
            'clt_layups' => [
                [
                    'name' => 'Imported-Layup-1',
                    'clt_layers' => [
                        ['thickness' => 45, 'width' => 1200, 'angle' => 0, 'layer_order' => 0],
                        ['thickness' => 35, 'width' => 1200, 'angle' => 90, 'layer_order' => 1]
                    ]
                ]
            ]
        ];

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $response = $this->actingAs($this->user)->postJson(route('suppliers.import', $supplier), [
            'file' => $file,
            'strategy' => 'skip',
            'dry_run' => false
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Imported-Layup-1'
        ]);

        $this->assertDatabaseHas('clt_layers', [
            'thickness' => 45,
            'angle' => 0
        ]);
        $this->assertDatabaseHas('clt_layers', [
            'thickness' => 35,
            'angle' => 90
        ]);
    }

    public function test_import_skips_conflicts_by_default()
    {
        $supplier = Supplier::create(['name' => 'Nordic Structures Test']);

        $existingLayup = CltLayup::create([
            'supplier_id' => $supplier->id,
            'name' => 'Existing-Layup',
        ]);
        
        CltLayer::create([
            'layup_id' => $existingLayup->id,
            'thickness' => 40,
            'width' => 1200,
            'angle' => 0,
            'layer_order' => 0,
        ]);

        $importData = [
            'clt_layups' => [
                [
                    'name' => 'Existing-Layup',
                    'clt_layers' => [
                        ['thickness' => 100, 'width' => 1500, 'angle' => 90, 'layer_order' => 0]
                    ]
                ],
                [
                    'name' => 'New-Layup',
                    'clt_layers' => [
                        ['thickness' => 20, 'width' => 1000, 'angle' => 0, 'layer_order' => 0]
                    ]
                ]
            ]
        ];

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $response = $this->actingAs($this->user)->postJson(route('suppliers.import', $supplier), [
            'file' => $file,
            'strategy' => 'skip',
            'dry_run' => false
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('clt_layers', [
            'thickness' => 100,
            'angle' => 90
        ]);
        $this->assertDatabaseHas('clt_layers', [
            'thickness' => 40,
            'angle' => 0
        ]);

        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $supplier->id,
            'name' => 'New-Layup'
        ]);
    }

    public function test_import_dry_run_prevents_database_changes()
    {
        $supplier = Supplier::create(['name' => 'Nordic Structures Test']);

        $importData = [
            'clt_layups' => [
                [
                    'name' => 'Dry-Run-Layup',
                    'clt_layers' => [
                        ['thickness' => 99, 'width' => 1200, 'angle' => 0, 'layer_order' => 0]
                    ]
                ]
            ]
        ];

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $response = $this->actingAs($this->user)->postJson(route('suppliers.import', $supplier), [
            'file' => $file,
            'strategy' => 'skip',
            'dry_run' => true
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'stats' => ['created', 'updated', 'skipped']]);

        $this->assertDatabaseMissing('clt_layups', [
            'name' => 'Dry-Run-Layup'
        ]);
        $this->assertDatabaseMissing('clt_layers', [
            'thickness' => 99
        ]);
    }

    public function test_import_returns_detailed_conflicts_on_dry_run()
    {
        $supplier = Supplier::create(['name' => 'Nordic Structures Test']);

        $existingLayup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'Conflict-Layup']);
        CltLayer::create(['layup_id' => $existingLayup->id, 'thickness' => 40, 'width' => 150, 'angle' => 0, 'layer_order' => 1]);

        $importData = [
            'clt_layups' => [
                [
                    'name' => 'Conflict-Layup',
                    'clt_layers' => [
                        ['thickness' => 35, 'width' => 150, 'angle' => 0, 'layer_order' => 1]
                    ]
                ]
            ]
        ];

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $response = $this->actingAs($this->user)->postJson(route('suppliers.import', $supplier), [
            'file' => $file,
            'dry_run' => true
        ]);

        $response->assertStatus(200);
        $json = $response->json();
        
        $this->assertArrayHasKey('conflicts', $json);
        $this->assertCount(1, $json['conflicts']);
        $this->assertEquals('Conflict-Layup', $json['conflicts'][0]['layup_name']);
        
        $this->assertArrayHasKey('existing_layers', $json['conflicts'][0]);
        $this->assertArrayHasKey('importing_layers', $json['conflicts'][0]);
    }

    public function test_import_with_resolution_map_applies_correct_strategies_per_layup()
    {
        $supplier = Supplier::create(['name' => 'Nordic Structures Test']);

        $layupA = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'Layup-A']);
        CltLayer::create(['layup_id' => $layupA->id, 'thickness' => 10, 'width' => 150, 'angle' => 0, 'layer_order' => 1]);

        $layupB = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'Layup-B']);
        CltLayer::create(['layup_id' => $layupB->id, 'thickness' => 20, 'width' => 150, 'angle' => 0, 'layer_order' => 1]);

        $importData = [
            'clt_layups' => [
                [
                    'name' => 'Layup-A',
                    'clt_layers' => [
                        ['thickness' => 999, 'width' => 150, 'angle' => 0, 'layer_order' => 1]
                    ]
                ],
                [
                    'name' => 'Layup-B',
                    'clt_layers' => [
                        ['thickness' => 888, 'width' => 150, 'angle' => 0, 'layer_order' => 1]
                    ]
                ]
            ]
        ];

        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $response = $this->actingAs($this->user)->postJson(route('suppliers.import', $supplier), [
            'file' => $file,
            'dry_run' => false,
            'resolution_map' => json_encode([
                'Layup-A' => 'skip',
                'Layup-B' => 'overwrite'
            ])
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layupA->fresh()->id, 'thickness' => 10]);
        $this->assertDatabaseMissing('clt_layers', ['layup_id' => $layupA->fresh()->id, 'thickness' => 999]);

        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layupB->fresh()->id, 'thickness' => 888]);
        $this->assertDatabaseMissing('clt_layers', ['layup_id' => $layupB->fresh()->id, 'thickness' => 20]);
    }
}
