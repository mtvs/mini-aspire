<?php

namespace Tests\Feature;

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

        $data = factory(Loan::class)->raw();

        $response = $this->postJson('api/officer/loans', $data);

        $response->assertSuccessful();

        $this->assertDatabaseHas('loans', array_merge($data, [
            'officer_id' => $officer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]));
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
