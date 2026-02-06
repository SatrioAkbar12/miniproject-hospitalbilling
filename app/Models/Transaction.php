<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'transaction_code',
        'patient_name',
        'insurance_id',
        'voucher_id',
        'total_amount_original',
        'discount_amount',
        'total_amount_final',
        'status',
        'created_by',
    ];

    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(MasterVoucher::class, 'voucher_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
