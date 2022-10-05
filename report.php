<?php
// недоплаты клиентов от 13-15.06.2021
require 'autoload.php';

class LostReport extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    private function run()
    {
        $this->db->query("
            SELECT 
                o.id AS operation_id,
                o.amount,
                o.contract_id,
                o.user_id,
                c.status,
                c.number,
                c.amount AS loan_body,
                u.lastname,
                u.firstname,
                u.patronymic,
                u.phone_mobile 
            FROM __operations AS o
            LEFT JOIN __contracts AS c
            ON c.id = o.contract_id
            LEFT JOIN __users AS u
            ON u.id = o.user_id
            WHERE o.type = 'PAY'
            AND DATE(o.created) >= '2021-06-13'
            AND DATE(o.created) <= '2021-06-15'
            AND c.type = 'base'
            AND c.status = 3
            GROUP BY o.user_id
            ORDER BY o.user_id
        ");
        $operations = $this->db->results();
        
        $total_diff = 0;
        foreach ($operations as $o)
        {
            $addeds = $this->operations->get_operations(array('contract_id'=>$o->contract_id, 'type' => array('PERCENTS', 'PENI', 'CHARGE')));
            $o->total_added = $o->loan_body;
            foreach ($addeds as $ad)
                $o->total_added += $ad->amount;

            $o->paid = 0;
            $paids = $this->operations->get_operations(array('contract_id'=>$o->contract_id, 'type' => array('PAY')));
            foreach ($paids as $p)
                $o->paid += $p->amount;
            if ($o->number == '0611-102082')
                $o->paid += 7454;
            
            $o->diff = $o->total_added - $o->paid;
        
            $total_diff += $o->diff;
        }
        
        
        $this->output($operations, $total_diff);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operations);echo '</pre><hr />';        
    }
    
    private function output($operations, $total_diff)
    {
        $html = '<table width="100%" border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr>';
        $html .= '<td>Договор</td>';
        $html .= '<td>Сумма</td>';
        $html .= '<td>ФИО</td>';
        $html .= '<td>Начислено</td>';
        $html .= '<td>Оплачено</td>';
        $html .= '<td>Разница</td>';
        $html .= '</tr>';
        foreach ($operations as $o):
            if ($o->diff > 0):
                $html .= '<tr>';
                $html .= '<td>'.$o->number.'</td>';
                $html .= '<td>'.($o->loan_body*1).'</td>';
                $html .= '<td>'.$o->lastname.' '.$o->firstname.' '.$o->patronymic.'</td>';
                $html .= '<td>'.$o->total_added.'</td>';
                $html .= '<td>'.$o->paid.'</td>';
                $html .= '<td>'.$o->diff.'</td>';
                $html .= '';
                $html .= '';
                $html .= '';
                $html .= '</tr>';
            endif;
        endforeach;

        $html .= '<tr>';
        $html .= '<td colspan="5">Всего</td>';
        $html .= '<td>'.$total_diff.'</td>';
        $html .= '</tr>';
        
        $html .= '</table>';
        
        echo $html;
    }
}
new LostReport();