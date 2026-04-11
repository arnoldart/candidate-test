<?php

namespace Tests\Unit;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\SupplierPolicy;
use App\Policies\CltLayupPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_supplier_policy_allows_view_any()
    {
        $policy = new SupplierPolicy();
        $this->assertTrue($policy->viewAny($this->user));
    }

    public function test_supplier_policy_allows_create()
    {
        $policy = new SupplierPolicy();
        $this->assertTrue($policy->create($this->user));
    }

    public function test_supplier_policy_allows_update()
    {
        $supplier = Supplier::create(['name' => 'Test']);
        $policy = new SupplierPolicy();
        $this->assertTrue($policy->update($this->user, $supplier));
    }

    public function test_supplier_policy_allows_delete()
    {
        $supplier = Supplier::create(['name' => 'Test']);
        $policy = new SupplierPolicy();
        $this->assertTrue($policy->delete($this->user, $supplier));
    }

    public function test_supplier_policy_allows_import()
    {
        $supplier = Supplier::create(['name' => 'Test']);
        $policy = new SupplierPolicy();
        $this->assertTrue($policy->import($this->user, $supplier));
    }

    public function test_supplier_policy_allows_export()
    {
        $supplier = Supplier::create(['name' => 'Test']);
        $policy = new SupplierPolicy();
        $this->assertTrue($policy->export($this->user, $supplier));
    }

    public function test_layup_policy_allows_view_any()
    {
        $policy = new CltLayupPolicy();
        $this->assertTrue($policy->viewAny($this->user));
    }

    public function test_layup_policy_allows_create()
    {
        $policy = new CltLayupPolicy();
        $this->assertTrue($policy->create($this->user));
    }

    public function test_layup_policy_allows_update()
    {
        $supplier = Supplier::create(['name' => 'Test']);
        $layup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'L1']);
        $policy = new CltLayupPolicy();
        $this->assertTrue($policy->update($this->user, $layup));
    }

    public function test_layup_policy_allows_delete()
    {
        $supplier = Supplier::create(['name' => 'Test']);
        $layup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'L1']);
        $policy = new CltLayupPolicy();
        $this->assertTrue($policy->delete($this->user, $layup));
    }

    public function test_layup_policy_allows_duplicate()
    {
        $supplier = Supplier::create(['name' => 'Test']);
        $layup = CltLayup::create(['supplier_id' => $supplier->id, 'name' => 'L1']);
        $policy = new CltLayupPolicy();
        $this->assertTrue($policy->duplicate($this->user, $layup));
    }
}
