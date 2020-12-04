<?php

namespace Base\Router;

use Base\App;
use Base\BaseApp;
use Base\Reply\Exception;
use Base\Reply\Redirect;
use Base\Util\breadcrumbs;

class Route
{

    protected $path;
    protected $controller;
    protected $params = [];
    protected $action = "";
    protected $subName = "";
    protected $RouteFormat = "";
    protected $forceAction = "";
    protected $type = "Pub";
    protected $context;
    protected $title;
    protected $description;
    protected $app;
    protected $newPage;
    protected $ajaxJason = false;
    protected $parent = '';
    /**
     * Route constructor.
     * @param App $app
     * @param $path
     * @param $controller
     * @param string $type
     */
    public function __construct(App $app, $path, $controller, $type = 'Pub')
    {
        $this->app = $app;
        $this->path = trim($path, '/');
        $this->type = $type;
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getSubName()
    {
        return $this->subName;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }
    /**
     * @return bool
     */
    public function getNewPage()
    {
        return $this->newPage;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->path;
    }
    /**
     * @return string
     */
    public function getAjaxJason()
    {
        return $this->ajaxJason;
    }
    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param $url
     * @param bool $context
     * @return bool
     */
    public function match($url, $context = false)
    {
        $format = $this->RouteFormat;

        $parts = explode('/', $url, 2);

        $matchRegex = $this->generateMatchRegexInner($format);

        $suffix = isset($parts[1]) ? $parts[1] : '';
        $prefix = $parts[0];
        if ($prefix != $this->path)
        {
            return false;
        }

        if(!in_array($url, [$this->path, $this->path . '/']) )
        {
            if (!preg_match('#^' . $matchRegex . '#i', $suffix, $textMatch))
            {
                return false;
            }
            $matchText = $textMatch[0];
            $trail = substr($suffix, strlen($matchText));
            $action = isset($textMatch['_action']) ? $textMatch['_action'] : '';
            $params = [];

            unset($textMatch['_action']);
            foreach ($textMatch AS $key => $value)
            {
                if (is_string($key) && strlen($value))
                {
                    $params[$key] = $value;
                }
            }
            $this->params = $params;
            $actionRtrim = rtrim(strval($trail), '/');
            $explodeAction = explode('-', $actionRtrim);

            if(count($explodeAction) > 1)
            {
                foreach ($explodeAction as $v)
                {

                    $action .= ucfirst($v);
                }
            }
            else
            {
                $action .= ucfirst($actionRtrim);
            }
            if (!empty($route['action_prefix']))
            {
                $action = $route['action_prefix'] . $action;
            }
            if(!empty($this->subName))
            {
                $action = $this->subName . $action;
            }
            if (!empty($this->forceAction))
            {
                $action = $this->forceAction;
            }
            $this->action = $action;
        }

        if($context)
        {
            if(empty($parts[1]))
            {
                return 'empty';
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * @param $format
     * @param string $wrapper
     * @return string|string[]|null
     */
    public function generateMatchRegexInner($format, $wrapper = '#')
    {
        $matchRegex = str_replace($wrapper, '\\' . $wrapper, $format);

        $matchRegex = preg_replace_callback(
            '#:(\+)?int(?:_p)?<([a-zA-Z0-9_]+)(?:,[a-zA-Z0-9_]+)?>/?#',
            function ($match)
            {
                $mainMatch = '(?:(?:[^/]*\.)?(?P<' . $match[2] . '>[0-9]+)(?:/|$))';
                return $match[1] ? $mainMatch : "{$mainMatch}?";
            },
            $matchRegex
        );
        $matchRegex = preg_replace_callback(
            '#:(\+)?int_int(?:_p)?<([a-zA-Z0-9_]+),([a-zA-Z0-9_]+)?>/?#',
            function ($match)
            {
                $mainMatch = '(?:(?:(?:[^/]*\.)?(?P<' . $match[3] . '>[0-9]+))\.(?:(?:[^/]*\.)?(?P<' . $match[2] . '>[0-9]+))(?:/|$))';
                return $match[1] ? $mainMatch : "{$mainMatch}?";
            },
            $matchRegex
        );
        $matchRegex = preg_replace_callback(
            '#:(\+)?str(?:_p)?<([a-zA-Z0-9_]+)>/?#',
            function ($match)
            {
                $mainMatch = '(?:(?P<' . $match[2] . '>[a-zA-Z0-9_-]+)/)';
                return $match[1] ? $mainMatch : "{$mainMatch}?";
            },
            $matchRegex
        );

        $matchRegex = preg_replace_callback(
            '#:(\+)?str_int<([a-zA-Z0-9_]+),([a-zA-Z0-9_]+)(?:,[a-zA-Z0-9_]+)?>/?#',
            function ($match)
            {
                $mainMatch = '(?:(?:(?:(?:[^/]*\.)?(?P<' . $match[3] . '>[0-9]+))|-|(?P<' . $match[2] . '>[a-zA-Z0-9_-]+))(?:/|$))';
                return $match[1] ? $mainMatch : "{$mainMatch}?";
            },
            $matchRegex
        );
        $matchRegex = preg_replace(
            '#:page<([a-zA-Z0-9_]+)>/?#',
            '(?:page-(?P<$1>[0-9]+)(?:/|$))?',
            $matchRegex
        );
        $matchRegex = preg_replace(
            '#:page/?#',
            '(?:page-(?P<page>[0-9]+)(?:/|$))?',
            $matchRegex
        );
        $matchRegex = str_replace(
            ':action',
            '(?P<_action>[^/]*)',
            $matchRegex
        );
        $matchRegex = preg_replace_callback(
            '#:(\+)?any<([a-zA-Z0-9_]+)>/?#',
            function ($match)
            {
                if ($match[1])
                {
                    return '(?P<' . $match[2] . '>.+)';
                }
                else
                {
                    return '(?P<' . $match[2] . '>.*)';
                }
            },
            $matchRegex
        );
        return $matchRegex;
    }

    /**
     * @param $forceAction
     * @return $this
     */
    public function forceAction($forceAction)
    {
        $this->forceAction = $forceAction;
        return $this;
    }
    /**
     * @param $subName
     * @return $this
     */
    public function subName($subName)
    {
        $this->subName = $subName;
        return $this;
    }

    /**
     * @param $RouteFormat
     * @return $this
     */
    public function RouteFormat($RouteFormat)
    {
        $this->RouteFormat = $RouteFormat;
        return $this;
    }

    /**
     * @return \Base\Mvc\ParameterBag
     */
    public function getParameterBag()
    {
        return new \Base\Mvc\ParameterBag($this->params);
    }

    /**
     * @param $context
     * @return $this
     */
    public function context($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param $newPage
     * @return $this
     */
    public function newPage($newPage)
    {
        $this->newPage = $newPage;
        return $this;
    }

    /**
     * @param $ajaxJason
     * @return $this
     */
    public function ajaxJason($ajaxJason)
    {
        $this->ajaxJason = $ajaxJason;
        return $this;
    }
    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @param breadcrumbs|null $breadcrumb
     * @return mixed|null
     */
    public function call(breadcrumbs $breadcrumb = null)
    {
        $controller = BaseApp::stringToClass($this->controller, '%s\\' . $this->type . '\Controller\%s');
        $controller = new $controller($breadcrumb);
        $action = "action";
        $dataUrl = $this->action;
        if(!$dataUrl)
        {
            $action .= "Index";
        }
        else
        {
            $explodeUrl = explode('/', $dataUrl);
            $outputAction = "";
            foreach ($explodeUrl as $url)
            {
                $outputAction .= ucfirst($url);
            }
            $action .= $outputAction;
        }
        $action = preg_replace('#-#', '', $action);
        $params = $this->getParameterBag();

        try{
            $preDispatch = $controller->preDispatch($action, $params);
            if($preDispatch instanceof Redirect)
            {
                return $preDispatch;
            }
        }
        catch (Exception $e)
        {
            $e->errorMessage();
            return null;

        }
        if(method_exists($controller, $action))
        {
            try {
                if($controllerContext = $controller->getContext())
                {
                    if(isset($controllerContext[$action]))
                    {
                        $_SESSION['currentPage'] = $controllerContext[$action];
                    }
                }
                return call_user_func_array([$controller, $action], [$params]);
            }
            catch (Exception $e)
            {
                $e->errorMessage();
            }
        }
        else
        {
            $this->app->methodNotFound('invalid_action', $this->controller, $action);
            return null;
        }
    }

    /**
     * @return BaseApp
     */
    protected function BaseApp()
    {
        return new BaseApp();
    }
}