<?php


namespace App\Pub\Controller;


class Home extends AbstractController
{
    public function actionIndex()
    {
        $this->view('Home');

    }
    /**
     * @return array
     * @throws \Exception
     */
    public function actionOperator()
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
                $output[$key]['logo'] = \Base\Util\Avatar::getAvatar($entity->id, $entity->id, 'xs');
                $output[$key]['title'] = $entity->{$inputs['search']};
            }
        }

        return $this->setViewAjax([
            'total' => !empty($inputs['value']) ? $entities->total() : 0,
            'entities' => $output,
        ]);
    }

}