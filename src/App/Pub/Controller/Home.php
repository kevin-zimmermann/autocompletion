<?php


namespace App\Pub\Controller;


class Home extends AbstractController
{
    public function actionIndex()
    {
        $this->view('Home');

    }

}