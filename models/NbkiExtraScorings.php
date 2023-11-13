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

    // public function update_by_scoring($id, $data)
    // {
    //     $query = $this->db->placehold("
    //         UPDATE s_nbki_scoreballs  SET ?% WHERE score_id = ?
    //     ", (array)$data, (int)$id);
    //     var_dump($query);
    //     echo '<hr><hr>';
    //     $this->db->query($query);

    //     return $id;
    // }

}