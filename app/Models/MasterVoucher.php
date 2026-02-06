<?php

namespace App\Models;

use App\Observers\MasterVoucherObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([MasterVoucherObserver::class])]
class MasterVoucher extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'insurance_id',
        'name',
        'type',
        'discount_value',
        'max_discount_amount',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
