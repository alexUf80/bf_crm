<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class PremierContractsCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    private function run()
    {
        $date_from = date('Y-m-d', time() - 10 * 86400);
        $date_to = date('Y-m-d');
        
        $contracts = $this->soap1c->get_premier_contracts($date_from, $date_to);
        if (!empty($contracts))
        {
            foreach ($contracts as $item)
            {
                if ($current_contract = $this->contracts->get_number_contract($item->Номер))
                {
                    $this->contracts->update_contract($current_contract->id, array(
                        'sold' => 1,
                        'premier' => 1,
                        'premier_date' => date('Y-m-d H:i:s', strtotime($item->ДатаПремьер)),
                    ));
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($item);echo '</pre><hr />';    
                }
            }
        }
    }
    
}
new PremierContractsCron();