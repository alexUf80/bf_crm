<?php
error_reporting(-1);
ini_set('display_errors', 'On');

/*

#Автоматический отказ, если:
1. Срок пользования прошлого займа = до 2 дней включительно И подаёт на сумму, большую, чем прошлый займ -  отказ по причине "Антиразгон 0-2".
2. ФССП от 50k - по причине "ФССП задолженность"
3. Ст. 46 МЕНЕЕ  1 года - отказ по причине "ФССП задолженность" (за исключением ст. за ЖКХ, штрафы ГИБДД)
В итоге: Если ст. 46 более 1 года ИЛИ менее 1 года, но по ЖКХ и штрафам ГИБДД - работаем.

#На автоматическое одобрение идут клиенты, выполняющие все условия:
1. Не было изменений анкетных данных по сравнению с прошлой заявкой.
2. Карта зачисления займа осталась прежней.
3. С момента последнего автоматического решения прошло не более 180 дней, т.е. 1 раз в полгода клиенты должны проверяться руками. Важно добавить.
4. Пройдена проверка ФМС.
5. Пройдена проверка ИНН.
6. Пройдена проверка ФССП.
При невыполнении хотя бы одного из условий - перевод на ручную обработку.
7. Не пройдена проверка НБКИ.
8. Показатель скора - от 300

#Установление лимита кредитования: выбирается наименьшее между запрошенной суммой клиентов и рассчитанной суммой МКК.

Лимиты МКК зависят от прошлой выданной суммы, срока пользования:
1. Срок пользования до 9 дней = сумма прошлого займа.
2. Срок от 10 дней +3k к прошлой одобренной сумме.
3. Срок от 30 дней +4k к прошлой одобренной сумме.

*/

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class AutoretryCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        if ($this->request->get('test'))
            $this->test();
        else
            $this->run();
    }
    
    private function run()
    {
    	if ($orders = $this->orders->get_orders(array('autoretry' => 1)))
        {
            foreach ($orders as $order)
            {
                // проверяем завершены ли уже скоринги, если нет переходим к следующей
                $scorings = $this->scorings->get_scorings(array('order_id' => $order->order_id, 'type' => array('fssp2', 'fms', 'nbki', 'nbkiscore')));
                $completed_scorings = 1;
                foreach ($scorings as $scoring)
                    if (in_array($scoring->status, array('new', 'process', 'repeat')))
                        $completed_scorings = 0;

                if ($completed_scorings)
                {
                    if ($this->check_autoreject($order))
                    {
                        if ($this->check_anketa($order))
                        {
                            if ($this->check_scorings($order))
                            {
                                if ($this->check_credit_count($order))
                                {
                                    if ($limit = $this->get_limit($order))
                                    {
                                        $new_order_amount = min($order->amount, $limit);
        
                                        $this->close_autoretry($order, 'Одобрение: Рассчитанный лимит: '.$limit.' руб', $new_order_amount);
                                        
                                        // переводим заявку в одобренные
                                        $this->approve_order($order, $new_order_amount);
                                    }
                                }
                            }
                        }
                    }
                }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order);echo '</pre><hr />';                
            }
        }
    }
    
    
    public function check_credit_count($order)
    {
        $params = [
            'date_from' => date('Y-m-d H:i:s', time() - 86400 * 365),
            'status' => [7],
            'user_id' => $order->user_id,
            
        ];        
        $count = $this->orders->count_orders($params);
        
        if ($count > 9)
        {   
            // Отказ по кредитной нагрузке (внутреннее решение) 
            $reason = $this->reasons->get_reason(23);
            return $this->reject($order, $reason, 'Отказ: Количество займов за год: '.$count);    	
        }
        
        return true;
    }
    
    /**
     * AutoretryCron::get_limit()
     * 
        Лимиты МКК зависят от прошлой выданной суммы, срока пользования:
        1. Срок пользования до 9 дней = сумма прошлого займа.
        2. Срок от 10 дней +3k к прошлой одобренной сумме.
        3. Срок от 30 дней +4k к прошлой одобренной сумме.
     
     * @param mixed $order
     * @return void
     */
    public function get_limit($order)
    {
        $limit_amount = 0;
        
        if ($last_contract = $this->contracts->get_last_close_contract($order->user_id))
    	{
            $limit_amount += $last_contract->amount;
            
            $date_open_contract = new DateTime(date('Y-m-d', strtotime($last_contract->inssuance_date)));
            $date_close_contract = new DateTime(date('Y-m-d', strtotime($last_contract->close_date)));
            $diff = $date_close_contract->diff($date_open_contract);
            
            if ($diff->days >= 10)
                $limit_amount += 3000;
            elseif ($diff->days >= 30)
                $limit_amount += 4000;
            
    	}
        
        return $limit_amount;
    }
    
    
    /**
     * AutoretryCron::check_scorings()
     * 
        4. Пройдена проверка ФМС.
        5. Пройдена проверка ИНН.

     * @param object $order
     * @return boolean
     */
    public function check_scorings($order)
    {
        $fms_scoring = $this->scorings->get_type_scoring($order->order_id, 'fms');
        if (empty($fms_scoring->success))
            return $this->close_autoretry($order, 'Ручная обработка: Проверка ФМС не пройдена');    	
        
        if (empty($order->inn))
            return $this->close_autoretry($order, 'Ручная обработка: Проверка ИНН не пройдена');    	
        
        $nbki_scoring = $this->scorings->get_type_scoring($order->order_id, 'nbki');
        if (empty($nbki_scoring->success))
            return $this->close_autoretry($order, 'Ручная обработка: Проверка НБКИ не пройдена');    	
        
/*
        $nbki_scoring = $this->scorings->get_type_scoring($order->order_id, 'nbkiscore');
        if ($nbki_scoring->body < $this->settings->nbkiscore['nk'])
            return $this->close_autoretry($order, 'Ручная обработка: Низкий балл ('.$nbki_scoring->body.') по НБКИ скоринг');    	
*/        
        return true;
    }
    
    /**
     * AutoretryCron::check_anketa()

        1. Не было изменений анкетных данных по сравнению с прошлой заявкой.
        2. Карта зачисления займа осталась прежней.
        3. С момента последнего автоматического решения прошло не более 180 дней, 
        т.е. 1 раз в полгода клиенты должны проверяться руками. Важно добавить.

     * @param object $order
     * @return bolean
     */
    private function check_anketa($order)
    {
        if ($order->client_status == 'crm')
        {
            if ($last_contract = $this->contracts->get_last_close_contract($order->user_id))
        	{
                $last_order = $this->orders->get_order($last_contract->order_id);
                
                if ($last_order->card_id != $order->card_id)
                    return $this->close_autoretry($order, 'Ручная обработка: Изменены данные или карта');
                
                $date_last_contract = new DateTime(date('Y-m-d', strtotime($last_contract->inssuance_date)));
                $date_order = new DateTime(date('Y-m-d', strtotime($order->date)));
                $diff = $date_order->diff($date_last_contract);
                
                if ($diff->m > 5)
                    return $this->close_autoretry($order, 'Ручная обработка: С момента последнего договора прошло более 6 месяцев');
        	}
            
            return true;
        }
        else
        {
            return $this->close_autoretry($order, '');
        }                
    }
    


    /**
     * AutoretryCron::check_autoreject()
     * 
     * Проверяет автоотказ по заявке
     * 
     * @param object $order
     * @return boolean
     */
    private function check_autoreject($order)
    {
        if ($this->check_autoreject_fssp($order))
            if ($this->check_autoreject_nbki($order))
                if ($this->check_autoreject_antirazgon($order))
                    return true;
            
        return false;
    }
    
    /**
     * AutoretryCron::check_autoreject_fssp()
     * 
     * 3. ФССП от 50k - по причине "ФССП задолженность"
       4. Ст. 46 МЕНЕЕ  1 года - отказ по причине "ФССП задолженность" (за исключением ст. за ЖКХ, штрафы ГИБДД)        
       В итоге: Если ст. 46 более 1 года ИЛИ менее 1 года, но по ЖКХ и штрафам ГИБДД - работаем.
     * @param mixed $order
     * @return
     */
    private function check_autoreject_fssp($order)
    {
        if ($fssp_scoring = $this->scorings->get_type_scoring($order->order_id, 'fssp2'))
        {
            if ($fssp_scoring->status == 'completed')
            {
                // Причинa отказа: ФССП задолженность 
                $reason = $this->reasons->get_reason(5);
                $fssp_scoring_body = unserialize($fssp_scoring->body);
                if (isset($fssp_scoring_body['sum']) && $fssp_scoring_body['sum'] > 50000)
                {
                    return $this->reject($order, $reason, 'Отказ: Долг по ФССП - '.$fssp_scoring_body['sum'].' руб');
                }
                
                //TODO: Ст. 46 МЕНЕЕ  1 года
            }
        }
        return true;
    }
    
    private function check_autoreject_nbki($order)
    {
        $nbki_scoring = $this->scorings->get_type_scoring($order->order_id, 'nbkiscore');
        if ($nbki_scoring->status == 'completed')
        {
            if ($nbki_scoring->body < $this->settings->nbkiscore['pk'])
            {
                // Причинa отказа: Скоринговый балл - ниже зоны отсечения
                $reason = $this->reasons->get_reason(1);
                return $this->reject($order, $reason, 'Отказ: Низкий балл ('.$nbki_scoring->body.') по НБКИ скоринг');    	
            }
        }
        
        return true;
    }    
    /**
     * AutoretryCron::check_autoreject_antirazgon()
     * 
     * 1. Срок пользования прошлого займа = до 2 дней включительно, отказ по причине "Антиразгон 0-2".
     * 
     * 2. Срок пользования прошлого займа = до 5 дней включиельно, отказ по причине "Разгон", 
     * исключение - *ПРАВИЛО ПРИБЫЛИ!
     * *Правило прибыли: клиент не подпадает под причину отказа "Разгон" в следующих условиях:
        оплата процентов от 7 000 - максимальная сумма возможного займа - не менее минимально принятой суммы для НК 
        (согласовано рекомендациям на основании данных НБКИ - https://skr.sh/sEFL7VVyYro)
        оплата процентов от 10 000 - максимальная сумма возможного займа - 8 000;
        оплата процентов от 15 000 - максимальная сумма возможного займа - 10 000.
     * @param mixed $order
     * @return void
     */
    private function check_autoreject_antirazgon($order)
    {
        if ($order->client_status == 'crm')
        {
            
            if ($last_contract = $this->contracts->get_last_close_contract($order->user_id))
            {
                $date_open_contract = new DateTime(date('Y-m-d', strtotime($last_contract->inssuance_date)));
                $date_close_contract = new DateTime(date('Y-m-d', strtotime($last_contract->close_date)));
                $diff = $date_close_contract->diff($date_open_contract);
                
                if ($diff->days <= 2 && $order_amount > $last_contract->amount)
                {
                    // Причинa отказа: Антиразгон 0-2
                    $reason = $this->reasons->get_reason(11);
                    return $this->reject($order, $reason, 'Отказ: Срок пользования займом 0-2 дней');
                }
                
            }
        }
    	
        return true;
    }    
    
    
    /**
     * AutoretryCron::close_autoretry()
     * 
     * Останавливает авторешение
     * 
     * @param mixed $order
     * @param mixed $autoretry_result
     * @param integer $max_amount
     * @return
     */
    private function close_autoretry($order, $autoretry_result, $max_amount = 0)
    {
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order);echo '</pre><hr />';
        $this->orders->update_order($order->order_id, array(
            'autoretry' => 0,
            'autoretry_result' => $autoretry_result,
            'autoretry_summ' => $max_amount
        ));                        

