<?php

error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class ExpiredClientsBlacklist extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $critical_date = date('Y-m-d', strtotime('-25 days'));

        $this->db->query("
        SELECT count(*) as `count`
        FROM s_contracts
        WHERE return_date <= ?
        and close_date is null
        ", $critical_date);

        $count_expired = $this->db->result('count');

        for ($i = 0; $i <= $count_expired; $i += 500) {
            $this->db->query("
            SELECT user_id, lastname, firstname, patronymic
            FROM s_contracts
            WHERE return_date <= ?
            and close_date is null
            limit $i, 500
            ", $critical_date);

            $users = $this->db->results();

            foreach ($users as $user) {

                $fio = "$user->lastname $user->firstname $user->patronymic";
                $fio = mb_strtolower($fio);
                $in_blacklist = $this->blacklist->search($fio);

                if (empty($in_blacklist)) {
                    $this->blacklist->add_person(['fio' => $fio]);
                }
            }

            usleep(300000);
        }
    }
}

new ExpiredClientsBlacklist();