<?php

namespace App\Http\Requests\MasterVouchers;

use App\Enums\MasterVoucherType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateVoucherRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'insurance_id' => ['required'],
            'type' => ['required', new Enum(MasterVoucherType::class)],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_active' => ['sometimes', 'boolean'],
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
            'name.required' => 'Nama voucher harus diisi.',
            'insurance_id.required' => 'Asuransi harus dipilih.',
            'type.required' => 'Tipe voucher harus dipilih.',
            'type.in' => 'Tipe voucher harus berupa fixed atau percentage.',
            'discount_value.required' => 'Nilai diskon harus diisi.',
            'discount_value.numeric' => 'Nilai diskon harus berupa angka.',
            'discount_value.min' => 'Nilai diskon tidak boleh kurang dari 0.',
            'start_date.required' => 'Tanggal mulai harus diisi.',
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'end_date.required' => 'Tanggal akhir harus diisi.',
            'end_date.date' => 'Tanggal akhir harus berupa tanggal yang valid.',
            'end_date.after' => 'Tanggal akhir harus setelah tanggal mulai.',
            'max_discount_amount.numeric' => 'Maksimal diskon harus berupa angka.',
            'max_discount_amount.min' => 'Maksimal diskon tidak boleh kurang dari 0.',
        ];
    }
}
