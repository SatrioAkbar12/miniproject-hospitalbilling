<?php

namespace App\Http\Controllers;

use App\Actions\ApiIntegration\RSDeltaSuryaApi;
use App\Http\Requests\MasterVouchers\StoreVoucherRequest;
use App\Http\Requests\MasterVouchers\UpdateVoucherRequest;
use App\Models\MasterVoucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MasterVoucherController extends Controller
{
    public function index(): Response
    {
        $vouchers = MasterVoucher::with('creator')
            ->when(request()->input('search'), function ($query, $search) {
                $query->where('name', 'ilike', '%'.$search.'%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $insurancesData = RSDeltaSuryaApi::getInsurancesData();

        $vouchers = $vouchers->through(function ($voucher) use ($insurancesData) {
            $insurance = collect($insurancesData)->firstWhere('id', $voucher->insurance_id);
            $voucher->insurance_name = $insurance['name'] ?? 'Unknown';

            return $voucher;
        });

        return Inertia::render('MasterVoucher/Index', [
            'vouchers' => $vouchers,
            'insurances' => $insurancesData,
            'filters' => request()->only(['search']),
        ]);
    }

    public function store(StoreVoucherRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        MasterVoucher::create($validated);

        return redirect()->route('master-vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function update(UpdateVoucherRequest $request, MasterVoucher $masterVoucher): RedirectResponse
    {
        $masterVoucher->update($request->validated());

        return redirect()->route('master-vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(MasterVoucher $masterVoucher): RedirectResponse
    {
        $masterVoucher->delete();

        return redirect()->route('master-vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
