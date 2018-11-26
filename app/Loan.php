<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    protected $guarded = [];

    public static function create($attributes = [])
    {

        DB::beginTransaction();

        try {
            $loan = static::query()->create($attributes);

            $totalRepaymentsAmount =
                $loan->amount +
                ($loan->interest_rate/12 * $loan->amount * $loan->duration) +
                $loan->arrangement_fee;

            $singleRepaymentAmount = $totalRepaymentsAmount / $loan->duration;

            for($i = 1; $i <= $loan->duration; $i++) {
                $repayments[] = [
                    'amount' => $singleRepaymentAmount
                ];
            }

            $loan->repayments()->createMany($repayments);
        }
        catch (\Throwable $exception) {
            DB::rollback();

            throw $exception;
        }

        DB::commit();

        return $loan;
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
}
