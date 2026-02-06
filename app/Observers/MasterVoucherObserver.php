<?php

namespace App\Observers;

use App\Models\MasterVoucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MasterVoucherObserver
{
    /**
     * Handle the MasterVoucher "created" event.
     */
    public function created(MasterVoucher $masterVoucher): void
    {
        Log::info('A new MasterVoucher has been created.', ['voucher_id' => $masterVoucher->id, 'user_id' => Auth::id()]);
    }

    /**
     * Handle the MasterVoucher "updated" event.
     */
    public function updated(MasterVoucher $masterVoucher): void
    {
        Log::info('MasterVoucher has been updated.', ['voucher_id' => $masterVoucher->id, 'user_id' => Auth::id()]);
    }

    /**
     * Handle the MasterVoucher "deleted" event.
     */
    public function deleted(MasterVoucher $masterVoucher): void
    {
        Log::info('MasterVoucher has been deleted.', ['voucher_id' => $masterVoucher->id, 'user_id' => Auth::id()]);
    }

    /**
     * Handle the MasterVoucher "restored" event.
     */
    public function restored(MasterVoucher $masterVoucher): void
    {
        //
    }

    /**
     * Handle the MasterVoucher "force deleted" event.
     */
    public function forceDeleted(MasterVoucher $masterVoucher): void
    {
        //
    }
}
