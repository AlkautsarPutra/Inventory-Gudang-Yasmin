<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the available inventory transaction types.
 */
enum InventoryTransactionType: string
{
    case INITIAL_STOCK = 'initial_stock';
    case GOODS_RECEIPT = 'goods_receipt';
    case GOODS_ISSUE = 'goods_issue';
    case STOCK_ADJUSTMENT = 'stock_adjustment';
    case REVERSAL = 'reversal';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::INITIAL_STOCK => 'Initial Stock',
            self::GOODS_RECEIPT => 'Goods Receipt',
            self::GOODS_ISSUE => 'Goods Issue',
            self::STOCK_ADJUSTMENT => 'Stock Adjustment',
            self::REVERSAL => 'Reversal',
        };
    }

    /**
     * Get the badge color used by the user interface.
     */
    public function color(): string
    {
        return match ($this) {
            self::INITIAL_STOCK => 'secondary',
            self::GOODS_RECEIPT => 'success',
            self::GOODS_ISSUE => 'danger',
            self::STOCK_ADJUSTMENT => 'warning',
            self::REVERSAL => 'info',
        };
    }

    /**
     * Return the direction defined by the transaction type, if any.
     *
     * Stock adjustments derive their direction from the physical count and
     * reversals derive it from the transaction being reversed.
     */
    public function fixedDirection(): ?InventoryMovementDirection
    {
        return match ($this) {
            self::INITIAL_STOCK,
            self::GOODS_RECEIPT => InventoryMovementDirection::IN,
            self::GOODS_ISSUE => InventoryMovementDirection::OUT,
            self::STOCK_ADJUSTMENT,
            self::REVERSAL => null,
        };
    }

    /**
     * Determine whether a supplier is required for this transaction type.
     */
    public function requiresSupplier(): bool
    {
        return $this === self::GOODS_RECEIPT;
    }

    /**
     * Return all available inventory transaction types.
     *
     * @return array<InventoryTransactionType>
     */
    public static function values(): array
    {
        return self::cases();
    }

    /**
     * Return inventory transaction type options for dropdown components.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
