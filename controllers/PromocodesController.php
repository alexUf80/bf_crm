<?php

class PromocodesController extends Controller
{
    public function fetch()
    {
        $sort = $this->request->get('sort');

        if(empty($sort))
            $sort = 'id asc';

        $this->design->assign('sort', $sort);

        $promocodes = $this->promocodes->gets(['sort' => $sort]);
        $this->design->assign('promocodes', $promocodes);

        return $this->design->fetch('promocodes.tpl');
    }
}