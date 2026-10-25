<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin / Pengelola',
            self::VIEWER => 'Viewer',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_reduce(self::cases(), function (array $options, UserRole $case) {
            $options[$case->value] = $case->label();
            return $options;
        }, []);
    }

    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }

    public function canManageMasterData(): bool
    {
        return $this === self::ADMIN;
    }

    public function canRecordInventory(): bool
    {
        return $this === self::ADMIN;
    }

    public function canReverseInventory(): bool
    {
        return $this === self::ADMIN;
    }

    public function canOverrideNegativeStock(): bool
    {
        return $this === self::ADMIN;
    }

    public function canAccessForecasting(): bool
    {
        return $this === self::ADMIN;
    }
}
