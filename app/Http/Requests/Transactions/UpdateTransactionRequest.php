<?php

namespace App\Http\Requests\Transactions;

use App\Enums\TransactionStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_code' => 'required|string|unique:transactions,transaction_code,'.$this->route('transaction')->id,
            'patient_name' => 'required|string|max:255',
            'insurance_id' => 'required',
            'voucher_id' => 'nullable|exists:master_vouchers,id',
            'total_amount_original' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount_final' => 'required|numeric|min:0',
            'status' => ['required', 'string', new Enum(TransactionStatus::class)],
            'detail_transactions' => 'nullable|array',
            'detail_transactions.*.procedure_id' => 'required_with:detail_transactions|string|max:255',
            'detail_transactions.*.procedure_name' => 'required_with:detail_transactions|string|max:255',
            'detail_transactions.*.price' => 'required_with:detail_transactions|numeric|min:0',
            'detail_transactions.*.discount_amount' => 'nullable|numeric|min:0',
            'detail_transactions.*.final_price' => 'required_with:detail_transactions|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'transaction_code.required' => 'Kode transaksi harus diisi.',
            'transaction_code.string' => 'Kode transaksi harus berupa string.',
            'transaction_code.unique' => 'Kode transaksi sudah digunakan.',
            'patient_name.required' => 'Nama pasien harus diisi.',
            'patient_name.string' => 'Nama pasien harus berupa string.',
            'patient_name.max' => 'Nama pasien tidak boleh lebih dari 255 karakter.',
            'insurance_id.required' => 'Asuransi harus dipilih.',
            'voucher_id.exists' => 'Voucher yang dipilih tidak valid.',
            'total_amount_original.required' => 'Total jumlah asli harus diisi.',
            'total_amount_original.numeric' => 'Total jumlah asli harus berupa angka.',
            'total_amount_original.min' => 'Total jumlah asli tidak boleh kurang dari 0.',
            'discount_amount.numeric' => 'Jumlah diskon harus berupa angka.',
            'discount_amount.min' => 'Jumlah diskon tidak boleh kurang dari 0.',
            'total_amount_final.required' => 'Total jumlah akhir harus diisi.',
            'total_amount_final.numeric' => 'Total jumlah akhir harus berupa angka.',
            'total_amount_final.min' => 'Total jumlah akhir tidak boleh kurang dari 0.',
            'status.required' => 'Status transaksi harus diisi.',
            'status.string' => 'Status transaksi harus berupa string.',
            'status.in' => 'Status transaksi tidak valid.',
            'detail_transactions.array' => 'Detail transaksi harus berupa array.',
            'detail_transactions.*.procedure_id.required_with' => 'ID prosedur harus diisi untuk setiap detail transaksi.',
            'detail_transactions.*.procedure_id.string' => 'ID prosedur harus berupa string.',
            'detail_transactions.*.procedure_id.max' => 'ID prosedur tidak boleh lebih dari 255 karakter.',
            'detail_transactions.*.procedure_name.required_with' => 'Nama prosedur harus diisi untuk setiap detail transaksi.',
            'detail_transactions.*.procedure_name.string' => 'Nama prosedur harus berupa string.',
            'detail_transactions.*.procedure_name.max' => 'Nama prosedur tidak boleh lebih dari 255 karakter.',
            'detail_transactions.*.price.required_with' => 'Harga harus diisi untuk setiap detail transaksi.',
            'detail_transactions.*.price.numeric' => 'Harga harus berupa angka.',
            'detail_transactions.*.price.min' => 'Harga tidak boleh kurang dari 0.',
            'detail_transactions.*.discount_amount.numeric' => 'Jumlah diskon harus berupa angka.',
            'detail_transactions.*.discount_amount.min' => 'Jumlah diskon tidak boleh kurang dari 0.',
            'detail_transactions.*.final_price.required_with' => 'Harga akhir harus diisi untuk setiap detail transaksi.',
            'detail_transactions.*.final_price.numeric' => 'Harga akhir harus berupa angka.',
            'detail_transactions.*.final_price.min' => 'Harga akhir tidak boleh kurang dari 0.',
        ];
    }
}
