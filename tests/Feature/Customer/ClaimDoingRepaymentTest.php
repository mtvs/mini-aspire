<?php

namespace Tests\Feature\Customer;

use App\Loan;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClaimDoingRepaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_customer_can_claim_doing_a_repayment()
    {
        $customer = factory(User::class)->create();

        $repayment = factory(Loan::class)->create([
            'customer_id' => $customer->id
        ])->repayments()->first();

        $response = $this->postJson('api/customer/repayment/'.$repayment->id.'/claim', [
            'transaction_details' => '...'
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('repayments', [
            'id' => $repayment->id,
            'transaction_details' => '...'
        ]);
    }
}
