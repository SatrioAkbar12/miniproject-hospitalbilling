<?php

use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('transaction index page loads', function () {
    $response = $this->actingAs($this->user)
        ->get('/transactions');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Transaction/Index')
        ->has('transactions')
    );
});

test('transaction create page loads', function () {
    $response = $this->actingAs($this->user)
        ->get('/transactions/create');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Transaction/Form')
    );
});

test('can create transaction with details', function () {
    $response = $this->actingAs($this->user)
        ->post('/transactions', [
            'transaction_code' => 'TRX001',
            'patient_name' => 'John Doe',
            'insurance_id' => 'INS001',
            'voucher_id' => null,
            'total_amount_original' => 500000,
            'discount_amount' => 50000,
            'total_amount_final' => 450000,
            'status' => 'pending',
            'detail_transactions' => [
                [
                    'procedure_id' => 'PROC001',
                    'procedure_name' => 'Konsultasi',
                    'price' => 250000,
                    'discount_amount' => 25000,
                    'final_price' => 225000,
                ],
                [
                    'procedure_id' => 'PROC002',
                    'procedure_name' => 'Tes Laboratorium',
                    'price' => 250000,
                    'discount_amount' => 25000,
                    'final_price' => 225000,
                ],
            ],
        ]);

    $response->assertRedirect('/transactions');

    $this->assertDatabaseHas('transactions', [
        'transaction_code' => 'TRX001',
        'patient_name' => 'John Doe',
        'status' => 'pending',
    ]);

    $transaction = Transaction::where('transaction_code', 'TRX001')->first();
    expect($transaction->details()->count())->toBe(2);
});

test('can update transaction', function () {
    $transaction = Transaction::factory()->create(['created_by' => $this->user->id]);
    $transaction->details()->create([
        'procedure_id' => 'PROC001',
        'procedure_name' => 'Konsultasi',
        'price' => 250000,
        'discount_amount' => 0,
        'final_price' => 250000,
    ]);

    $response = $this->actingAs($this->user)
        ->patch("/transactions/{$transaction->id}", [
            'transaction_code' => $transaction->transaction_code,
            'patient_name' => 'Updated Patient',
            'insurance_id' => $transaction->insurance_id,
            'voucher_id' => null,
            'total_amount_original' => 500000,
            'discount_amount' => 50000,
            'total_amount_final' => 450000,
            'status' => 'completed',
            'detail_transactions' => [
                [
                    'procedure_id' => 'PROC001',
                    'procedure_name' => 'Konsultasi',
                    'price' => 500000,
                    'discount_amount' => 50000,
                    'final_price' => 450000,
                ],
            ],
        ]);

    $response->assertRedirect('/transactions');

    $transaction->refresh();
    expect($transaction->patient_name)->toBe('Updated Patient');
    expect($transaction->status)->toBe('completed');
    expect($transaction->details()->count())->toBe(1);
});

test('can view transaction edit page', function () {
    $transaction = Transaction::factory()->create(['created_by' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get("/transactions/{$transaction->id}/edit");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Transaction/Form')
        ->has('transaction')
    );
});
