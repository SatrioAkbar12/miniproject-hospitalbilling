<?php

namespace Database\Factories;

use App\Actions\ApiIntegration\RSDeltaSuryaApi;
use App\Models\MasterVoucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $insurances = RSDeltaSuryaApi::getInsurancesData();
        $insurance = collect($insurances)->random();

        $voucher = MasterVoucher::inRandomOrder()->first();
        $totalOriginal = fake()->numberBetween(100000, 1000000);
        $discountAmount = $voucher->type === 'fixed' ? $voucher->discount_value : $totalOriginal * ($voucher->discount_value / 100);
        $totalFinal = $totalOriginal - $discountAmount;

        return [
            'transaction_code' => 'INV-'.fake()->date('Ymd').'-'.fake()->unique()->numberBetween(100, 999),
            'patient_name' => fake()->name(),
            'insurance_id' => $insurance['id'],
            'voucher_id' => $voucher->id,
            'total_amount_original' => $totalOriginal,
            'discount_amount' => $discountAmount,
            'total_amount_final' => $totalFinal,
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
            'created_by' => 1,
        ];
    }
}
