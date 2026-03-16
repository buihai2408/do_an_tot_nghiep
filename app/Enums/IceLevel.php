<?php

namespace App\Enums;

enum IceLevel: string
{
    case None = 'none';
    case Less = 'less';
    case Normal = 'normal';
    case More = 'more';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Không đá',
            self::Less => 'Ít đá',
            self::Normal => 'Đá bình thường',
            self::More => 'Nhiều đá',
        };
    }
}
