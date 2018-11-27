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
                $this->createRepayments();
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

    protected function createRepayments()
    {
        $singleRepaymentAmount = $this->totalRepaymentsAmount() / $this->duration;

        for($i = 1; $i <= $this->duration; $i++) {
            $repayments[] = [
                'amount' => $singleRepaymentAmount
            ];
        }

        $this->repayments()->createMany($repayments);
    }

    public function totalRepaymentsAmount()
    {
        return $this->amount +
            ($this->interest_rate/12 * $this->amount * $this->duration) +
            $this->arrangement_fee;
    }
}
