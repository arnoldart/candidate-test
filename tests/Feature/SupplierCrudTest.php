<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_suppliers()
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_suppliers()
    {
        $response = $this->actingAs($this->user)->get(route('suppliers.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_supplier()
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => 'New Supplier',
            'primary_contact' => 'test@example.com',
            'location' => 'Jakarta',
            'material_certifications' => 'ISO 9001',
            'last_audit_date' => '2026-01-15',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('supplier', ['name' => 'New Supplier']);
    }

    public function test_create_supplier_requires_name()
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => '',
            'primary_contact' => 'test@example.com',
            'location' => 'Jakarta',
            'material_certifications' => 'ISO 9001',
            'last_audit_date' => '2026-01-15',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_can_update_supplier()
    {
        $supplier = Supplier::create([
            'name' => 'Old Name',
            'primary_contact' => 'old@test.com',
            'location' => 'Bandung',
            'material_certifications' => 'CE',
            'last_audit_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($this->user)->put(route('suppliers.update', $supplier), [
            'name' => 'Updated Name',
            'primary_contact' => 'new@test.com',
            'location' => 'Surabaya',
            'material_certifications' => 'FSC',
            'last_audit_date' => '2026-06-01',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('supplier', ['name' => 'Updated Name', 'location' => 'Surabaya']);
    }

    public function test_user_can_delete_supplier()
    {
        $supplier = Supplier::create([
            'name' => 'To Delete',
            'primary_contact' => 'del@test.com',
            'location' => 'Medan',
            'material_certifications' => 'PEFC',
            'last_audit_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($this->user)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('supplier', ['id' => $supplier->id]);
    }

    public function test_supplier_search_filters_results()
    {
        Supplier::create(['name' => 'Nordic Structures']);
        Supplier::create(['name' => 'Southern Pine']);

        $response = $this->actingAs($this->user)->get(route('suppliers.index', ['search' => 'Nordic']));

        $response->assertStatus(200);
        $response->assertSee('Nordic Structures');
        $response->assertDontSee('Southern Pine');
    }
}
