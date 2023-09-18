<?php

class ServicesCost extends Core
{
    public function add($services_cost)
    {
        $query = $this->db->placehold("
        INSERT INTO s_services_cost 
        SET ?%
        ", $services_cost);
        file_put_contents($this->config->root_dir.'files/sas.txt',$query);

        $this->db->query($query);

        return $this->db->insert_id();
    }

    public function get($id)
    {
        $query = $this->db->placehold("
        SELECT * 
        FROM s_services_cost
        where id = ?
        ", $id);

        $this->db->query($query);
        return $this->db->result();
    }

    public function gets($filter = array())
    {
        $region_filter = '';

        if (isset($filter['region']))
            $region_filter = $this->db->placehold("AND region = ?", $this->db->escape(trim($filter['region'])));
        

        $query = $this->db->placehold("
        SELECT * 
        FROM s_services_cost
        where 1
        $region_filter
        ORDER BY region
        ");

        $this->db->query($query);
        return $this->db->results();
    }

    public function update($id)
    {
        $query = $this->db->placehold("
        UPDATE s_services_cost 
        SET ?%
        where id = ?
        ", $id);

        $this->db->query($query);
    }

    public function delete($id)
    {
        $query = $this->db->placehold("
        DELETE FROM s_services_cost
        where id = ?
        ", $id);

        $this->db->query($query);
    }
}