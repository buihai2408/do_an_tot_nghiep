<?php

namespace App\Enums;

enum SugarLevel: string
{
    case None = 'none';
    case Less = 'less';
    case Normal = 'normal';
    case More = 'more';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Không đường',
            self::Less => 'Ít đường',
            self::Normal => 'Đường bình thường',
            self::More => 'Nhiều đường',
        };
    }
}