echo '<br />Закрываем автоповтор: '.$order->order_id.': '.$autoretry_result.'<br />';        
        return false;
    }
    
    /**
     * AutoretryCron::reject()
     * 
     * Ставит отказ по заявке
     * 
     * @param object $order
     * @param object $reason
     * @param string $autoretry_result
     * @return void
     */
    private function reject($order, $reason, $autoretry_result)
    {
        $this->orders->update_order($order->order_id, [
            'autoretry' => 0,
            'autoretry_result' => $autoretry_result,        
        ]);
return false; // временно отключил отказ

        $update = array(
            'autoretry' => 0,
            'autoretry_result' => $autoretry_result,
            'status' => 3,
            'reason_id' => $reason->id,
            'reject_reason' => $reason->client_name,
            'reject_date' => date('Y-m-d H:i:s'),
            'manager_id' => 1, // System
        );
        $this->orders->update_order($order->order_id, $update);
        
        $old_values = array(
            'manager_id' => $order->manager_id,
        );
        $this->changelogs->add_changelog(array(
            'manager_id' => 1,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'order_status',
            'old_values' => serialize($old_values),
            'new_values' => serialize($update),
            'order_id' => $order->order_id,
            'user_id' => $order->user_id,
        ));

        // снимаем за причину отказа
        $order = $this->orders->get_order((int)$order->order_id);
        if ($reason->type == 'mko' && $order->status != 3)
            $this->best2pay->reject_reason($order);

echo '<br />Ставим отказ "'.$autoretry_result.'" по причине '.$reason->admin_name.'<br />';
        
        return false;
    }
    
    /**
     * AutoretryCron::approve_order()
     * Одобряем заявку
     * @param mixed $order
     * @param mixed $approve_amount
     * @return
     */
    private function approve_order($order, $approve_amount)
    {
return false; // временно отключил одобрение

        if ($order->status != 0)
            return array('error' => 'Неверный статус заявки, возможно Заявка уже одобрена или получен отказ');

        $update = array(
            'status' => 2,
            'manager_id' => 1,
            'approve_date' => date('Y-m-d H:i:s'),
            'amount' => $approve_amount,
            'uid' => exec($this->config->root_dir . 'generic/uidgen'),
        );
        if ($approve_amount <= 4000)
            $update['period'] = min($order->period, 14);
        elseif ($approve_amount <= 7000)
            $update['period'] = min($order->period, 21);
        else
            $update['period'] = $order->period;
        
        $old_values = array(
            'status' => $order->status,
            'manager_id' => $order->manager_id,
            'amount' => $order->amount,
            'period' => $order->period,
        );

        $this->orders->update_order($order->order_id, $update);

        $this->changelogs->add_changelog(array(
            'manager_id' => 1,
            'created' => date('Y-m-d H:i:s'),
            'type' => 'order_status',
            'old_values' => serialize($old_values),
            'new_values' => serialize($update),
            'order_id' => $order->order_id,
            'user_id' => $order->user_id,
        ));

        $accept_code = rand(1000, 9999);

        $new_contract = array(
            'order_id' => $order->order_id,
            'user_id' => $order->user_id,
            'card_id' => $order->card_id,
            'type' => 'base',
            'amount' => $approve_amount,
            'period' => $update['period'],
            'create_date' => date('Y-m-d H:i:s'),
            'status' => 0,
            'base_percent' => $this->settings->loan_default_percent,
            'charge_percent' => $this->settings->loan_charge_percent,
            'peni_percent' => $this->settings->loan_peni,
            'service_sms' => $order->service_sms,
            'service_reason' => $order->service_reason,
            'service_insurance' => $order->service_insurance,
            'accept_code' => $accept_code,
        );
        $contract_id = $this->contracts->add_contract($new_contract);

        $this->orders->update_order($order->order_id, array('contract_id' => $contract_id));

        // отправялем смс
        $msg = 'Активируй займ ' . ($approve_amount * 1) . ' в личном кабинете, код' . $accept_code . ' ecozaym24.ru/lk';
        $this->sms->send($order->phone_mobile, $msg);
        
    }




    public function test()
    {
        $order_id = '251440';
        $order = $this->orders->get_order($order_id);
        
        $check_close_contract = $this->check_autoreject_nbki($order);
        
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($check_close_contract);echo '</pre><hr />';    
    }
    
}
new AutoretryCron();