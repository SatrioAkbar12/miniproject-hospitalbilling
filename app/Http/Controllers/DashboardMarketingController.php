<?php

namespace App\Http\Controllers;

use App\Actions\ApiIntegration\RSDeltaSuryaApi;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

class DashboardMarketingController extends Controller
{
    public function index(): Response
    {
        // Fetch insurance data from external API
        $insurances = RSDeltaSuryaApi::getInsurancesData();

        // Data for Visits Charts
        $visitData = Transaction::select('insurance_id')
            ->selectRaw('count(*) as total_visits')
            ->where('status', 'completed')
            ->groupBy('insurance_id')
            ->orderBy('total_visits', 'desc')
            ->get();

        // Map insurance names to visit data
        $visitData = collect($visitData)->map(function ($item) use ($insurances) {
            $insurance = collect($insurances)->firstWhere('id', $item->insurance_id);
            $item->insurance_name = $insurance['name'] ?? 'Unknown';

            return $item;
        });

        // Data for Revenue Charts
        $revenueData = Transaction::select('insurance_id')
            ->selectRaw('sum(total_amount_final) as total_revenue')
            ->where('status', 'completed')
            ->groupBy('insurance_id')
            ->get();

        // Map insurance names to revenue data
        $revenueData = collect($revenueData)->map(function ($item) use ($insurances) {
            $insurance = collect($insurances)->firstWhere('id', $item->insurance_id);
            $item->insurance_name = $insurance['name'] ?? 'Unknown';

            return $item;
        });

        $voucherTrendData = Transaction::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(CASE WHEN voucher_id IS NOT NULL THEN 1 END) as used_count')
            ->selectRaw('COUNT(CASE WHEN voucher_id IS NULL THEN 1 END) as non_used_count')
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return Inertia::render('DashboardMarketing/Index', [
            'visitChart' => [
                'labels' => $visitData->pluck('insurance_name'),
                'datasets' => [
                    [
                        'label' => 'Jumlah Kunjungan',
                        'data' => $visitData->pluck('total_visits'),
                    ],
                ],
            ],
            'revenueChart' => [
                'labels' => $revenueData->pluck('insurance_name'),
                'datasets' => [
                    [
                        'label' => 'Total Pendapatan',
                        'data' => $revenueData->pluck('total_revenue'),
                    ],
                ],
            ],
            'voucherTrendChart' => [
                'labels' => $voucherTrendData->pluck('date'),
                'datasets' => [
                    [
                        'label' => 'Pengguna Voucher',
                        'dataVouchers' => $voucherTrendData->pluck('used_count'),
                    ],
                    [
                        'label' => 'Pengguna Non-Voucher',
                        'dataNonVouchers' => $voucherTrendData->pluck('non_used_count'),
                    ],
                ],
            ],
        ]);
    }
}
