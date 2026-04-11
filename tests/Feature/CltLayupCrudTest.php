<?php

namespace Tests\Feature;

use App\Models\CltLayup;
use App\Models\CltLayer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::create(['name' => 'Test Supplier']);
    }

    public function test_guest_cannot_access_layups()
    {
        $response = $this->get(route('suppliers.layups.index', $this->supplier));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_layups()
    {
        $response = $this->actingAs($this->user)->get(route('suppliers.layups.index', $this->supplier));
        $response->assertStatus(200);
    }

    public function test_user_can_create_layup()
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.layups.store', $this->supplier), [
            'name' => 'New Layup',
            'species_grade' => 'Spruce / C24',
            'status' => 'Draft',
            'created_by' => 'Eng. Dept',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $this->supplier->id,
            'name' => 'New Layup',
            'species_grade' => 'Spruce / C24',
        ]);
    }

    public function test_create_layup_requires_name()
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.layups.store', $this->supplier), [
            'name' => '',
            'species_grade' => 'Spruce',
            'status' => 'Draft',
            'created_by' => 'Test',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_create_layup_requires_species_grade()
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.layups.store', $this->supplier), [
            'name' => 'Test Layup',
            'species_grade' => '',
            'status' => 'Draft',
            'created_by' => 'Test',
        ]);

        $response->assertSessionHasErrors('species_grade');
    }

    public function test_user_can_update_layup()
    {
        $layup = CltLayup::create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Old Layup',
            'species_grade' => 'Pine / C16',
            'status' => 'Draft',
            'created_by' => 'Old User',
        ]);

        $response = $this->actingAs($this->user)->put(
            route('suppliers.layups.update', ['supplier' => $this->supplier->id, 'layup' => $layup->id]),
            [
                'name' => 'Updated Layup',
                'species_grade' => 'Spruce / C24',
                'status' => 'Active',
                'created_by' => 'New User',
            ]
        );

        $response->assertRedirect(route('suppliers.layups.index', $this->supplier));
        $this->assertDatabaseHas('clt_layups', ['name' => 'Updated Layup', 'status' => 'Active']);
    }

    public function test_update_layup_increments_revision()
    {
        $layup = CltLayup::create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Revision Test',
            'species_grade' => 'Spruce',
            'status' => 'Draft',
            'created_by' => 'Tester',
        ]);

        $this->assertEquals(1, $layup->fresh()->revision);

        $this->actingAs($this->user)->put(
            route('suppliers.layups.update', ['supplier' => $this->supplier->id, 'layup' => $layup->id]),
            [
                'name' => 'Revision Test Updated',
                'species_grade' => 'Spruce',
                'status' => 'Active',
                'created_by' => 'Tester',
            ]
        );

        $this->assertEquals(2, $layup->fresh()->revision);
    }

    public function test_user_can_delete_layup()
    {
        $layup = CltLayup::create([
            'supplier_id' => $this->supplier->id,
            'name' => 'To Delete',
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.destroy', ['supplier' => $this->supplier->id, 'layup' => $layup->id])
        );

        $response->assertRedirect(route('suppliers.layups.index', $this->supplier));
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }

    public function test_user_can_duplicate_layup()
    {
        $layup = CltLayup::create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Original',
            'species_grade' => 'Spruce / Mixed',
            'status' => 'Active',
            'created_by' => 'Eng. A',
        ]);

        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 1, 'thickness' => 30, 'width' => 1200, 'angle' => 0]);
        CltLayer::create(['layup_id' => $layup->id, 'layer_order' => 2, 'thickness' => 20, 'width' => 1200, 'angle' => 90]);

        $response = $this->actingAs($this->user)->post(route('layups.duplicate', $layup));

        $response->assertStatus(302);
        $this->assertDatabaseHas('clt_layups', ['name' => 'Original (Copy)']);

        $copy = CltLayup::where('name', 'Original (Copy)')->first();
        $this->assertCount(2, $copy->cltLayers);
    }

    public function test_layup_search_filters_results()
    {
        CltLayup::create(['supplier_id' => $this->supplier->id, 'name' => 'Standard 3-Ply']);
        CltLayup::create(['supplier_id' => $this->supplier->id, 'name' => 'Custom 5-Ply']);

        $response = $this->actingAs($this->user)->get(
            route('suppliers.layups.index', ['supplier' => $this->supplier->id, 'search' => 'Standard'])
        );

        $response->assertStatus(200);
        $response->assertSee('Standard 3-Ply');
        $response->assertDontSee('Custom 5-Ply');
    }
}
