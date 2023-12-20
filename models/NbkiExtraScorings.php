<?php

class NbkiExtraScorings extends Core
{
    public function add($score)
    {
        $query = $this->db->placehold("
            INSERT INTO s_nbki_extra_scorings
            SET ?%
        ", $score);

        $this->db->query($query);

        $id = $this->db->insert_id();

        return $id;
    }

    public function get($order_id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM s_nbki_extra_scorings 
            Where order_id = ?
            order by id desc 
            limit 1
        ", $order_id);

        $this->db->query($query);

        $result = $this->db->result();

        return $result;
    }

    public function get_all()
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM s_nbki_extra_scorings 
        ");

        $this->db->query($query);

        $results = $this->db->results();

        return $results;
    }

    public function update($order_id, $data)
    {
        $query = $this->db->placehold("
            UPDATE s_nbki_extra_scorings  SET ?% WHERE order_id = ?
        ", (array)$data, (int)$order_id);
        $nbki_extra_scoring_id = $this->db->query($query);

        return $nbki_extra_scoring_id;
    }

}