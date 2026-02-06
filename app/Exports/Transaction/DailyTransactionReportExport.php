<?php

namespace App\Exports\Transaction;

use App\Actions\ApiIntegration\RSDeltaSuryaApi;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyTransactionReportExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $insurances = RSDeltaSuryaApi::getInsurancesData();
        $transactions = Transaction::with([
            'voucher',
            'creator',
        ])
            ->whereDate('created_at', today()->subDay())
            ->get();

        $transactions->map(function ($transaction) use ($insurances) {
            $insurance = collect($insurances)->firstWhere('id', $transaction->insurance_id);
            $transaction->insurance_name = $insurance ? $insurance['name'] : 'N/A';
        });

        return $transactions;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode Transaksi',
            'Nama Pasien',
            'ID Asuransi',
            'Nama Asuransi',
            'ID Voucher',
            'Nama Voucher',
            'Total Harga Awal',
            'Jumlah Diskon',
            'Total Harga Akhir',
            'Status',
            'Dibuat Oleh',
            'Created At',
            'Updated At',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->transaction_code,
            $transaction->patient_name,
            $transaction->insurance_id,
            $transaction->insurance_name,
            $transaction->voucher ? $transaction->voucher->id : null,
            $transaction->voucher ? $transaction->voucher->name : null,
            $transaction->total_amount_original,
            $transaction->discount_amount,
            $transaction->total_amount_final,
            $transaction->status,
            $transaction->creator ? $transaction->creator->name : null,
            $transaction->created_at,
            $transaction->updated_at,
        ];
    }

    public function styles(Worksheet $worksheet): array
    {
        return [
            // Set bold font for the header row
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
