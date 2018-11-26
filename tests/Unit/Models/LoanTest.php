<?php

namespace Tests\Unit\Models;

use App\Loan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_creates_its_own_repayments_when_gets_created()
    {
        $loan = Loan::create(factory(Loan::class)->raw([
            'amount' => 10000,
            'duration' => 3,
            'interest_rate' => .15,
            'arrangement_fee' => 100
        ]));

        $repayment_amount = (10000 + .15/12*10000*3 + 100) / 3;

        $this->assertCount($loan->duration, $loan->repayments);

        $loan->repayments->each(function ($repayment) use ($repayment_amount) {
            $this->assertEquals($repayment_amount, $repayment->amount);
        });
    }
}
