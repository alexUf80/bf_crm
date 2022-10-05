<?php
error_reporting(-1);
ini_set('display_errors', 'On');
require_once 'autoload.php';

class Transfer extends Core
{
    public function __construct()
    {
        parent::__construct();
        
    	$this->run();
    }
    
    public function run()
    {
    	$this->transfer_contactpersons();
    }
    
    private function transfer_contactpersons()
    {
//    	$this->db->query("TRUNCATE TABLE __contactpersons");
        
        $query = $this->db->placehold("
            SELECT
                id,
                contact_person_name,
                contact_person_relation,
                contact_person_phone,
                contact_person2_name,
                contact_person2_relation,
                contact_person2_phone,
                contact_person3_name,
                contact_person3_relation,
                contact_person3_phone
            FROM __users
            WHERE contact_person_name IS NOT NULL
            AND contact_person_name != ''
        ");
        $this->db->query($query);
        
        $results = $this->db->results();
        
        foreach ($results as $result)
        {
            if (!empty($result->contact_person_name))
            {
                $contactperson = array(
                    'user_id' => $result->id,
                    'name' => $result->contact_person_name,
                    'relation' => $result->contact_person_relation,
                    'phone' => $result->contact_person_phone,
                );
                $this->contactpersons->add_contactperson($contactperson);
            }
            
            if (!empty($result->contact_person_name))
            {
                $contactperson = array(
                    'user_id' => $result->id,
                    'name' => $result->contact_person2_name,
                    'relation' => $result->contact_person2_relation,
                    'phone' => $result->contact_person2_phone,
                );
                $this->contactpersons->add_contactperson($contactperson);
            }
            
            if (!empty($result->contact_person_name))
            {
                $contactperson = array(
                    'user_id' => $result->id,
                    'name' => $result->contact_person3_name,
                    'relation' => $result->contact_person3_relation,
                    'phone' => $result->contact_person3_phone,
                );
                $this->contactpersons->add_contactperson($contactperson);
            }
        }
        
    }
    
}

new Transfer();