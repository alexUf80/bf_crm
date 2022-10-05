<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class ZvonobotCheckCron extends Core
{
    private $record_id_zero;
    private $record_id_one;
    
    public function __construct()
    {
    	parent::__construct();
        
        $this->record_id_zero = '812862';
        $this->record_id_one = '812870';
        
        $this->run_check_status();
        
    }
    
    private function run_check_status()
    {
        if ($calls = $this->zvonobot->get_zvonobots(array('status' => 'new', 'sort' => 'date_desc')))
        {
            foreach ($calls as $call)
            {
                if (!empty($call->zvonobot_id) && !empty($call->contract_id))
                {
                    $contract = $this->contracts->get_contract((int)$call->contract_id);
                    
                    $resp = $this->zvonobot->check($call->zvonobot_id, $call->yuk);
                    
                    $status = $resp['data'][0]['calls'][0]['status'];
                    if ($status == 'finished')
                    {
                        $startedAt = $resp['data'][0]['calls'][0]['startedAt'];
                        $answeredAt = $resp['data'][0]['calls'][0]['answeredAt'];
                        $finishedAt = $resp['data'][0]['calls'][0]['finishedAt'];
                        
                        $talkTime = $finishedAt - $answeredAt;
                        
                        $this->zvonobot->update_zvonobot($call->id, array(
                            'status' => 'finished',
                            'result' => $talkTime
                        ));
                        
                        if ($talkTime > 0)
                        {
                            $this->communications->add_communication(array(
                                'user_id' => $contract->user_id,
                                'type' => 'zvonobot',
                                'created' => $call->create_date,
                                'content' => $call->zvonobot_id
                            ));
                        }

                        //фиксируем в счетчик уведомлений
                        if ($talkTime > 7)
                        {
                            $contract->user = $this->users->get_user($contract->user_id);
                            $this->set_count_calls($contract);
                        }
                        
                        // отправляем смс
                        if ($talkTime > 0 && $talkTime <= 7)
                        {
                            if ($call->yuk)
                            {                            
                                if ($contract->collection_status == 1)
                                    $sms = "Уважаемый клиент, напоминаем, что сегодня дата оплаты по вашему договору займа. ООО «ЮК №1». 88002226091";
                                if ($contract->collection_status == 2)
                                    $sms = "Уважаемый клиент, уведомляем о наличии просроченной задолженности по вашему договору займа. Не нарушайте принятые вами условия договора. ООО «ЮК №1». 88002226091";
                            }
                            else
                            {
                                if ($contract->collection_status == 1)
                                    $sms = "Уважаемый клиент, напоминаем, что сегодня дата оплаты по вашему договору займа. ООО МКК «Наличное +». 88003332484";
                                if ($contract->collection_status == 2)
                                    $sms = "Уважаемый клиент, уведомляем о наличии просроченной задолженности по вашему договору займа. Не нарушайте принятые вами условия договора. ООО МКК «Наличное +». 88003332484";                            
                            }
                            $this->sms->send($call->phone, $sms, $call->yuk);
                            
                            $this->zvonobot->update_zvonobot($call->id, array(
                                'sms_text' => $sms,
                                'sms_sent' => date('Y-m-d H:i:s'),
                            ));

                            $this->communications->add_communication(array(
                                'user_id' => $contract->user_id,
                                'type' => 'sms',
                                'created' => date('Y-m-d H:i:s'),
                                'content' => $sms
                            ));
                        }
                    }
                    elseif ($status == 'canceled')
                    {
                        $this->zvonobot->update_zvonobot($call->id, array(
                            'status' => 'canceled',
                        ));
                        
                    }
                    
                    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
    //                exit;
                }
            }
        }
    }

    private function set_count_calls($contract)
    {
        /**
         * Увеличиваем счетчик для "успешных" звонков
         */

        $counter = $contract->user->notify_counter;
        $now = new DateTime(); // текущее время на сервере

        //первый запуск счетчика
        if (is_null($counter)) {
            $counter = ['zvonobot' => [
                                'month' => ['number' => $now->format('m'), 'count' => 1],
                                'week' => ['number' => $now->format('W'), 'count' => 1],
                                'day' => ['number' => $now->format('z'), 'count' => 1],
                                'last_call' => $now->format('Y-m-d H:i:s')
                                ]];
        } else {
            $counter = json_decode($counter, true);
            $needUpdate = true;

            //обновляем счетчик для месяца
            if ($counter['zvonobot']['month']['number'] == $now->format('m')) {
                $newCountM = $counter['zvonobot']['month']['count'];
                $newCountM++;
            } else {
                $newCountM = 1;
                $newCountW = 1;
                $newCountD = 1;
                $needUpdate = false;
            }

            //обновляем счетчик для недели
            if ($needUpdate && ($counter['zvonobot']['week']['number'] == $now->format('W'))) {
                $newCountW = $counter['zvonobot']['week']['count'];
                $newCountW++;
            } else {
                $newCountW = 1;
                $newCountD = 1;
                $needUpdate = false;
            }

            //обновляем счетчик для дня
            if ($needUpdate && ($counter['zvonobot']['day']['number'] == $now->format('z'))) {
                $newCountD = $counter['zvonobot']['day']['count'];
                $newCountD++;
            } else {
                $newCountD = 1;
            }

            $lastCall = $counter['zvonobot']['last_call'];

            //собираем данные в нужную структуру
            $counter = ['zvonobot' => [
                                'month' => ['number' => $now->format('m'), 'count' => $newCountM],
                                'week' => ['number' => $now->format('W'), 'count' => $newCountW],
                                'day' => ['number' => $now->format('z'), 'count' => $newCountD],
                                'last_call' => $lastCall
                                ]];
        }

        //преобразуем в json перед сохранением в бд
        $counter = json_encode($counter); 
        
        $this->users->update_user($contract->user_id, array('notify_counter' => $counter));
    }
}
new ZvonobotCheckCron();