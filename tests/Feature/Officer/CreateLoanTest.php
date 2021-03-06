<?php

namespace Tests\Feature\Officer;

use App\Loan;
use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateLoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function an_officer_can_create_a_loan_for_a_customer()
    {
        $officer = factory(User::class)->state('officer')->create();

        $this->actingAs($officer, 'api');

        $data = factory(Loan::class)->raw([
            'amount' => 10000,
            'duration' => 3,
            'interest_rate' => .15,
            'arrangement_fee' => 100
        ]);

        $repayment_amount = (10000 + .15/12*10000*3 + 100) / 3;

        $response = $this->postJson('api/officer/loans', $data);

        $response->assertSuccessful();

        $this->assertDatabaseHas('loans', array_merge($data, [
            'id' => 1,
            'officer_id' => $officer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        foreach (range(1, 3) as $i) {
            $this->assertDatabaseHas('repayments', [
                'loan_id' => 1,
                'amount' => $repayment_amount,
                'transaction_details' => null
            ]);
        }
    }

    /**
     * @test
     */
    public function an_officer_should_send_valid_data_when_creating_a_loan()
    {
        $this->withExceptionHandling();

        $officer = factory(User::class)->state('officer')->create();

        $this->actingAs($officer, 'api');

        $response = $this->postJson('api/officer/loans', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *  @test
     */
    public function an_officer_can_not_create_a_loan_for_an_officer()
    {
        $this->withExceptionHandling();

        $officer = factory(User::class)->state('officer')->create();

        $this->actingAs($officer, 'api');

        $data = factory(Loan::class)->raw([
            'customer_id' => $officer->id
        ]);

        $response = $this->postJson('api/officer/loans', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonStructure([
            'errors' => [
                'customer_id'
            ]
        ]);
    }

    /**
     * @test
     */
    public function an_unauthenticated_user_can_not_create_a_loan()
    {
        $this->withExceptionHandling();

        $data = factory(Loan::class)->raw();

        $response = $this->postJson('api/officer/loans', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function a_customer_can_not_create_a_loan()
    {
        $this->withExceptionHandling();

        $customer = factory(User::class)->create();

        $this->actingAs($customer, 'api');

        $data = factory(Loan::class)->raw();

        $response = $this->postJson('api/officer/loans', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
