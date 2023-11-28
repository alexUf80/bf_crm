<?php

class ProlongationServicesCostController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                $methodName = 'action_' . $this->request->post('action', 'string');
                if (method_exists($this, $methodName)) {
                    $this->$methodName();
                }
            }
        }

        $sort = $this->request->get('sort');

        if (empty($sort))
            $sort = 'id asc';

        $this->design->assign('sort', $sort);

        $services_cost = $this->ProloServicesCost->gets(['sort' => $sort]);
        $this->design->assign('services_cost', $services_cost);

        return $this->design->fetch('prolo_services_cost.tpl');
    }

    private function action_delete()
    {
        $code_id = $this->request->post('code_id');

        $this->ProloServicesCost->delete($code_id);
        exit;
    }

    private function action_add()
    {
        // $region = $this->request->post('region');
        $reject_reason_cost = $this->request->post('reject_reason_cost');
        $insurance_cost = $this->request->post('insurance_cost');

        $services_cost =
            [
                'insurance_cost' => $insurance_cost,
            ];

        $this->ProloServicesCost->add($services_cost);
        exit;
    }

    private function action_edit()
    {
        $id = $this->request->post('id');
        // $region = $this->request->post('region');
        $reject_reason_cost = $this->request->post('reject_reason_cost');
        $insurance_cost = $this->request->post('insurance_cost');

        $services_cost =
            [
                'insurance_cost' => $insurance_cost,
            ];

            ProloServicesCostORM::where('id', $id)->update($services_cost);
        exit;
    }

    private function action_get_services_cost()
    {
        $id = $this->request->post('id');
        
        $services_cost = $this->ProloServicesCost->get($id);
        
        echo json_encode($services_cost);
        exit;
    }
}