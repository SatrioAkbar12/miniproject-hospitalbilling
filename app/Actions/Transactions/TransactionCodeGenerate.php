<?php

namespace App\Actions\Transactions;

use App\Models\Transaction;

class TransactionCodeGenerate
{
    public static function generateInvoiceNumber()
    {
        $date = now()->format('Ymd'); // Hasil: 20260204
        $prefix = 'INV-'.$date.'-';

        // Mencari nomor urut terakhir pada hari ini
        $lastTransaction = Transaction::where('transaction_code', 'like', $prefix.'%')
            ->orderBy('transaction_code', 'desc')
            ->first();

        if (! $lastTransaction) {
            $number = '0001';
        } else {
            // Mengambil 4 angka terakhir dan menambahkannya 1
            $lastNumber = substr($lastTransaction->transaction_code, -4);
            $number = str_pad((int) $lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix.$number;
    }
}
