<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class ZvonobotCron extends Core
{
    private $record_id_zero;
    private $record_id_one;
    
    public function __construct()
    {
    	parent::__construct();
        
        $this->record_id_zero = '812862';
        $this->record_id_one = '812870';
        
        $this->yuk_record_id_zero = '812860';
        $this->yuk_record_id_one = '812868';
        
        $this->run_calls();
/*
        switch($this->request->get('action', 'string')):
            
            case 'calls':
                $this->run_calls();
            break;
            
            case 'status':
                $this->run_check_status();
            break;
            
            default:
                exit('UNDEFINED ACTION');
        endswitch;
*/        
    }
    
    private function run_calls()
    {

        if ($contracts = $this->contracts->get_contracts(array('collection_status' => 2)))
        {
            foreach ($contracts as $contract)
            {
                $contract->user = $this->users->get_user($contract->user_id);
                $contract->calls = $this->zvonobot->get_zvonobots(array('contract_id' => $contract->id, 'create_date' => date('Y-m-d')));
                
                $check_communications = $this->communications->check_user($contract->user_id);
                
                if (empty($contract->calls) && $check_communications)
                {
                    if (empty($contract->sold))
                    {
                        if (date('Ymd', strtotime($contract->return_date)) == date('Ymd'))
                            $resp = $this->zvonobot->call($contract->user->phone_mobile, $this->record_id_zero, $contract->sold);
                        else
                            $resp = $this->zvonobot->call($contract->user->phone_mobile, $this->record_id_one, $contract->sold);                        
                    }
                    else
                    {
                        if (date('Ymd', strtotime($contract->return_date)) == date('Ymd'))
                            $resp = $this->zvonobot->call($contract->user->phone_mobile, $this->yuk_record_id_zero, $contract->sold);
                        else
                            $resp = $this->zvonobot->call($contract->user->phone_mobile, $this->yuk_record_id_one, $contract->sold);
                        
                    }
                    
                    $this->zvonobot->add_zvonobot(array(
                        'user_id' => $contract->user->id,
                        'contract_id' => $contract->id,
                        'yuk' => $contract->sold,
                        'zvonobot_id' => isset($resp['data'][0]['id']) ? $resp['data'][0]['id'] : null,
                        'status' => isset($resp['data'][0]['status']) ? $resp['data'][0]['status'] : 'new',
                        'body' => serialize($resp),
                        'create_date' => date('Y-m-d H:i:s'),
                        'phone' => $contract->user->phone_mobile,
                    ));

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract, $resp);echo '</pre><hr />';  
                    
                    $this->set_count_calls($contract);             
                
                    sleep(1);
                }
            }
        }
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
    
    /**
     * ZvonobotCron::run_check_status()
     * не работает 
     * @see:zvonobot_check.php
     * @return void
     */
    private function run_check_status()
    {
        if ($calls = $this->zvonobot->get_zvonobots(array('status' => 'new')))
        {
            foreach ($calls as $call)
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
                    
                    // отправляем смс
                    if ($talkTime > 0 && $talkTime <= 7)
                    {
                        if (date('Ymd', strtotime($contract->return_date)) == date('Ymd'))
                            $sms = "Уважаемый клиент, напоминаем, что сегодня дата оплаты по вашему договору займа. ООО МКК «Наличное +». 88003332484";
                        else
                            $sms = "Уважаемый клиент, уведомляем о наличии просроченной задолженности по вашему договору займа. Не нарушайте принятые вами условия договора. ООО МКК «Наличное +». 88003332484";
                        $this->sms->send($call->phone, $sms);
                    }
                }
                
                echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
//                exit;
            }
        }
    }
}
new ZvonobotCron();