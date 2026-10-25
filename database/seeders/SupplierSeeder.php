<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SupplierStatus;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Seed the suppliers table for Toko Parfum Yasmin Golden Care.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Master Perfume Suppliers
        |--------------------------------------------------------------------------
        */

        $suppliers = [
            [
                'supplier_code' => 'SUP-000001',
                'supplier_name' => 'PT Mane Indonesia',
                'company_name' => 'PT Mane Indonesia Fragrance & Flavors',
                'supplier_category' => 'Bibit Parfum & Fragrance House',
                'business_type' => 'International',
                'contact_person' => 'Budi Santoso (Account Manager)',
                'email' => 'sales@id.mane.com',
                'phone_number' => '021-8970001',
                'city' => 'Cikarang, Bekasi',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000002',
                'supplier_name' => 'PT Firmenich Aromatics Indonesia',
                'company_name' => 'PT Firmenich Aromatics Indonesia',
                'supplier_category' => 'Bibit Parfum & Fragrance House',
                'business_type' => 'International',
                'contact_person' => 'Rina Wijaya (Fragrance Specialist)',
                'email' => 'contact.id@firmenich.com',
                'phone_number' => '021-8935512',
                'city' => 'Cikarang, Bekasi',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000003',
                'supplier_name' => 'PT Givaudan Indonesia',
                'company_name' => 'PT Givaudan Fragrances Indonesia',
                'supplier_category' => 'Bibit Parfum & Fixative',
                'business_type' => 'International',
                'contact_person' => 'Hendro Salim',
                'email' => 'info.indonesia@givaudan.com',
                'phone_number' => '021-5278890',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000004',
                'supplier_name' => 'PT Symrise Indonesia',
                'company_name' => 'PT Symrise Scent & Care Indonesia',
                'supplier_category' => 'Bibit Parfum & Aroma Kimia',
                'business_type' => 'International',
                'contact_person' => 'Diana Putri',
                'email' => 'fragrance.id@symrise.com',
                'phone_number' => '021-8971234',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000005',
                'supplier_name' => 'PT IFF Aromatics Indonesia',
                'company_name' => 'PT International Flavors & Fragrances Indonesia',
                'supplier_category' => 'Bibit Parfum & Essential Oil',
                'business_type' => 'International',
                'contact_person' => 'Farhan Pratama',
                'email' => 'orders@iff.com',
                'phone_number' => '021-8754321',
                'city' => 'Karawang',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000006',
                'supplier_name' => 'PT Brataco Chemical Jakarta',
                'company_name' => 'PT Brataco Chemical Divisi Kosmetik',
                'supplier_category' => 'Pelarut, Alkohol & Gliserin',
                'business_type' => 'Local',
                'contact_person' => 'Agus Haryanto',
                'email' => 'sales.jkt@brataco.co.id',
                'phone_number' => '021-6530123',
                'city' => 'Jakarta Utara',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000007',
                'supplier_name' => 'PT Multi Indokemas Sukses',
                'company_name' => 'PT Multi Indokemas Kemasan Kosmetik',
                'supplier_category' => 'Botol Kaca, Crimp & Nozzle',
                'business_type' => 'Local',
                'contact_person' => 'Linda Setiawan',
                'email' => 'order@multiindokemas.com',
                'phone_number' => '031-7668901',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000008',
                'supplier_name' => 'CV Aroma Atsiri Nusantara',
                'company_name' => 'CV Aroma Atsiri Nusantara Botanical',
                'supplier_category' => 'Minyak Atsiri Alami & Oud',
                'business_type' => 'Local',
                'contact_person' => 'Ir. Bambang Sugianto',
                'email' => 'info@atsirinusantara.id',
                'phone_number' => '0274-889123',
                'city' => 'Sleman',
                'province' => 'DI Yogyakarta',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000009',
                'supplier_name' => 'PT Surya Kemasan Prima',
                'company_name' => 'PT Surya Kemasan Prima Printing & Box',
                'supplier_category' => 'Hardbox & Label Kemasan',
                'business_type' => 'Local',
                'contact_person' => 'Jessica Tan',
                'email' => 'packaging@suryaprima.co.id',
                'phone_number' => '021-5591234',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000010',
                'supplier_name' => 'CV Mitra Botol Cantik',
                'company_name' => 'CV Mitra Botol Kosmetik Cantik',
                'supplier_category' => 'Botol Roll-On & Vial Tester',
                'business_type' => 'Local',
                'contact_person' => 'Siti Nurhaliza',
                'email' => 'cs@mitrabotolcantik.com',
                'phone_number' => '022-7204455',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000011',
                'supplier_name' => 'PT Golden Aroma Sentosa',
                'company_name' => 'PT Golden Aroma Sentosa Pelarut Parfum',
                'supplier_category' => 'Pelarut Solfiore & DPG',
                'business_type' => 'Local',
                'contact_person' => 'Kevin Gunawan',
                'email' => 'sales@goldenaroma.id',
                'phone_number' => '021-6901122',
                'city' => 'Jakarta Barat',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
            ],
            [
                'supplier_code' => 'SUP-000012',
                'supplier_name' => 'CV Java Essential Oils',
                'company_name' => 'CV Java Essential Oils & Patchouli',
                'supplier_category' => 'Minyak Nilam & Pengikat Alami',
                'business_type' => 'Local',
                'contact_person' => 'Wahyu Hidayat',
                'email' => 'java.essential@aroma.co.id',
                'phone_number' => '0281-635789',
                'city' => 'Banyumas',
                'province' => 'Jawa Tengah',
                'country' => 'Indonesia',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                [
                    'supplier_code' => $supplier['supplier_code'],
                ],
                [
                    'supplier_name' => $supplier['supplier_name'],
                    'company_name' => $supplier['company_name'],
                    'tax_identification_number' => null,
                    'supplier_category' => $supplier['supplier_category'],
                    'business_type' => $supplier['business_type'],
                    'contact_person' => $supplier['contact_person'],
                    'phone_number' => $supplier['phone_number'],
                    'mobile_number' => $supplier['phone_number'],
                    'email' => $supplier['email'],
                    'website' => null,
                    'street_address' => 'Kawasan Industri Terpadu',
                    'city' => $supplier['city'],
                    'province' => $supplier['province'],
                    'postal_code' => '12000',
                    'country' => $supplier['country'],
                    'status' => SupplierStatus::ACTIVE,
                ]
            );
        }
    }
}
