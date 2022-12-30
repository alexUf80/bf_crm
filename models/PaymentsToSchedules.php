<?php

class PaymentsToSchedules extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_payments_to_schedules';
    protected $guarded = [];
    public $timestamps = false;

    public static function getNext($contractId)
    {
        $nextPay = self::where('id', $contractId)
            ->whereIn('status', [0, 1])
            ->orderBy('id', 'asc')
            ->first();

        return $nextPay;
    }

    public static function getCountRemaining($contractId)
    {
        $countRemaining = self::where('id', $contractId)
            ->whereIn('status', [0,1])
            ->count();

        return $countRemaining;
    }
}