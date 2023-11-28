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

    // public static function get_insurance_cost($amount)
    // {
    //     if ($amount <= 3999)
    //         return 590;
    //     elseif ($amount >= 4000 && $amount <= 4999)
    //         return 690;
    //     elseif ($amount >= 5000 && $amount <= 6999)
    //         return 890;
    //     elseif ($amount >= 7000 && $amount <= 10999)
    //         return 1490;
    //     elseif ($amount >= 11000)
    //         return 2190;
    // }

    // public function get_insurance_cost($amount, $address_id)
    // {
    //     $address = $this->Addresses->get_address($address_id);
        
    //     $scoring_type = $this->scorings->get_type('location');
        
    //     $reg='green-regions';
    //     $yellow_regions = array_map('trim', explode(',', $scoring_type->params['yellow-regions']));
    //     if(in_array(mb_strtolower(trim($address->region), 'utf8'), $yellow_regions)){
    //         $reg = 'yellow-regions';
    //     }
    //     $red_regions = array_map('trim', explode(',', $scoring_type->params['red-regions']));
    //     if(in_array(mb_strtolower(trim($address->region), 'utf8'), $red_regions)){
    //         $reg = 'red-regions';
    //     }
    //     $exception_regions = array_map('trim', explode(',', $scoring_type->params['regions']));
    //     if(in_array(mb_strtolower(trim($address->region), 'utf8'), $exception_regions)){
    //         $reg = 'regions';
    //     }

    //     $contract_operations = $this->ServicesCost->gets(array('region' => $reg));
    //     if (isset($contract_operations[0]->insurance_cost)) {
            
    //         $insurance_cost_limits = json_decode($contract_operations[0]->insurance_cost);

    //         $array_name = [];
    //         foreach ($insurance_cost_limits as $key => $val) {
    //             $array_name[$key] = $val[0];
    //         }            
    //         array_multisort($array_name, SORT_ASC, $insurance_cost_limits);

    //         foreach ($insurance_cost_limits as $insurance_cost_limit) {
    //             if ($amount < $insurance_cost_limit[0] ) {
    //                 $insurance_cost_amount = $insurance_cost_limit[1];
    //                 break;
    //             }
    //         }

    //         return (float)$insurance_cost_amount;
    //     }
    //     else {
    //         if ($amount <= 3999)
    //             return 590;
    //         elseif ($amount >= 4000 && $amount <= 4999)
    //             return 690;
    //         elseif ($amount >= 5000 && $amount <= 6999)
    //             return 890;
    //         elseif ($amount >= 7000 && $amount <= 10999)
    //             return 1490;
    //         elseif ($amount >= 11000)
    //             return 2190;
    //     }
    // }
}