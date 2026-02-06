<?php

namespace Database\Seeders;

use App\Models\MasterVoucher;
use Illuminate\Database\Seeder;

class MasterVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterVoucher::factory()->count(20)->create();
    }
}
