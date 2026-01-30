<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_transaction_id',
        'destination_transaction_id',
        'rate',
    ];

    protected $casts = [
        'rate' => 'decimal:18',
    ];

    public function sourceTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'source_transaction_id');
    }

    public function destinationTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'destination_transaction_id');
    }
}
