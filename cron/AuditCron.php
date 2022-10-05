<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');

require 'autoload.php';

class AuditCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        file_put_contents($this->config->root_dir.'cron/log.txt', date('d-m-Y H:i:s').PHP_EOL, FILE_APPEND);
    }
    
    public function run()
    {
    	$audits = $this->scorings->get_audits(array('status'=>'new'));

        foreach ($audits as $audit)
            $this->scorings->update_audit($audit->id, array('status'=>'process'));

        foreach ($audits as $audit)
        {
            $this->run_audit($audit);
        }
    }
    
    public function run_audit($audit)
    {
    	foreach ($audit->types as $type)
        {
            $scoring_type = $this->scorings->get_type($type);
            
            $classname = $type."_scoring";
            $scoring_type_result = $this->$classname->run($audit->id, $audit->user_id, $audit->order_id);
            
            if (!$scoring_type_result && $scoring_type->negative_action == 'stop')
            {
                $this->scorings->update_audit($audit->id, array('status'=>'stopped'));
                return false;
            }
            
        }
        
        $this->scorings->update_audit($audit->id, array('status'=>'completed'));
        return true;
    }
    
}

$cron = new AuditCron();
$cron->run();
