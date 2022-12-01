<?php
error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
    {
        $this->db->query("
        SELECT
        ts.id,
        ts.user_id,
        ts.amount,
        ts.register_id
        FROM s_orders os
        JOIN s_transactions ts ON os.user_id = ts.user_id
        WHERE ts.`description` = 'Привязка карты'
        AND reason_code = 1
        AND os.`status` = 3
        and checked = 0
        order by id desc
        ");

        $transactions = $this->db->results();

        foreach ($transactions as $transaction)
            $this->Best2pay->completeCardEnroll($transaction);
    }
}