<?php

namespace Tests\Unit;

use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SupplierService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SupplierService();
    }

    public function test_export_returns_json_with_layups_and_layers()
    {
        $supplier = Supplier::create(['name' => 'Export Supplier']);
        $layup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'Test Layup']);
        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 30, 'width' => 1200, 'angle' => 0]);

        $json = $this->service->export($supplier);
        $data = json_decode($json, true);

        $this->assertEquals('Export Supplier', $data['name']);
        $this->assertCount(1, $data['clt_layups']);
        $this->assertCount(1, $data['clt_layups'][0]['clt_layers']);
    }

    public function test_export_all_returns_all_suppliers()
    {
        Supplier::create(['name' => 'Supplier A']);
        Supplier::create(['name' => 'Supplier B']);

        $json = $this->service->exportAll();
        $data = json_decode($json, true);

        $this->assertCount(2, $data);
    }

    public function test_import_creates_new_layup()
    {
        $supplier = Supplier::create(['name' => 'Import Supplier']);

        $data = [
            'clt_layups' => [
                [
                    'name' => 'New Layup',
                    'clt_layers' => [
                        ['layer_order' => 1, 'thickness' => 25, 'width' => 1000, 'angle' => 0],
                    ]
                ]
            ]
        ];

        $result = $this->service->import($supplier, $data, 'skip', [], false);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['stats']['created']);
        $this->assertDatabaseHas('clt_layups', ['supplier_id' => $supplier->id, 'name' => 'New Layup']);
    }

    public function test_import_skips_identical_data()
    {
        $supplier = Supplier::create(['name' => 'Import Supplier']);
        $layup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'Existing']);
        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 30, 'width' => 1200, 'angle' => 0]);

        $data = [
            'clt_layups' => [
                [
                    'name' => 'Existing',
                    'clt_layers' => [
                        ['layer_order' => 1, 'thickness' => 30, 'width' => 1200, 'angle' => 0],
                    ]
                ]
            ]
        ];

        $result = $this->service->import($supplier, $data, 'skip', [], false);

        $this->assertEquals(1, $result['stats']['skipped']);
        $this->assertEquals(0, $result['stats']['created']);
    }

    public function test_import_dry_run_does_not_persist()
    {
        $supplier = Supplier::create(['name' => 'Dry Run Supplier']);

        $data = [
            'clt_layups' => [
                [
                    'name' => 'Phantom Layup',
                    'clt_layers' => [
                        ['layer_order' => 1, 'thickness' => 99, 'width' => 500, 'angle' => 45],
                    ]
                ]
            ]
        ];

        $this->service->import($supplier, $data, 'skip', [], true);

        $this->assertDatabaseMissing('clt_layups', ['name' => 'Phantom Layup']);
    }

    public function test_import_detects_conflicts_on_dry_run()
    {
        $supplier = Supplier::create(['name' => 'Conflict Supplier']);
        $layup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'Conflicting']);
        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 30, 'width' => 1200, 'angle' => 0]);

        $data = [
            'clt_layups' => [
                [
                    'name' => 'Conflicting',
                    'clt_layers' => [
                        ['layer_order' => 1, 'thickness' => 99, 'width' => 1200, 'angle' => 0],
                    ]
                ]
            ]
        ];

        $result = $this->service->import($supplier, $data, 'skip', [], true);

        $this->assertEquals(1, $result['stats']['conflicts_detected']);
        $this->assertCount(1, $result['conflicts']);
        $this->assertEquals('Conflicting', $result['conflicts'][0]['layup_name']);
    }

    public function test_import_overwrite_replaces_layers()
    {
        $supplier = Supplier::create(['name' => 'Overwrite Supplier']);
        $layup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'Overwritable']);
        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 10, 'width' => 500, 'angle' => 0]);

        $data = [
            'clt_layups' => [
                [
                    'name' => 'Overwritable',
                    'clt_layers' => [
                        ['layer_order' => 1, 'thickness' => 99, 'width' => 1500, 'angle' => 45],
                    ]
                ]
            ]
        ];

        $result = $this->service->import($supplier, $data, 'overwrite', [], false);

        $this->assertEquals(1, $result['stats']['updated']);
        $this->assertDatabaseHas('clt_layers', ['layup_id' => $layup->id, 'thickness' => 99]);
        $this->assertDatabaseMissing('clt_layers', ['layup_id' => $layup->id, 'thickness' => 10]);
    }
}
