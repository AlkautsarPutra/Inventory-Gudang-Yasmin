<?php

declare(strict_types=1);

namespace App\Enums;

enum WarehouseType: string
{
    case RAW_MATERIAL = 'raw_material';
    case FINISHED_GOODS = 'finished_goods';

    public function label(): string
    {
        return match ($this) {
            self::RAW_MATERIAL => 'Gudang Bahan Mentah',
            self::FINISHED_GOODS => 'Gudang Produk Jadi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RAW_MATERIAL => 'amber',
            self::FINISHED_GOODS => 'emerald',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}
