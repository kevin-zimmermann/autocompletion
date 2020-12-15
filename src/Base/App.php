<?php

namespace Base;

use Base\BaseApp;
use Base\Router\Router;
use Base\Util\breadcrumbs;

class App
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $globalRoute = [];

    /**
     * App constructor.
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->router = new Router($this);
        $this->setRouters();
    }

    /**
     *
     */
    protected function setRouters()
    {
        $this->globalRoute = [
            'Admin' => [
                'DefaultRouter' => [
                    '' => [
                        'controller' => 'App:Home',
                        'context' => 'home',
                        'title' => '<!--TITLE-->',
                        'description' => false
                    ]
                ],
            ],
            'Pub' => [
                'DefaultRouter' => [
                    '' => [
                        'controller' => 'App:Home',
                        'context' => 'home',
                        'title' => '<!--TITLE-->',
                        'description' => true,
                        'RouteFormat' => null,
                    ]
                ],
                'operator' =>[
                    '' => [
                        'controller' => 'App:Operator',
                        'context' => 'operator',
                        'title' => '<!--TITLE-->',
                        'description' => true,
                        'RouteFormat' => ':int<id>/'
                    ]
                ]
            ]
        ];
    }

    /**
     * @return mixed|null
     */
    public function currentRoute()
    {
        return $this->router->currentRoute($this->getRoutersByType());
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getRouters()
    {
        return $this->globalRoute;
    }

    /**
     * @return mixed
     */
    public function getRoutersByType()
    {
        return $this->globalRoute[$this->type];
    }

    /**
     * @param array $routes
     */
    public function addRouter(array $routes = [])
    {
        foreach ($routes as $prefix => $route)
        {
            foreach ($route as $subName => $router)
            {
                $setRouter = $this->router->get($prefix, $router['controller'], $this->type);

                if (isset($router['forceAction']))
                {
                    $setRouter->forceAction($router['forceAction']);
                }
                if (isset($subName))
                {
                    $setRouter->subName($subName);
                }
                if (isset($router['RouteFormat']))
                {
                    $setRouter->RouteFormat($router['RouteFormat']);
                }
                if ($router['context'])
                {
                    $setRouter->context($router['context']);
                }
                if (isset($router['newPage']))
                {
                    $setRouter->newPage($router['newPage']);
                }
                if (isset($router['ajaxJason']))
                {
                    $setRouter->ajaxJason($router['ajaxJason']);
                }
                if (isset($router['title']))
                {
                    $setRouter->setTitle($router['title']);
                }
                if (isset($router['description']))
                {
                    $setRouter->setDescription($router['description']);
                }
                if (isset($router['parent']))
                {
                    $setRouter->setParent($router['parent']);
                }
            }
        }
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {

        $pageContents = ob_get_contents();
        ob_end_clean();
        if ($title == null)
        {
            $ConfigOptions = BaseApp::getConfigOptions();
            $title = $ConfigOptions->defaultNameSite;
        }

        echo str_replace('<!--TITLE-->', $title, $pageContents);
    }

    /**
     * @param breadcrumbs|null $breadcrumb
     * @return null
     * @throws \Base\Router\RouterException
     */
    public function runRouter(breadcrumbs $breadcrumb = null)
    {
        return $this->router->run($breadcrumb);
    }

    /**
     * @param string $code
     * @param string $controller
     * @param string $action
     */
    public function methodNotFound($code = "", $controller = "", $action = "")
    {
        include "src/Template/" . $this->type . "/methodNotFound.php";
        $this->setTitle('Oops! Nous avons rencontré des problèmes.');
    }

    /**
     * @param $link
     * @param null $data
     * @param array $parameters
     * @return mixed|string
     */
    public function buildLink($link, $data = null, array $parameters = [])
    {
        return $this->router->buildLink($link, $data, $parameters);
    }

    public function getBaseLink()
    {
        $base = BaseApp::request()->getBaseUrl();
        $base = preg_replace('#(index|admin)\.php#', '', $base);
        return $base;
    }
}