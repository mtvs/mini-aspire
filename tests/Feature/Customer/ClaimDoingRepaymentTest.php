<?php

namespace Tests\Feature\Customer;

use App\Loan;
use App\User;
use Illuminate\Http\Response;
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

        $this->actingAs($customer, 'api');

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

    /**
     * @test
     */
    public function a_customer_can_not_claim_a_repayment_that_is_not_theirs()
    {
        $this->withExceptionHandling();

        $customer = factory(User::class)->create();

        $this->actingAs($customer, 'api');

        $repayment = factory(Loan::class)->create()
            ->repayments()->first();

        $response = $this->postJson('api/customer/repayment/'.$repayment->id.'/claim', [
            'transaction_details' => '...'
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function a_customer_should_send_valid_data_when_claiming_a_repayment()
    {
        $this->withExceptionHandling();

        $customer = factory(User::class)->create();

        $this->actingAs($customer, 'api');

        $repayment = factory(Loan::class)->create([
            'customer_id' => $customer->id
        ])
            ->repayments()->first();

        $response = $this->postJson('api/customer/repayment/'.$repayment->id.'/claim', [
            'transaction_details' => ''
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function an_unauthenticated_user_can_not_claim_doing_a_repayment()
    {
        $this->withExceptionHandling();

        $repayment = factory(Loan::class)->create()
            ->repayments()->first();

        $response = $this->postJson('api/customer/repayment/'.$repayment->id.'/claim', [
            'transaction_details' => '...'
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
