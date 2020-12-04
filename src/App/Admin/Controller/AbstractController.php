<?php

namespace App\Admin\Controller;

use Base\Mvc\Controller;
use Base\Mvc\ParameterBag;

abstract class AbstractController extends Controller
{
    /**
     * @var string[]
     */
    protected $extensionType = ['.jpeg', '.png', '.jpg'];

    /**
     * @var string
     */
    protected $type = "Admin";

    /**
     *
     */
    protected function setAbstractPhrase()
    {
        $this->phrase += (array)$this->setPhraseByController();
        parent::setAbstractPhrase();
    }

    /**
     * @return array
     */
    protected function setPhraseByController()
    {
        return [];
    }

    /**
     * @param $action
     * @param ParameterBag $params
     * @return \Base\Reply\Redirect|bool
     */
    protected function preDispatchType($action, ParameterBag $params)
    {
        return $this->preDispatchController($action, $params);
    }

    protected function preDispatchController($action, ParameterBag $params)
    {
        if(!isset($_SESSION['is_admin']) && $this->app()->currentRoute()->getContext() != 'connexion')
        {
            return $this->redirect($this->app()->buildLink('admin:login'));
        }
        return true;
    }


    /**
     * @param string $linkEdit
     * @param string $linkDeleted
     * @param string $title
     * @param array $errors
     * @return array
     */
    public function Delete($linkEdit = "", $linkDeleted = "", $title = "", $errors = [] )
    {
        $html = "";
        $htmlForm = "";
        $renderError = "";
        $BaseApp = $this->BaseApp();
        $link = 'src/Template/' . $this->type . '/deleted.php';
        $params = $this->getParam([
            'EditLink' => $linkEdit,
            'linkDeleted' => $linkDeleted,
            'title' => $title,
        ]);
        if(!$this->BaseApp()->request()->isXhr())
        {
            $this->setTitle('Confirmer l\'action');
        }
        if(!empty($errors))
        {
            $this->setMessages($errors);
            $renderError = $this->getMessage()->renderMessageNoHtml();
        }
        include $link;

        return [
            'type' => 'json',
            'html' => $html . $htmlForm,
            'for' => 'deleted'
        ];
    }
}