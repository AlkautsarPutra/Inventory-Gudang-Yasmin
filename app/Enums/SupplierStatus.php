<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Represents the available supplier statuses.
 */
enum SupplierStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case SUSPENDED = 'SUSPENDED';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        };
    }

    /**
     * Get the badge color used by the user interface.
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'secondary',
            self::SUSPENDED => 'danger',
        };
    }

    /**
     * Determine whether the supplier can be used for transactions.
     */
    public function isUsable(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Determine whether the supplier is inactive.
     */
    public function isInactive(): bool
    {
        return $this === self::INACTIVE;
    }

    /**
     * Determine whether the supplier is suspended.
     */
    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    /**
     * Return all available supplier statuses.
     *
     * @return array<SupplierStatus>
     */
    public static function values(): array
    {
        return self::cases();
    }

    /**
     * Return supplier status options for dropdown components.
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