<?php

namespace App\Http\Controllers;

use App\Actions\ApiIntegration\RSDeltaSuryaApi;
use App\Actions\Transactions\TransactionCodeGenerate;
use App\Http\Requests\Transactions\StoreTransactionRequest;
use App\Http\Requests\Transactions\UpdateTransactionRequest;
use App\Models\MasterVoucher;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\LaravelPdf\Facades\Pdf;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('creator')
            ->withCount('details')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where('transaction_code', 'ilike', '%'.$search.'%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Transaction/Index', [
            'transactions' => $transactions,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create()
    {
        $generatedTransactionCode = TransactionCodeGenerate::generateInvoiceNumber();
        $insurances = RSDeltaSuryaApi::getInsurancesData();
        $vouchers = MasterVoucher::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
        $procedures = RSDeltaSuryaApi::getProceduresData();

        foreach ($procedures as &$procedure) {
            $allPrices = RSDeltaSuryaApi::getProcedurePricesData($procedure['id']);

            $filteredPrices = array_values(array_filter($allPrices, function ($price) {
                $startDate = Carbon::parse($price['start_date']['value'])->startOfDay();
                $endDate = Carbon::parse($price['end_date']['value'])->endOfDay();
                $today = now()->startOfDay();

                return $today->isBetween($startDate, $endDate);
            }));
            $procedure['prices'] = $filteredPrices[0] ?? null;
        }
        unset($procedure);

        return Inertia::render('Transaction/Form', [
            'generated_transaction_code' => $generatedTransactionCode,
            'insurances' => $insurances,
            'vouchers' => $vouchers,
            'procedures' => $procedures,
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        $transaction = Transaction::create($validated);

        if ($request['detail_transactions']) {
            foreach ($request['detail_transactions'] as $detail) {
                $transaction->details()->create($detail);
            }
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        $transaction->load(['details', 'creator', 'voucher']);

        $insurances = RSDeltaSuryaApi::getInsurancesData();
        $vouchers = MasterVoucher::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
        $procedures = RSDeltaSuryaApi::getProceduresData();

        foreach ($procedures as &$procedure) {
            $allPrices = RSDeltaSuryaApi::getProcedurePricesData($procedure['id']);

            $filteredPrices = array_values(array_filter($allPrices, function ($price) {
                $startDate = Carbon::parse($price['start_date']['value'])->startOfDay();
                $endDate = Carbon::parse($price['end_date']['value'])->endOfDay();
                $today = now()->startOfDay();

                return $today->isBetween($startDate, $endDate);
            }));
            $procedure['prices'] = $filteredPrices[0] ?? null;
        }
        unset($procedure);

        return Inertia::render('Transaction/Form', [
            'transaction' => $transaction,
            'insurances' => $insurances,
            'vouchers' => $vouchers,
            'procedures' => $procedures,
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        if ($request['detail_transactions']) {
            $transaction->details()->delete();

            foreach ($request['detail_transactions'] as $detail) {
                $transaction->details()->create($detail);
            }
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function finalize(Transaction $transaction): RedirectResponse
    {
        $transaction->update(['status' => 'completed']);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diselesaikan.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['details', 'creator', 'voucher']);

        $insurances = RSDeltaSuryaApi::getInsurancesData();
        $insurance = collect($insurances)->firstWhere('id', $transaction->insurance_id);

        return Inertia::render('Transaction/Show', [
            'transaction' => $transaction,
            'insurance' => $insurance,
        ]);
    }

    public function downloadInvoice(Transaction $transaction)
    {
        $transaction->load(['details', 'creator', 'voucher']);

        $insurances = RSDeltaSuryaApi::getInsurancesData();
        $insurance = collect($insurances)->firstWhere('id', $transaction->insurance_id);

        $pdf = Pdf::view('transactions.invoice_pdf', [
            'transaction' => $transaction,
            'insurance' => $insurance,
        ]);

        return $pdf->save('invoice_'.$transaction->transaction_code.'.pdf');
    }
}
