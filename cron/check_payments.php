<?php
error_reporting(-1);
ini_set('display_errors', 'On');

/*
Проверка транзакций без операций
*/

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class CheckPaymentsCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    private function run()
    {
        $from_time = date('Y-m-d H:00:00', time() - 24*3600);
        $to_time = date('Y-m-d H:00:00', time() - 0.5*3600);
        
//        $from_time = '2021-07-30 06:00:00';
//        $to_time = '2021-07-31 06:00:00';
        
        $query = $this->db->placehold("
            SELECT *
            FROM __transactions AS t
            WHERE (
                (t.operation IS NULL OR t.operation = 0)
                OR callback_response = ''
            )
            AND t.sector != 8079
            AND t.created >= ?
            AND t.created <= ?
            ORDER BY id DESC
        ", $from_time, $to_time);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query);echo '</pre><hr />';        
        $this->db->query($query);
        if ($transactions = $this->db->results())
        {
            echo 'COUNT: '.count($transactions).'<br />';
            foreach ($transactions as $t)
            {
                if (!empty($t->register_id))
                {
                    $url = $this->config->front_url.'/best2pay_callback/payment?id='.$t->register_id;
                    file_get_contents($url);
                    usleep(100000);
            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($url, $t);echo '</pre><hr />';
                }
            }
        }
    }
    
    
    
    
    
    
}
new CheckPaymentsCron();