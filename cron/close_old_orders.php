<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * CloseContractsCron
 * Закрывает договра с нулевым остатком долга
 * 
 * Все новые заявки должны уходить в отказ через 1 сутки, 
 * те которые  приняты в работу 1 сутки тоже, но с момента принятия менеджером, 
 * заявки которые на одобрении - думаю нет смысла держать более 3 суток
 * 
 * @author Kopyl Ruslan
 * @copyright 2021
 * @access public
 */
class CloseOldOrdersCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    private function run()
    {
        // Все новые заявки должны уходить в отказ через 1 сутки
        $minus1 = date('Y-m-d', strtotime('-1days'));
        $query = $this->db->placehold("
            SELECT * 
            FROM __orders
            WHERE DATE(date) < ?
            AND status = 0
            ORDER BY date DESC
        ", $minus1);
        $this->db->query($query);
        $results = $this->db->results();
//        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($minus1, $results);echo '</pre><hr />';
        foreach ($results as $order)
        {
            $this->orders->update_order($order->id, array(
                'status' => 8,
                'reason_id' => 2,
                'reject_reason' => 'Вы отказались от займа',
                'reject_date' => date('Y-m-d'),
            ));
        }



        $query = $this->db->placehold("
            SELECT * 
            FROM __orders
            WHERE DATE(accept_date) < ?
            AND status = 1
            ORDER BY date DESC
        ", $minus1);
        $this->db->query($query);
        $results = $this->db->results();

        foreach ($results as $order)
        {
            $this->orders->update_order($order->id, array(
                'status' => 8,
                'reason_id' => 2,
                'reject_reason' => 'Вы отказались от займа',
                'reject_date' => date('Y-m-d'),
            ));
        }


        $minus3 = date('Y-m-d', strtotime('-3days'));
        $query = $this->db->placehold("
            SELECT * 
            FROM __orders
            WHERE DATE(approve_date) < ?
            AND status = 2
            ORDER BY date DESC
        ", $minus3);
        $this->db->query($query);
        $results = $this->db->results();
        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($minus3, $results);echo '</pre><hr />';

        foreach ($results as $order)
        {
            $this->orders->update_order($order->id, array(
                'status' => 8,
                'reason_id' => 2,
                'reject_reason' => 'Вы отказались от займа',
                'reject_date' => date('Y-m-d'),
            ));
        }

/*        
        foreach ($results as $order)
        {
            $core->orders->update_order($order->id, array(
                'status' => 8,
                'reason_id' => 2,
                'reject_reason' => 'Вы отказались от займа',
                'reject_date' => date('Y-m-d'),
            ));
        }
*/




//        // все одобренные но не полученные заявки в отказ клиента
//$query = $core->db->placehold("
//    SELECT * 
//    FROM __orders
//    WHERE date < '2021-07-12 23:59:59'
//    AND (status = 2)
//    ORDER BY date DESC
//");
//$core->db->query($query);
//$results = $core->db->results();
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';
//
//foreach ($results as $order)
//{
//    $core->orders->update_order($order->id, array(
//        'status' => 8,
//        'reason_id' => 2,
//        'reject_reason' => 'Вы отказались от займа',
//        'reject_date' => date('Y-m-d'),
//    ));
//    if (!empty($order->contract_id))
//    {
//        $core->contracts->update_contract($order->contract_id, array('status' => 8));
//    }
//}
//
    }
}
new CloseOldOrdersCron();