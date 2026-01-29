<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            // Fiat Currencies
            [
                'code' => 'USD',
                'name' => 'United States Dollar',
                'symbol' => '$',
                'type' => 'fiat',
                'decimals' => 2,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'type' => 'fiat',
                'decimals' => 2,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound Sterling',
                'symbol' => '£',
                'type' => 'fiat',
                'decimals' => 2,
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'type' => 'fiat',
                'decimals' => 0,
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => '$',
                'type' => 'fiat',
                'decimals' => 2,
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => '$',
                'type' => 'fiat',
                'decimals' => 2,
            ],
            [
                'code' => 'CHF',
                'name' => 'Swiss Franc',
                'symbol' => 'Fr',
                'type' => 'fiat',
                'decimals' => 2,
            ],
            [
                'code' => 'CNY',
                'name' => 'Chinese Yuan',
                'symbol' => '¥',
                'type' => 'fiat',
                'decimals' => 2,
            ],
            
            // Crypto Currencies
            [
                'code' => 'BTC',
                'name' => 'Bitcoin',
                'symbol' => '₿',
                'type' => 'crypto',
                'decimals' => 8,
            ],
            [
                'code' => 'ETH',
                'name' => 'Ethereum',
                'symbol' => 'Ξ',
                'type' => 'crypto',
                'decimals' => 18,
            ],
            [
                'code' => 'USDT',
                'name' => 'Tether',
                'symbol' => '₮',
                'type' => 'crypto',
                'decimals' => 6,
            ],
            [
                'code' => 'USDC',
                'name' => 'USD Coin',
                'symbol' => '$',
                'type' => 'crypto',
                'decimals' => 6,
            ],
            [
                'code' => 'BNB',
                'name' => 'Binance Coin',
                'symbol' => 'BNB',
                'type' => 'crypto',
                'decimals' => 18,
            ],
            [
                'code' => 'SOL',
                'name' => 'Solana',
                'symbol' => 'SOL',
                'type' => 'crypto',
                'decimals' => 9,
            ],
            [
                'code' => 'XRP',
                'name' => 'XRP',
                'symbol' => 'XRP',
                'type' => 'crypto',
                'decimals' => 6,
            ],
            [
                'code' => 'ADA',
                'name' => 'Cardano',
                'symbol' => '₳',
                'type' => 'crypto',
                'decimals' => 6,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}