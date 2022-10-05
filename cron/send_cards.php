<?php

error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

$response = array();

class SendCardsCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
                
        $this->run();
    }
    
    private function run()
    {
        $cards = $this->get_cards();
        if (!empty($cards))
            foreach ($cards as $card)
                $this->send_card($card);
    }
    
    private function send_card($card)
    {
        $resp = $this->soap1c->send_card($card);
        if ($resp == 'OK')
        {
            $this->cards->update_card($card->id, array('sent_status'=>1, 'sent_date'=>date('Y-m-d H:i:s')));
        }
        else
        {
            
        }
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($card->id, $resp);echo '</pre><hr />';        
    }
    
    private function get_cards()
    {
        $cards = array();
        $users = array();
        $transactions = array();
        $user_ids = array();
        $transaction_ids = array();
        
        $query_params = array(
            'sent_status' => 0, 
            'sort' => 'id_asc', 
            'limit' => 50,
//            'page' => 300,
        );
        foreach ($this->cards->get_cards($query_params) as $card)
        {
            if (strtotime($card->created) < (time() - 7200))
            {
                $user_ids[] = $card->user_id;
                $transaction_ids[] = $card->transaction_id;
                $cards[$card->id] = $card;
            }
        }
        
        if (!empty($user_ids))
            foreach ($this->users->get_users(array('id'=>$user_ids)) as $u)
                $users[$u->id] = $u;
        
        if (!empty($transaction_ids))
            foreach ($this->transactions->get_transactions(array('id'=>$transaction_ids)) as $t)
                $transactions[$t->id] = $t;
        
        foreach ($cards as $card)
        {
            if (!empty($users[$card->user_id]))
                $card->user = $users[$card->user_id];
            if (!empty($transactions[$card->transaction_id]))
                $card->transaction = $transactions[$card->transaction_id];
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($cards);echo '</pre><hr />';        
        return $cards;
    }
}
new SendCardsCron();