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
        if (! $this->doesRepaymentBelongToCurrentUser($repayment)) {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden to claim a repayment of another customer.');
        }

        $this->validate($request, [
            'transaction_details' => 'required'
        ]);

        $repayment->update($request->only('transaction_details'));

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    protected function doesRepaymentBelongToCurrentUser(Repayment $repayment)
    {
        return (bool) auth()->user()->repayments()
            ->where('repayments.id', $repayment->id)->count();
    }
}
