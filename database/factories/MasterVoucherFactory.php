<?php

namespace Database\Factories;

use App\Actions\ApiIntegration\RSDeltaSuryaApi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterVoucher>
 */
class MasterVoucherFactory extends Factory
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
        $type = fake()->randomElement(['fixed', 'percentage']);
        $discountValue = $type === 'fixed' ? fake()->numberBetween(5000, 100000) : fake()->numberBetween(5, 50);
        $maxDiscountAmount = $type === 'percentage' ? fake()->numberBetween(50000, 200000) : null;

        return [
            'insurance_id' => $insurance['id'],
            'name' => fake()->word().' Voucher',
            'type' => $type,
            'discount_value' => $discountValue,
            'max_discount_amount' => $maxDiscountAmount,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'is_active' => true,
            'created_by' => 1,
        ];
    }
}
