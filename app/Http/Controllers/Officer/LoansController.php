<?php

namespace App\Http\Controllers\Officer;

use App\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class LoansController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric|min:1',
            'duration' => 'required|int|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'arrangement_fee' => 'required|numeric|min:0',
            'customer_id' => ['required', Rule::exists('users', 'id')
                ->where('is_officer', 0)],
        ]);

        $request->merge([
            'officer_id' => auth()->id()
        ]);

        return Loan::create($request->only([
            'amount', 'duration', 'interest_rate',
            'arrangement_fee', 'customer_id', 'officer_id'
        ]));
    }
}
