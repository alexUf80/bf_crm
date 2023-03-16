<?php

class InsurancesORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_insurances';
    protected $guarded = [];
    public $timestamps = false;

    public static function create_number($id)
    {
        $number = '';
        $number .= date('y'); // год выпуска полиса
        $number .= '0H3'; // код подразделения выпустившего полис (не меняется)
        $number .= 'NZI'; // код продукта (не меняется)
        $number .= '383'; // код партнера (не меняется)

        $polis_number = str_pad($id, 7, '0', STR_PAD_LEFT);

        $number .= $polis_number;

        return $number;
    }

    public static function get_insurance_cost($amount)
    {
        if ($amount <= 4999)
            return 590;
        elseif ($amount >= 5000 && $amount <= 8999)
            return 890;
        elseif ($amount >= 9000)
            return 990;
    }
}