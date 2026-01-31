<?php

namespace App\Enums;

enum PlatformTypeEnum: string
{
    case BANK = 'bank';
    case EXCHANGE = 'exchange';
    case CASINO = 'casino';
    case WALLET = 'wallet';
    case PAYMENT_PROCESSOR = 'payment_processor';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BANK => 'Bank',
            self::EXCHANGE => 'Exchange',
            self::CASINO => 'Casino',
            self::WALLET => 'Wallet',
            self::PAYMENT_PROCESSOR => 'Payment Processor',
            self::OTHER => 'Other',
        };
    }
}
