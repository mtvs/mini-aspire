<?php

namespace App\Http\Controllers\Customer;

use App\Repayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RepaymentsController extends Controller
{
    public function claim(Repayment $repayment, Request $request)
    {
        $repayment->update($request->only('transaction_details'));

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
