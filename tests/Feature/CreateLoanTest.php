<?php

namespace Tests\Feature;

use App\Loan;
use App\User;
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

        $this->actingAs($officer);

        $data = factory(Loan::class)->raw();

        $response = $this->post('api/officer/loans', $data);

        $response->assertSuccessful();

        $this->assertDatabaseHas('loans', array_merge($data, [
            'officer_id' => $officer->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }
}
