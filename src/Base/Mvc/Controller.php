<?php
namespace Base\Mvc;

use Base\App;
use Base\BaseApp;
use Base\Mvc\Entity\Finder;
use Base\Reply\Exception;
use Base\Util\breadcrumbs;
use Base\Util\PageNave;

abstract class Controller
{
    protected $type = "Pub";
    protected $redirect = null;
    protected $messages = [];
    protected $iconMessage = true;
    protected $typeMessage = 'error';
    protected $phrase = [];
    protected $errorTitlePhraseKey = "error_title";
    protected $breadcrumb = null;

    /**
     * Controller constructor.
     * @param breadcrumbs|null $breadcrumb
     */
    public function __construct(breadcrumbs $breadcrumb = null)
    {
        $this->setAbstractPhrase();
        $this->setPhrase($this->phrase);
        if($this->breadcrumb == null)
        {
            $this->breadcrumb = $breadcrumb;
        }
    }

    /**
     *
     */
    protected function setAbstractPhrase() {}

    /**
     * @return Entity\DateBase
     */
    public function getDb()
    {
        return  BaseApp::getDb();
    }

    /**
     * @param $shortName
     * @return Finder
     */
    public function finder($shortName)
    {
        return BaseApp::finder($shortName);
    }

    /**
     * @param $shortName
     * @return string
     */
    public function repository($shortName)
    {
        $className = BaseApp::stringToClass($shortName, '%s\Repository\%s');
        return BaseApp::setNewClass($className);
    }


    /**
     * @param $table
     * @return mixed
     */
    public function create($table)
    {
        return BaseApp::create($table);
    }

    /**
     * @return mixed
     */
    public function getPreDispatchLink()
    {
        return $this->redirect;
    }

    /**
     * @param $title
     */
    protected function setTitle($title)
    {
        $this->app()->setTitle($title);
    }

    /**
     * @param $controller
     * @param $action
     * @param array $params
     * @return mixed
     */
    public function rerouteController($controller, $action, $params = [])
    {
        if(method_exists($controller, 'action' . $action))
        {
            $action = 'action' . $action;
            $controller = new $controller($this->breadcrumb);
            return $controller->{$action}($params);
        }
        else
        {

            $this->app()->methodNotFound('invalid_action', $this->BaseApp()->classToString($controller, $this->type), $action);
        }
    }

    /**
     * @param $phraseKey
     * @param array $values
     * @return mixed
     */
    public function phrase($phraseKey, array $values = [])
    {
        return $this->BaseApp()->phrase($phraseKey, $values);
    }

    /**
     * @param string $templateName
     * @param array $params
     * @param null $title
     */
    public function view($templateName = '', array $params = [], $title = null)
    {
        $app = $this->app();
        $BaseApp = $this->BaseApp();
        $error = $this->getMessage();
        $form = $this->form();
        $PageNave = new PageNave($app);
        $this->setTitle($title);
        if(!empty($params))
        {
            $params = $this->getParam($params);
        }
        $breadcrumb = $this->breadcrumb;
        include 'src/Template/' . $this->type . '/breadcrumbs.php';
        include 'src/Template/' . $this->type . '/' . $templateName . '.php';
    }

    /**
     * @return \Base\Reply\Form
     */
    public function form()
    {
        return new \Base\Reply\Form();
    }

    /**
     * @param array $messages
     * @param string $type
     * @param bool $icon
     */
    protected function setMessages(array $messages, $type = 'error', $icon = true)
    {
        $this->messages = $messages;
        $this->typeMessage = $type;
        $this->iconMessage = $icon;
    }

