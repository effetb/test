<?php

namespace App\Enum;

enum ColorEnum: string
{
    case ROUGE = 'rouge';
    case VERT = 'vert';
    case BLEU = 'bleu';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
