<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Marketing 1',
            'email' => 'marketing1@mail.com',
            'password' => bcrypt('marketing123'),
            'role_id' => Role::where('name', 'marketing')->first()->id,
        ]);

        User::create([
            'name' => 'Cashier 1',
            'email' => 'cashier1@mail.com',
            'password' => bcrypt('cashier123'),
            'role_id' => Role::where('name', 'cashier')->first()->id,
        ]);
    }
}
