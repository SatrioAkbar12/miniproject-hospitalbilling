<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $transaction->transaction_code }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-10 rounded-lg shadow-md">
        <header class="flex justify-between items-center pb-8 border-b">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">INVOICE</h1>
                <p class="text-gray-500">Kode Transaksi: {{ $transaction->transaction_code }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-500">Tanggal: {{ $transaction->created_at->format('d F Y') }}</p>
            </div>
        </header>

        <section class="mt-10 grid grid-cols-2 gap-10">
            <div>
                <h2 class="text-sm font-semibold text-gray-600 mb-2">Pasien:</h2>
                <p class="text-gray-800 font-medium">{{ $transaction->patient_name }}</p>

            </div>
            <div class="text-right">
                <h2 class="text-sm font-semibold text-gray-600 mb-2">Kasir:</h2>
                <p class="text-gray-800 font-medium">{{ $transaction->creator->name }}</p>
                <p class="text-sm text-gray-500">{{ $transaction->creator->email }}</p>

            </div>
        </section>

        <section class="mt-10">
            @if($insurance)
                <div class="mb-4">
                    <h2 class="text-sm font-semibold text-gray-600 mb-2">Asuransi:</h2>
                    <p class="text-gray-800 font-medium">{{ $insurance['name'] }}</p>
                </div>
            @endif
            @if($transaction->voucher)
                <div>
                    <h2 class="text-sm font-semibold text-gray-600 mb-2">Voucher:</h2>
                    <p class="text-gray-800 font-medium">{{ $transaction->voucher->name }}</p>
                    <p class="text-sm text-gray-500">Diskon:
                        @if ($transaction->voucher->type === 'fixed')
                            Rp {{ number_format($transaction->voucher->discount_value, 2, ',', '.') }}
                        @else
                            {{ number_format($transaction->voucher->discount_value, 0) }}{{ $transaction->voucher->type === 'percentage' ? '%' : ''}}
                        @endif
                        @if ($transaction->voucher->type === 'percentage')
                            (Maksimum: Rp {{ number_format($transaction->voucher->max_discount_amount, 0, ',', '.') }})</p>
                        @endif
                </div>
            @endif
        </section>

        <section class="mt-10">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Rincian Transaksi</h2>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-left text-sm font-semibold text-gray-600 rounded-l-lg">Tindakan medis</th>
                        <th class="p-3 text-right text-sm font-semibold text-gray-600">Harga Asli</th>
                        <th class="p-3 text-right text-sm font-semibold text-gray-600">Diskon</th>
                        <th class="p-3 text-right text-sm font-semibold text-gray-600 rounded-r-lg">Harga Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->details as $detail)
                        <tr class="border-b">
                            <td class="p-3 text-gray-800">{{ $detail->procedure_name }}</td>
                            <td class="p-3 text-right text-gray-800">Rp {{ number_format($detail->price, 2, ',', '.') }}</td>
                            <td class="p-3 text-right text-gray-800">Rp {{ number_format($detail->discount_amount, 2, ',', '.') }}</td>
                            <td class="p-3 text-right text-gray-800">Rp {{ number_format($detail->final_price, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="mt-10 flex justify-end">
            <div class="w-full max-w-xs">
                <div class="flex justify-between text-gray-600">
                    <p>Total Asli:</p>
                    <p>Rp {{ number_format($transaction->total_amount_original, 2, ',', '.') }}</p>
                </div>
                <div class="flex justify-between text-gray-600 mt-2">
                    <p>Total Diskon:</p>
                    <p>Rp {{ number_format($transaction->discount_amount, 2, ',', '.') }}</p>
                </div>
                <div class="mt-4 pt-4 border-t-2">
                    <div class="flex justify-between font-bold text-gray-800 text-lg">
                        <p>Total Akhir:</p>
                        <p>Rp {{ number_format($transaction->total_amount_final, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
