<?php

class Postbacks extends Core
{
    public function add($postback)
    {
        $query = $this->db->placehold("
            INSERT INTO s_postbacks SET ?%
        ", $postback);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }
}