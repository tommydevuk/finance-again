<?php

namespace App\Enums;

enum CurrencyTypeEnum: string
{
    case FIAT = 'fiat';
    case CRYPTO = 'crypto';

    public function label(): string
    {
        return match ($this) {
            self::FIAT => 'Fiat',
            self::CRYPTO => 'Crypto',
        };
    }
}