    /**
     * @return \Base\Reply\Message
     */
    protected function getMessage()
    {
        return new \Base\Reply\Message($this->messages, $this->typeMessage, $this->iconMessage);
    }
    /**
     * @param array $params
     * @return \ArrayObject
     */
    public function getParam(array $params)
    {
        return new \ArrayObject($params,\ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param array $phrases
     */
    protected function setPhrase(array $phrases)
    {
        $this->BaseApp()->setPhrase($phrases);
    }

    /**
     * @param $identifier
     * @param $id
     * @param null $with
     * @param null $phraseKey
     * @return mixed
     * @throws \Exception
     */
    public function assertRecordExists($identifier, $id, $with = null, $phraseKey = null)
    {
        $record = BaseApp::find($identifier, $id, $with);
        if (!$record)
        {
            if (!$phraseKey)
            {
                $phraseKey = $this->phrase('requested_page_not_found');
            }
            throw $this->Exception($phraseKey);
        }
        return $record;
    }

    /**
     * @param $phrase
     * @return Exception
     */
    protected function Exception($phrase)
    {
        return new Exception($this, $phrase, $this->phrase($this->errorTitlePhraseKey));
    }

    /**
     * @param $url
     * @param null $message
     * @param string $type
     * @return \Base\Reply\Redirect
     */
    public function redirect($url, $message = null, $type = 'temporary')
    {
        if ($message === null)
        {
            $message = 'your_changes_have_been_saved';
        }
        return new \Base\Reply\Redirect($url, $type, $message);
    }

    /**
     * @return BaseApp
     */
    public function BaseApp()
    {
        return new BaseApp();
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return ($this->getRequestMethod() === 'post' && !BaseApp::request()->isXhr());
    }

    /**
     * @return App
     */
    public function app()
    {
        return new App($this->type);
    }
    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return strtolower($this->getServer('REQUEST_METHOD'));
    }

    /**
    /**
     * @param $key
     * @param bool $fallback
     * @return bool|mixed
     */
    public function getServer($key, $fallback = false)
    {
        if (array_key_exists($key, $_SERVER))
        {
            return $_SERVER[$key];
        }
        else
        {
            return $fallback;
        }
    }
    /**
     * @param $action
     * @param ParameterBag $params
     * @return bool
     */
    public function preDispatch($action, ParameterBag $params)
    {
        return $this->preDispatchType($action, $params);
    }

    /**
     * @param array $data
     * @return array
     */
    public function formAjax(array $data = [])
    {
        if(!$this->BaseApp()->request()->isXhr())
        {
            $viewParams = [
                'formParam' => \Base\Util\Arr::ArrayToObject($data),
                'formRepo' => $this->form(),
                'title' => $data['namePage']
            ];
            $this->view('form', $viewParams, $data['namePage']);
        }
        else
        {
            return [
                'type' => 'json',
                'for' => 'form',
                'data' => $data
            ];
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function displayInfo(array $data = [])
    {
        if(!$this->BaseApp()->request()->isXhr())
        {
            $viewParams = [
                'infoParam' => \Base\Util\Arr::ArrayToObject($data),
                'title' => $data['namePage']
            ];
            $this->view('display_info', $viewParams, $data['namePage']);
        }
        else
        {
            return [
                'type' => 'json',
                'for' => 'info',
                'data' => $data
            ];
        }
    }
    /**
     * @param $error
     * @param $link
     * @return array
     */
    public function setErrorByFromByJason($error, $link)
    {
        return [
            'for' => 'error',
            'type' => 'json',
            'error' => $error,
            'link' => $link
        ];
    }

    public function setViewAjax(array $data)
    {
        return [
            'type' => 'view',
            'data' => $data
        ];
    }

    /**
     * @return bool
     */
    public function getContext()
    {
        return false;
    }
    /**
     * @param $action
     * @param ParameterBag $params
     * @return bool
     */
    protected function preDispatchType($action, ParameterBag $params)
    {
        return true;
    }

    /**
     * @param $key
     * @param null $type
     * @param null $default
     * @return array|bool|\DateTime|false|float|int|string|string[]|null
     */
    public function filter($key, $type = null, $default = null)
    {
        return BaseApp::filter($key, $type, $default);
    }
}