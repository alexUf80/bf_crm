<?php

class ReoprtContractsNbki extends Core
{

    // !!!!!!!!!!!!!
    public function get_reoprt_nbki($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __reoprt_contracts_nbki
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);

        $result = $this->db->result();

        return $result;
    }

    // !!!!!!!!!!!!!
    public function get_reoprt_nbkis($filter = array())
    {
        $id_filter = '';
        $order_id_filter = '';
        $limit = 1000;
        $page = 1;
        $sort = 'id DESC';

        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));
        
        if (!empty($filter['order_id']))
            $id_filter = $this->db->placehold("AND order_id IN (?@)", array_map('intval', (array)$filter['order_id']));

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword)
                $keyword_filter .= $this->db->placehold('
                    AND (
                        firstname LIKE "%' . $this->db->escape(trim($keyword)) . '%" 
                        OR lastname LIKE "%' . $this->db->escape(trim($keyword)) . '%" 
                        OR patronymic LIKE "%' . $this->db->escape(trim($keyword)) . '%" 
                        OR phone_mobile LIKE "%' . $this->db->escape(trim($keyword)) . '%" 
                        OR email LIKE "%' . $this->db->escape(trim($keyword)) . '%" 
                    )
                ');
        }

        $query = $this->db->placehold("
            SELECT * 
            FROM __reoprt_contracts_nbki
            WHERE 1
                $id_filter
            ORDER BY $sort
        ");
        $this->db->query($query);

        $results = $this->db->results();

        return $results;
    }

   
    public function add_reoprt_nbki($reoprt_contracts_nbki)
    {
        $query = $this->db->placehold("
            INSERT INTO __reoprt_contracts_nbki SET ?%
        ", (array)$reoprt_contracts_nbki);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    

}