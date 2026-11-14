<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\SupplierStatus;
use App\Enums\UserRole;
use App\Enums\WarehouseType;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudAndWarehouseTitleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
    }

    public function test_dashboard_displays_correct_warehouse_titles(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Gudang Bahan Mentah');
        $response->assertSee('Gudang Produk Jadi');
    }

    public function test_product_crud_lifecycle(): void
    {
        // 1. Create Product
        $createResponse = $this->actingAs($this->admin)->post(route('products.store'), [
            'product_code' => 'PRD-TEST-001',
            'product_name' => 'Bibit Parfum Vanilla Royale',
            'description' => 'Aroma manis vanila',
            'category' => 'Bibit Parfum',
            'brand' => 'Mane',
            'unit' => 'LTR',
            'warehouse_type' => WarehouseType::RAW_MATERIAL->value,
            'minimum_stock' => 5,
            'reorder_point' => 15,
            'lead_time_days' => 7,
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $createResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['product_code' => 'PRD-TEST-001']);

        $product = Product::where('product_code', 'PRD-TEST-001')->firstOrFail();

        // 2. Read
        $indexResponse = $this->actingAs($this->admin)->get(route('products.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Bibit Parfum Vanilla Royale');

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('products.update', $product), [
            'product_code' => 'PRD-TEST-001',
            'product_name' => 'Bibit Parfum Vanilla Royale Premium',
            'category' => 'Bibit Parfum',
            'brand' => 'Mane France',
            'unit' => 'LTR',
            'warehouse_type' => WarehouseType::RAW_MATERIAL->value,
            'minimum_stock' => 10,
            'reorder_point' => 20,
            'lead_time_days' => 10,
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $updateResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['product_name' => 'Bibit Parfum Vanilla Royale Premium']);

        // 4. Delete (permanent if no transactions)
        $deleteResponse = $this->actingAs($this->admin)->delete(route('products.destroy', $product));
        $deleteResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_supplier_crud_lifecycle(): void
    {
        // 1. Create Supplier
        $createResponse = $this->actingAs($this->admin)->post(route('suppliers.store'), [
            'supplier_code' => 'SUP-TEST-001',
            'supplier_name' => 'Mane Fragrances',
            'company_name' => 'PT Mane Indonesia',
            'contact_person' => 'Jean Dupont',
            'supplier_category' => 'Bibit Parfum',
            'business_type' => 'Manufacturer',
            'country' => 'Indonesia',
            'status' => SupplierStatus::ACTIVE->value,
        ]);

        $createResponse->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['supplier_code' => 'SUP-TEST-001']);

        $supplier = Supplier::where('supplier_code', 'SUP-TEST-001')->firstOrFail();

        // 2. Read
        $indexResponse = $this->actingAs($this->admin)->get(route('suppliers.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('PT Mane Indonesia');

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('suppliers.update', $supplier), [
            'supplier_code' => 'SUP-TEST-001',
            'supplier_name' => 'Mane Fragrances International',
            'company_name' => 'PT Mane Indonesia Tbk',
            'contact_person' => 'Jean Dupont Jr',
            'supplier_category' => 'Bibit Parfum',
            'business_type' => 'Manufacturer',
            'country' => 'Indonesia',
            'status' => SupplierStatus::ACTIVE->value,
        ]);

        $updateResponse->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['supplier_name' => 'Mane Fragrances International']);

        // 4. Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('suppliers.destroy', $supplier));
        $deleteResponse->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_inventory_transaction_can_be_recorded_with_current_datetime(): void
    {
        $product = Product::factory()->create([
            'unit' => 'LTR',
            'status' => ProductStatus::ACTIVE,
        ]);
        $supplier = Supplier::factory()->create([
            'status' => SupplierStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->admin)->post(route('inventory-transactions.store'), [
            'transaction_date' => now()->format('Y-m-d H:i:s'),
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'transaction_type' => 'goods_receipt',
            'quantity' => '25.000',
            'unit' => 'LTR',
            'reference_type' => 'PO',
            'reference_number' => 'PO-TEST-123',
            'notes' => 'Penerimaan stok bahan baku baru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory_transactions', [
            'reference_number' => 'PO-TEST-123',
            'product_id' => $product->id,
        ]);

        $product->refresh();
        $this->assertSame('25.000', (string) $product->inventoryStock->quantity);
    }

    public function test_user_crud_lifecycle(): void
    {
        // 1. Create User
        $createResponse = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'Staf Gudang Baru',
            'email' => 'stafbaru@yasmingoldencare.com',
            'role' => UserRole::VIEWER->value,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $createResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'stafbaru@yasmingoldencare.com']);

        $newUser = User::where('email', 'stafbaru@yasmingoldencare.com')->firstOrFail();

        // 2. Read
        $indexResponse = $this->actingAs($this->admin)->get(route('users.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Staf Gudang Baru');

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('users.update', $newUser), [
            'name' => 'Staf Gudang Diperbarui',
            'email' => 'stafbaru@yasmingoldencare.com',
            'role' => UserRole::ADMIN->value,
        ]);

        $updateResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['name' => 'Staf Gudang Diperbarui', 'role' => UserRole::ADMIN->value]);

        // 4. Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('users.destroy', $newUser));
        $deleteResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }
}
