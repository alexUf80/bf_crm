<?php

class Managers extends Core
{
    private $salt = '0c7540eb7e65b553ec1ba6b20de79608';

    public function get_manager($id)
    {
        $query = $this->db->placehold("
            SELECT * 
            FROM __managers
            WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
        $result = $this->db->result();

        if (!empty($result->team_id))
            $result->team_id = explode(',', $result->team_id);

        return $result;
    }

    public function get_managers($filter = array())
    {
        $id_filter = '';
        $role_filter = '';
        $blocked_filter = '';
        $collection_status_filter = '';
        $keyword_filter = '';
        $limit = 1000;
        $page = 1;

        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));

        if (!empty($filter['role']))
            $role_filter = $this->db->placehold("AND role IN (?@)", (array)$filter['role']);

        if (isset($filter['blocked']))
            $blocked_filter = $this->db->placehold("AND blocked = ?", (int)$filter['blocked']);

        if (!empty($filter['collection_status']))
            $collection_status_filter = $this->db->placehold("AND collection_status_id IN (?@)", (array)$filter['collection_status']);

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword)
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
        }

        if (isset($filter['limit']))
            $limit = max(1, intval($filter['limit']));

        if (isset($filter['page']))
            $page = max(1, intval($filter['page']));

        $sql_limit = $this->db->placehold(' LIMIT ?, ? ', ($page - 1) * $limit, $limit);

        $query = $this->db->placehold("
            SELECT * 
            FROM __managers
            WHERE 1
                $id_filter
                $role_filter
                $blocked_filter
                $keyword_filter
                $collection_status_filter
            ORDER BY id ASC 
            $sql_limit
        ");
        $this->db->query($query);
        if ($results = $this->db->results()) {
            foreach ($results as $result) {
                if (!empty($result->team_id))
                    $result->team_id = explode(',', $result->team_id);
            }
        }

        return $results;
    }

    public function count_managers($filter = array())
    {
        $id_filter = '';
        $role_filter = '';
        $blocked_filter = '';
        $collection_status_filter = '';
        $keyword_filter = '';

        if (!empty($filter['id']))
            $id_filter = $this->db->placehold("AND id IN (?@)", array_map('intval', (array)$filter['id']));

        if (!empty($filter['role']))
            $role_filter = $this->db->placehold("AND role IN (?@)", (array)$filter['role']);

        if (isset($filter['blocked']))
            $blocked_filter = $this->db->placehold("AND blocked = ?", (int)$filter['blocked']);

        if (!empty($filter['collection_status']))
            $collection_status_filter = $this->db->placehold("AND collection_status_id IN (?@)", (array)$filter['collection_status']);

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword)
                $keyword_filter .= $this->db->placehold('AND (name LIKE "%' . $this->db->escape(trim($keyword)) . '%" )');
        }

        $query = $this->db->placehold("
            SELECT COUNT(id) AS count
            FROM __managers
            WHERE 1
                $id_filter
                $role_filter
                $blocked_filter
                $collection_status_filter
                $keyword_filter
        ");
        $this->db->query($query);
        $count = $this->db->result('count');

        return $count;
    }

    public function add_manager($manager)
    {
        $manager = (array)$manager;

        if (!empty($manager['password']))
            $manager['password'] = $this->hash_password($manager['password']);

        $query = $this->db->placehold("
            INSERT INTO __managers SET ?%
        ", (array)$manager);
        $this->db->query($query);
        $id = $this->db->insert_id();

        return $id;
    }

    public function update_manager($id, $manager)
    {
        $manager = (array)$manager;

        if (!empty($manager['password']))
            $manager['password'] = $this->hash_password($manager['password']);

        $query = $this->db->placehold("
            UPDATE __managers SET ?% WHERE id = ?
        ", (array)$manager, (int)$id);
        $this->db->query($query);

        return $id;
    }

    public function delete_manager($id)
    {
        $query = $this->db->placehold("
            DELETE FROM __managers WHERE id = ?
        ", (int)$id);
        $this->db->query($query);
    }

    public function get_roles()
    {
        $this->db->query("
        select *
        from s_managers_roles
        ");
        $results = $this->db->results();

        $roles = [];

        foreach ($results as $result) {
            $roles[$result->name] = $result->translate;
        }

        return $roles;
    }

    public function get_permissions($role)
    {
        $roles = $this->get_roles();

        if (!isset($roles[$role]))
            throw new Exception('Неизвестная роль пользователя: ' . $role);

        $list_permissions = array(
            'managers' => array('developer', 'admin', 'manager', 'cessia', 'senior manager'), // просмотр менеджеров
            'block_manager' => array('developer', 'admin', 'senior collector', 'city_manager'), // блокирование менеджеров
            'create_managers' => array('developer', 'admin', 'senior collector', 'chief_exactor', 'chief_sudblock', 'city_manager'), // создание и редактирование менеджеров
            'my_contracts' => array('developer', 'admin', 'collector', 'senior collector', 'team_collector', 'cessia', 'manager'),
            'collection_report' => array('developer', 'admin', 'senior collector', 'team_collector', 'collector'),
            'zvonobot' => array('developer', 'admin', 'senior collector'),
            'orders' => array('developer', 'admin', 'cessia', 'contact_center', 'quality_control', 'senior collector', 'team_collector', 'risk', 'lawyer'),
            'clients' => array('developer', 'admin', 'cessia', 'manager', 'contact_center', 'cs_pc', 'risk'),
            'settings' => array('developer', 'admin'),
            'settings_risk' => array('risk'),
            'changelogs' => array('developer', 'admin'),
            'handbooks' => array('developer', 'admin'),
            'pages' => array('developer', 'admin'),
            'order_comment' => array('cessia'),
            'approve_contract' => array('developer', 'admin', 'senior manager'),
            'analitics' => array('developer', 'admin', 'senior collector', 'risk'),
            'penalty_statistics' => array('developer', 'admin', 'cessia', 'manager'),
            'collector_mailing' => array('developer', 'admin', 'senior collector', 'team_collector'),
            'tags' => array('developer', 'admin', 'senior collector', 'team_collector'),
            'sms_templates' => array('developer', 'admin', 'senior collector', 'team_collector'),
            'communications' => array('developer', 'admin', 'senior collector', 'team_collector'),
            'tickets' => array('developer'),
            'ticket_handbooks' => array('developer'),
            'close_contract' => array('developer', 'admin', 'team_collector', 'senior collector'),
            'repay_button' => array('developer', 'admin', 'cessia', 'manager'),
            'looker_link' => array('developer', 'admin', 'exactor', 'collector', 'cessia', 'manager', 'senior collector', 'team_collector', 'chief_exactor', 'chief_sudblock'),
            'sudblock' => array('developer', 'admin', 'exactor', 'chief_exactor', 'sudblock', 'chief_sudblock'),
            'sudblock_settings' => array('developer', 'admin', 'chief_exactor', 'chief_sudblock'),
            'change_sudblock_manager' => array('developer', 'admin', 'chief_exactor', 'chief_sudblock'),
            'notifications' => array('developer', 'admin', 'exactor', 'chief_exactor', 'sudblock', 'chief_sudblock', 'collector', 'senior collector', 'team_collector'),
            'add_penalty' => array('developer', 'admin', 'quality_control'),
            'penalties' => array('developer', 'admin', 'quality_control', 'cessia', 'manager', 'cs_pc'),
            'collection_moving' => array('developer', 'admin', 'senior collector', 'team_collector'),
            'neworder' => array('developer', 'admin', 'senior manager', 'cessia', 'manager'),
            'offline' => array('developer', 'admin', 'cs_pc', 'city_manager'),
            'offline_settings' => array('developer', 'admin', 'city_manager'),
        );

        $access_permissions = array();
        foreach ($list_permissions as $permission => $permission_roles)
            if (in_array($role, $permission_roles))
                $access_permissions[] = $permission;

        return $access_permissions;
    }

    public function check_password($login, $password)
    {
        $password = $this->hash_password($password);

        $query = $this->db->placehold("
            SELECT id 
            FROM __managers 
            WHERE login = ?
            AND password = ?
        ", $login, $password);
        $this->db->query($query);

        return $this->db->result('id');
    }

    private function hash_password($password)
    {
        return md5(sha1($this->salt . $password) . $this->salt);
    }
}