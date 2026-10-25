<?php

declare(strict_types=1);

namespace App\Enums;

enum InventoryMovementDirection: string
{
    case IN = 'IN';
    case OUT = 'OUT';

    /**
     * Get the display label.
     */
    public function label(): string
    {
        return match ($this) {
            self::IN => 'Inbound',
            self::OUT => 'Outbound',
        };
    }

    /**
     * Get the Bootstrap badge color.
     */
    public function color(): string
    {
        return match ($this) {
            self::IN => 'success',
            self::OUT => 'danger',
        };
    }

    /**
     * Determine whether the movement is inbound.
     */
    public function isInbound(): bool
    {
        return $this === self::IN;
    }

    /**
     * Determine whether the movement is outbound.
     */
    public function isOutbound(): bool
    {
        return $this === self::OUT;
    }

    /**
     * Get the opposite movement direction.
     */
    public function opposite(): self
    {
        return match ($this) {
            self::IN => self::OUT,
            self::OUT => self::IN,
        };
    }

    /**
     * Get all enum values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum options for select inputs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $direction) {
            $options[$direction->value] = $direction->label();
        }

        return $options;
    }
}
