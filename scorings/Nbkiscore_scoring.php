<?php

class Nbkiscore_scoring extends Core
{
    private $scoring_id;
    private $error = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function run_scoring($scoring_id)
    {
        $scoring = $this->scorings->get_scoring($scoring_id);

        $this->db->query("
        SELECT *
        FROM s_scorings
        WHERE order_id = ?
        and `type` = 'nbki'
        and `status` = 'completed'
        ", $scoring->order_id);

        $nbki = $this->db->result();

        $error = 0;

        if (empty($nbki)) {
            $error = 1;
        } else {
            $nbki = unserialize($nbki->body);

            if ($nbki == false)
                $error = 1;
        }

        if ($error == 1) {
            $update = [
                'status' => 'completed',
                'body' => 'Скоринг НБКИ пуст',
                'success' => 1,
                'string_result' => 'Скоринг НБКИ пуст'
            ];

            $this->scorings->update_scoring($scoring_id, $update);
            return $update;
        }

        if(isset($nbki['json']['AccountReply']['paymtPat']))
        {
            $rezerv = $nbki['json']['AccountReply'];
            unset($nbki['json']['AccountReply']);
            $nbki['json']['AccountReply'][0] = $rezerv;
        }

        /*
        foreach ($nbki['json'] as $reply)
        {

        }
        */

        $update = [
            'status' => 'completed',
            'body' => 'Скоринг НБКИ пуст',
            'success' => 1,
            'string_result' => 'Скоринг НБКИ пуст'
        ];

        $this->scorings->update_scoring($scoring_id, $update);
        return $update;
    }
}