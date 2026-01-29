<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'currency_id',
        'network_id',
        'amount_native',
        'fee',
        'fee_currency_id',
        'date',
        'description',
        'category',
        'meta_data',
        'related_transaction_id',
        'external_id',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
        'amount' => 'decimal:18',
        'amount_native' => 'decimal:18',
        'fee' => 'decimal:18',
        'meta_data' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function feeCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'fee_currency_id');
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }

    public function relatedTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }

    public function conversionAsSource(): HasOne
    {
        return $this->hasOne(Conversion::class, 'source_transaction_id');
    }

    public function conversionAsDestination(): HasOne
    {
        return $this->hasOne(Conversion::class, 'destination_transaction_id');
    }
}
