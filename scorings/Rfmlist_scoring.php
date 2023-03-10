<?php

class Rfmlist_scoring extends Core
{
    public function run_scoring($scoring_id)
    {
        $scoring = $this->scorings->get_scoring($scoring_id);

        $order = $this->orders->get_order((int)$scoring->order_id);

        $fio = mb_strtolower($order->lastname) . ' ' . mb_strtolower($order->firstname) . ' ' . mb_strtolower($order->patronymic);

        $searchUser = RfmORM::where('fio', $fio)->where('birth', $order->birth)->first();


        $update = array(
            'status' => 'completed',
            'body' => '',
            'success' => empty($searchUser) ? 1 : 0,
            'string_result' => empty($searchUser) ? 'Клиент не найден в списке' : 'Пользователь найден в списке: ' . $searchUser->fio,
        );


        $this->scorings->update_scoring($scoring_id, $update);

        return $update;
    }
}
