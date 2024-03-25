<?php

class Location_scoring extends Core
{
    private $user_id;
    private $order_id;
    private $audit_id;
    private $type;
    private $exception_regions;
    
    public function run_scoring($scoring_id)
    {
        $update = array();
        
    	$scoring_type = $this->scorings->get_type('location');
        
        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {
                $faktaddress = $this->Addresses->get_address($order->faktaddress_id);
                $order->Regregion = $faktaddress->region;

                if (empty($order->Regregion))
                {
                    $update = array(
                        'status' => 'error',
                        'string_result' => 'в заявке не указан регион регистрации'
                    );
                }
                else
                {
                    $order->Regregion = trim($order->Regregion);
                    $order_Regregion = $order->Regregion;
                    if(mb_substr($order->Regregion, -2) == " г" ||
                    mb_substr($order->Regregion, 0, 2) == "г " ||
                    mb_substr($order->Regregion, -4) == " обл" ||
                    mb_substr($order->Regregion, -5) == " обл." ||
                    mb_substr($order->Regregion, -8) == " область" ||
                    mb_substr($order->Regregion, -8) == " ОБЛАСТЬ" ||
                    mb_substr($order->Regregion, -5) == " край" ||
                    mb_substr($order->Regregion, -5) == " Край" ||
                    mb_substr($order->Regregion, -11) == " республика" ||
                    mb_substr($order->Regregion, -11) == " Республика" ||
                    mb_substr($order->Regregion, -5) == " Респ" ||
                    mb_substr($order->Regregion, -5) == " респ" ||
                    mb_substr($order->Regregion, 0, 5) == "Респ " ||
                    mb_substr($order->Regregion, 0, 5) == "респ " ||
                    mb_substr($order->Regregion, 0, 5) == "Республика" ||
                    mb_substr($order->Regregion, 0, 11) == "Республика " ){
                        $order_Regregion = str_replace(["г ", " г", " область", " ОБЛАСТЬ", " обл.", " обл", " край", " Край", " республика", " Республика", " Респ", "Респ ", "Республика "], "", $order->Regregion);
                    }
                    $exception_regions = array_map('trim', explode(',', mb_strtolower($scoring_type->params['regions'])));
                    $order->Regregion = $order_Regregion;
                    if (stripos($order->Regregion, 'кути')) {
                        $order->Regregion = 'Саха/Якутия';
                    }
                    if (stripos($order->Regregion, 'анси')) {
                        $order->Regregion = 'Ханты-Мансийский';
                    }
                
                    $score = !in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $exception_regions);
                    
                    $red_regions = array_map('trim', explode(',', mb_strtolower($scoring_type->params['red-regions'])));
                    $red = in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $red_regions);

                    $yellow_regions = array_map('trim', explode(',', mb_strtolower($scoring_type->params['yellow-regions'])));
                    $yellow = in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $yellow_regions);
                    
                    $gray_regions = array_map('trim', explode(',', mb_strtolower($scoring_type->params['gray-regions'])));
                    $gray = in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $gray_regions);
                    
                    $update = array(
                        'status' => 'completed',
                        'body' => serialize(array('region' => $order->Regregion)),
                        'success' => $score
                    );
                    $zone_result = '';
                    if ($score){
                        $update['string_result'] = 'Допустимый регион: '.$order->Regregion;
                        if($yellow){
                            $update['string_result'] .= ". ЖЕЛТАЯ ЗОНА";
                            $zone_result = "ЖЕЛТАЯ ЗОНА";
                        }
                        elseif ($red) {
                            $update['string_result'] .= ". КРАСНАЯ ЗОНА";
                            $zone_result = "КРАСНАЯ ЗОНА";
                        }
                        elseif ($gray) {
                            $update['string_result'] .= ". СЕРАЯ ЗОНА";
                            $zone_result = "СЕРАЯ ЗОНА";
                        }
                        else {
                            $update['string_result'] .= ". ЗЕЛЕНАЯ ЗОНА ";
                            $zone_result = "ЗЕЛЕНАЯ ЗОНА";
                        }

                    }
                    else{
                        $update['string_result'] = 'Недопустимый регион: '.$order->Regregion;
                        $zone_result = "ОЧЕНЬ КРАСНАЯ ЗОНА";
                    }

                }
                
            }
            else
            {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найдена заявка'
                );
            }
            
            if (!empty($update)){
                $this->scorings->update_scoring($scoring_id, $update);

                $address['zone'] = $zone_result;
                $this->Addresses->update_address($faktaddress->id, $address);
            }
            
            return $update;

        }
    }
    
    
    public function run($audit_id, $user_id, $order_id)
    {
        $this->user_id = $user_id;
        $this->audit_id = $audit_id;
        $this->order_id = $order_id;
        
        $this->type = $this->scorings->get_type('location');
        $this->exception_regions = explode(',', $this->type->params['regions']);
        
        
        $user = $this->users->get_user((int)$user_id);
        
        return $this->scoring($user->Regregion);        
    }

    private function scoring($region_name)
    {

        $region_user = explode(' ', $region_name);

        $score = 0;

        foreach ($this->exception_regions as $region) {
            foreach ($region_user as $value) {
                if (mb_strtolower($value, 'UTF-8') == $region) {
                    $score = 1;
                }
            }
        }
        
        $add_scoring = array(
            'user_id' => $this->user_id,
            'audit_id' => $this->audit_id,
            'type' => 'location',
            'body' => $region_name,
            'success' => (int)$score
        );
        if ($score == 0)
        {
            $add_scoring['string_result'] = 'Допустимый регион: '.$region_name;
        }
        if($score == 1)
        {
            $add_scoring['string_result'] = 'Недопустимый регион: '.$region_name;
        }
        
        $this->scorings->add_scoring($add_scoring);
        
        return $score;
    }

}