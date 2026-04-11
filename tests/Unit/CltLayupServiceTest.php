<?php

namespace Tests\Unit;

use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Models\Supplier;
use App\Services\CltLayupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CltLayupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CltLayupService();
    }

    public function test_duplicate_creates_copy_with_correct_name()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = CltLayup::create([
            'supplier_id' => $supplier->id,
            'name' => 'Original Layup',
            'species_grade' => 'Spruce / C24',
            'status' => 'Active',
            'created_by' => 'Eng. Team',
        ]);

        $duplicated = $this->service->duplicate($layup);

        $this->assertEquals('Original Layup (Copy)', $duplicated->name);
        $this->assertEquals($layup->supplier_id, $duplicated->supplier_id);
        $this->assertNotEquals($layup->id, $duplicated->id);
    }

    public function test_duplicate_copies_all_layers()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = CltLayup::create([
            'supplier_id' => $supplier->id,
            'name' => 'Layup With Layers',
        ]);

        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 30, 'width' => 1200, 'angle' => 0]);
        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 20, 'width' => 1200, 'angle' => 90]);
        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 3, 'thickness' => 30, 'width' => 1200, 'angle' => 0]);

        $duplicated = $this->service->duplicate($layup);

        $this->assertCount(3, $duplicated->cltLayers);
        $this->assertEquals(30, $duplicated->cltLayers->where('layer_order', 1)->first()->thickness);
        $this->assertEquals(90, $duplicated->cltLayers->where('layer_order', 2)->first()->angle);
    }

    public function test_duplicate_does_not_affect_original()
    {
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $layup = CltLayup::create([
            'supplier_id' => $supplier->id,
            'name' => 'Original',
        ]);

        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 50, 'width' => 1000, 'angle' => 0]);

        $this->service->duplicate($layup);

        $original = CltLayup::find($layup->id);
        $this->assertEquals('Original', $original->name);
        $this->assertCount(1, $original->cltLayers);
    }
}
