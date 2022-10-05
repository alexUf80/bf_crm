<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

    /**
     * Уведомление о предстоящем платеже до даты платежа (-3, -2, -1, 0)
     */

class ZvonobotNotifyCron extends Core
{
    private $limitMonth;
    private $limitWeek;
    private $limitDay;
    private $period;

    public function __construct()
    {
    	parent::__construct();
        
        $this->limitMonth = $this->settings->call_limit_communications['month'];
        $this->limitWeek = $this->settings->call_limit_communications['week'];
        $this->limitDay = $this->settings->call_limit_communications['day'];
        $this->period = $this->settings->zvonobot_period_calls;

        $this->run_calls();     
    }
    
    private function run_calls()
    {
        if ($contracts = $this->contracts->get_contracts(array('collection_status' => 2, 'search' => ['delay_plus_from' => 3]))) 
        {
            //номер ключа в массиве соответствует количеству дней до даты платежа
            $arr_day = ['d0', 'd1', 'd2', 'd3'];
            $arr_record_id = [];
            $arr_text = [
                            $this->settings->msg_zvonobot_d0, 
                            $this->settings->msg_zvonobot_d1,
                            $this->settings->msg_zvonobot_d2,
                            $this->settings->msg_zvonobot_d3
                        ];

            foreach ($arr_day as $key => $day) {
                $name = 'msg_'.$day.'_notify_'.date('ymd');
                $text = $arr_text[$key];
                $arr_record_id[$key] = $this->zvonobot->create_record($name, $text, 0);
            }

            $now = new DateTime(); // текущее время на сервере

            foreach ($contracts as $contract)
            {
                $contract->user = $this->users->get_user($contract->user_id);
                $contract->client_time = date('Y-m-d H:i:s');

                if($this->check_limit_calls($contract)) {

                    $date = DateTime::createFromFormat("Y-m-d H:i:s", $contract->return_date); // задаем дату предстоящего платежа
                    $interval = $now->diff($date); // получаем разницу в виде объекта DateInterval
                    $record_id = $arr_record_id[$interval->d];

                    $resp = $this->zvonobot->call($contract->user->phone_mobile, $record_id['data']['id'], $contract->sold);


                    //фиксируе факты дозвона\уведомлений
                    $zvonobot_id = $this->zvonobot->add_zvonobot(array(
                        'user_id' => $contract->user->id,
                        'contract_id' => $contract->id,
                        'yuk' => $contract->sold,
                        'zvonobot_id' => isset($resp['data'][0]['id']) ? $resp['data'][0]['id'] : null,
                        'status' => isset($resp['data'][0]['status']) ? $resp['data'][0]['status'] : 'new',
                        'body' => serialize($resp),
                        'create_date' => date('Y-m-d H:i:s'),
                        'phone' => $contract->user->phone_mobile,
                    ));


                    $this->communications->add_communication(array(
                        'user_id' => $contract->user->id,
                        'manager_id' => 100,
                        'created' => date('Y-m-d H:i:s'),
                        'type' => 'zvonobot',
                        'content' => 'Автоматическая рассылка(new)',
                        'outer_id' => $zvonobot_id,
                        'from_number' => $this->sms->get_originator($contract->sold),
                        'to_number' => $contract->user->phone_mobile,
                        'yuk' => $contract->sold,
                        'result' => serialize($resp),
                    ));

                    $this->set_count_calls($contract);

                    sleep(1);
                }
                
            }
            
        }
             
    }

    private function check_limit_calls($contract)
    {
        /**
         * Проверка исчерпан ли лимит звонков и можно ли в данный момент сделать звонок
         * $counter = ['zvonobot' => [
         *                       'month' => ['number' => 1, 'count' => 8],
         *                       'week' => ['number' => 1, 'count' => 4],
         *                       'day' => ['number' => 15, 'count' => 1],
         *                       'last_call' => '2021-07-27 16:41:29'
         *                       ]];
         * В бд хранится в формате json
         * number - номер месяца\недели\дня от начала года, count - число сделаных уведомлений в месяц\неделю\день
         * last_call - дата последнего уведомления
         */

        $counter = $contract->user->notify_counter;
        

        //Проверка на выходные и рабочие часы
        $clock = date('H', strtotime($contract->client_time));
        $weekday = date('N', strtotime($contract->client_time));
        
        if ($weekday == 6 || $weekday == 7) {
            $workTime = $clock > $this->settings->holiday_worktime['from'] && $clock <= $this->settings->holiday_worktime['to'];
        } else {
            $workTime = $clock > $this->settings->workday_worktime['from'] && $clock <= $this->settings->workday_worktime['to'];
        }

        if(! $workTime) {
            return false;
        }

        //первый запуск счетчика
        if (is_null($counter)) {
            return true;
        }

        $counter = json_decode($counter, true);

        $last_call = $counter['zvonobot']['last_call'];
        $countM = $counter['zvonobot']['month']['count'];
        $countW = $counter['zvonobot']['week']['count'];
        $countD = $counter['zvonobot']['day']['count'];

        $now = new DateTime(); // текущее время на сервере
        $date = DateTime::createFromFormat("Y-m-d H:i:s", $last_call);
        $interval = $now->diff($date); // получаем разницу в виде объекта DateInterval

        //проверяем время последнего уведомления
        if($interval->h <= $this->period) {
            return false;
        }

        //проверка месяца
        if(! $date->format('m') == $now->format('m')) {
            return true;
        } elseif ($this->limitMonth > $countM) {
            return true;
        }

        //проверка недели
        if(! $date->format('W') == $now->format('W')) {
            return true;
        } elseif ($this->limitWeek > $countW) {
            return true;
        }

        //проверка дня
        if(! $date->format('z') == $now->format('z')) {
            return true;
        } elseif ($this->limitDay > $countD) {
            return true;
        }

        //ни одно условие не отработало - в данный момент нельзя сделать уведомление
        return false;

    }

    private function set_count_calls($contract)
    {
        /**
         * Фиксируем дату звонка без увеличения счетчика
         */

        $counter = $contract->user->notify_counter;
        $now = new DateTime(); // текущее время на сервере

        //первый запуск счетчика
        if (is_null($counter)) {
            $counter = ['zvonobot' => [
                                'month' => ['number' => $now->format('m'), 'count' => 0],
                                'week' => ['number' => $now->format('W'), 'count' => 0],
                                'day' => ['number' => $now->format('z'), 'count' => 0],
                                'last_call' => $now->format('Y-m-d H:i:s')
                                ]];
        } else {
            $counter = json_decode($counter, true);
            
            $counter['zvonobot']['last_call'] = $now->format('Y-m-d H:i:s');
        }

        
        //преобразуем в json перед сохранением в бд
        $counter = json_encode($counter); 
        
        $this->users->update_user($contract->user_id, array('notify_counter' => $counter));
    }
    
}
new ZvonobotNotifyCron();