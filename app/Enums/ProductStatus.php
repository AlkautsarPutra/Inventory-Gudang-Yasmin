<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the available product statuses.
 */
enum ProductStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case DISCONTINUED = 'DISCONTINUED';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DISCONTINUED => 'Discontinued',
        };
    }

    /**
     * Get the badge color used by the user interface.
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'warning',
            self::DISCONTINUED => 'danger',
        };
    }

    /**
     * Return all available product statuses.
     *
     * @return array<ProductStatus>
     */
    public static function values(): array
    {
        return self::cases();
    }

    /**
     * Return product status options for dropdown components.
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