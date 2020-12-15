<?php


namespace App\Pub\Controller;


use Base\Mvc\ParameterBag;

class Home extends AbstractController
{
    public function actionIndex()
    {
        $this->view('Home');

    }
}