<?php

namespace App\Http\Controllers\Officer;

use App\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoansController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'officer_id' => auth()->id()
        ]);

        Loan::create($request->only([
            'amount', 'duration', 'interest_rate',
            'arrangement_fee', 'customer_id', 'officer_id'
        ]));
    }
}
