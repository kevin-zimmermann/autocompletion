<?php


namespace App\Pub\Controller;


use Base\Mvc\ParameterBag;

class Operator extends AbstractController
{
    public function actionIndex(ParameterBag $params)
    {
        $operator = $this->assertOperatorExists($params->id);
        $viewParams = [
            'operator' => $operator
        ];
        $this->view('operator', $viewParams);
    }
    public function actionSearch()
    {
        $inputs = $this->filter([
            'value' => 'str',
            'search' => 'str',
            'entity' => 'str'
        ]);

        $entities = $this->finder($inputs['entity'])
            ->order($inputs['search']);

        $valueToArray = explode(' ', $inputs['value']);

        foreach ($valueToArray as $value)
        {
            $entities->where($inputs['search'], 'like', '%' . $value . '%');
        }
        $output = [];
        if($inputs['value'])
        {
            foreach ($entities->fetch() as $key => $entity)
            {
                $output[$key]['logo'] = \Base\Util\Img::getImg($entity, 'operator_name', 'defaultOperator', 'operator', 'xs');
                $output[$key]['title'] = $entity->{$inputs['search']};
                $output[$key]['link'] = $this->app()->buildLink('Pub:operator', $entity);
            }
        }

        return $this->setViewAjax([
            'total' => !empty($inputs['value']) ? $entities->total() : 0,
            'entities' => $output,
        ]);
    }
    protected function assertOperatorExists($id, $with = null, $phraseKey = null)
    {
        return $this->assertRecordExists('App:Operators', $id, $with, $phraseKey);
    }


}