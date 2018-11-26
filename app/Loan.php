<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    protected $guarded = [];

    public function save($options = [])
    {

        DB::beginTransaction();

        try {
            $isNew = ! $this->exists;

            $return = parent::save($options);

            if ($isNew) {
                $totalRepaymentsAmount =
                    $this->amount +
                    ($this->interest_rate/12 * $this->amount * $this->duration) +
                    $this->arrangement_fee;

                $singleRepaymentAmount = $totalRepaymentsAmount / $this->duration;

                for($i = 1; $i <= $this->duration; $i++) {
                    $repayments[] = [
                        'amount' => $singleRepaymentAmount
                    ];
                }

                $this->repayments()->createMany($repayments);
            }
        }
        catch (\Throwable $exception) {
            DB::rollback();

            throw $exception;
        }

        DB::commit();

        return $return;
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
}
