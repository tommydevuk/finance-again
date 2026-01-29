<?php

namespace Database\Seeders;

use App\Models\Network;
use Illuminate\Database\Seeder;

class NetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $networks = [
            [
                'name' => 'Bitcoin',
                'code' => 'BTC',
                'chain_id' => null, // Not EVM compatible usually
                'currency_symbol' => 'BTC',
                'explorer_url' => 'https://mempool.space',
            ],
            [
                'name' => 'Ethereum Mainnet',
                'code' => 'ETH',
                'chain_id' => 1,
                'currency_symbol' => 'ETH',
                'explorer_url' => 'https://etherscan.io',
            ],
            [
                'name' => 'Binance Smart Chain',
                'code' => 'BSC',
                'chain_id' => 56,
                'currency_symbol' => 'BNB',
                'explorer_url' => 'https://bscscan.com',
            ],
            [
                'name' => 'Polygon',
                'code' => 'MATIC',
                'chain_id' => 137,
                'currency_symbol' => 'MATIC',
                'explorer_url' => 'https://polygonscan.com',
            ],
            [
                'name' => 'Solana',
                'code' => 'SOL',
                'chain_id' => null, // Non-EVM
                'currency_symbol' => 'SOL',
                'explorer_url' => 'https://solscan.io',
            ],
            [
                'name' => 'Avalanche C-Chain',
                'code' => 'AVAX',
                'chain_id' => 43114,
                'currency_symbol' => 'AVAX',
                'explorer_url' => 'https://snowtrace.io',
            ],
            [
                'name' => 'Arbitrum One',
                'code' => 'ARB',
                'chain_id' => 42161,
                'currency_symbol' => 'ETH',
                'explorer_url' => 'https://arbiscan.io',
            ],
            [
                'name' => 'Optimism',
                'code' => 'OP',
                'chain_id' => 10,
                'currency_symbol' => 'ETH',
                'explorer_url' => 'https://optimistic.etherscan.io',
            ],
        ];

        foreach ($networks as $network) {
            Network::firstOrCreate(
                ['code' => $network['code']],
                $network
            );
        }
    }
}