<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    use HasUuids;

    protected $fillable = [
        'transaction_id',
        'procedure_id',
        'procedure_name',
        'price',
        'discount_amount',
        'final_price',
    ];

    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
