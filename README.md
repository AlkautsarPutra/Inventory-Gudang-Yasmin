# Inventory Intelligence Platform

Laravel application for auditable warehouse inventory operations. It uses an immutable inventory ledger and a materialized current-stock projection.

## Local setup (Laragon)

Requirements: PHP 8.3+, MySQL, Composer, Node.js, and npm.

1. Create a MySQL database named `inventory_intelegence`.
2. Configure the database and initial administrator variables in `.env`.
3. Install dependencies and prepare the local database:

```powershell
composer install
npm.cmd install --ignore-scripts
php artisan migrate:fresh --seed --force
npm.cmd run build
php artisan serve
```

Open `http://127.0.0.1:8000/login` and sign in with the initial administrator credentials configured in `.env`.

## Roles

| Role | Permissions |
| --- | --- |
| Administrator | Manage users, products, suppliers, and record inventory transactions. |
| Warehouse Manager | Manage products/suppliers, record transactions, reverse transactions, and override negative-stock protection. |
| Warehouse Staff | Manage products and suppliers; inventory ledger is read-only. |
| Viewer | Dashboard and inventory ledger are read-only. |

## Inventory rules

- Transactions are immutable; corrections use a linked reversal entry.
- `GOODS_RECEIPT` requires an active supplier.
- Transaction unit must match the product master unit.
- A stock adjustment is based on the physical quantity. Its direction and difference are calculated by the domain service.
- Adjustments require a reference type, reference number, and reason.
- Negative stock is rejected unless a warehouse manager records it.
- `inventory_stocks` is updated atomically with its ledger entry and may be rebuilt from the ledger if required.

## Verification

```powershell
php artisan test
php artisan route:list
```
