<?php
namespace Base\Router;

use Base\App;
use Base\BaseApp;
use Base\Util\breadcrumbs;

class Router
{
    /**
     * @var array|bool|\DateTime|false|float|int|string|string[]
     */
    protected $url;

    /**
     * @var array|Route
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected $namedRoutes = [];

    /**
     * @var App
     */
    protected $app;

    /**
     * @var bool
     */
    protected $includeTitleInUrls = true;

    /**
     * @var array
     */
    protected $stringCache = [];

    /**
     * @var bool
     */
    protected $romanizeUrls = false;

    /**
     * @var string
     */
    protected $type = "Pub";

    /**
     * Router constructor.
     * @param $app
     */
    public function __construct(App $app)
    {
        $this->url = $this->BaseApp()->request()->getRoutePath();

        $this->app = $app;
    }

    /**
     * @param $path
     * @param $controller
     * @param string $type
     * @return Route
     */
    public function get($path, $controller, $type = "Pub")
    {
        $route = new Route($this->app, $path, $controller, $type);
        $this->type = $type;
        $this->namedRoutes[$path] = $route;
        $this->routes[] = $route;
        return $route;
    }

    /**
     * @param $router
     * @return mixed|null
     */
    public function currentRoute($router)
    {
        $currentRoute = null;
        if(empty($this->routes))
        {
            $this->app->addRouter($router);
        }
        if(isset($this->routes))
        {
            /** @var Route $route */
            foreach($this->routes as $route)
            {
                if(!empty($this->url))
                {
                    if($route->match($this->url) && $currentRoute == null)
                    {
                        if($route->match($this->url, true) == 'empty' && empty($route->getSubName()))
                        {
                            $currentRoute = $route;
                        }
                        else if ($route->match($this->url, true) !== 'empty')
                        {
                            $currentRoute = $route;
                        }
                    }
                }
                elseif($route->getPath() == "DefaultRouter")
                {
                    $currentRoute = $route;
                }
            }

        }
        return $currentRoute;
    }

    /**
     * @param breadcrumbs|null $breadcrumb
     * @return mixed|null
     * @throws RouterException
     */
    public function run(breadcrumbs $breadcrumb = null){
        if(!isset($this->routes))
        {
            throw new RouterException('REQUEST_METHOD does not exist');
        }
        /** @var Route $route */
        foreach ($this->routes as $route)
        {
            if(!empty($this->url))
            {
                if ($route->match($this->url))
                {
                    return $route->call($breadcrumb);
                }
            }
            elseif($route->getPath() == "DefaultRouter")
            {
                return $route->call($breadcrumb);
            }
        }
        if(!empty($this->url))
        {
            $this->app->methodNotFound('invalid_route', '-', '-');
        }
        return null;
    }

    /**
     * @return BaseApp
     */
    public function BaseApp()
    {
        return new BaseApp();
    }

    /**
     * @param $link
     * @param null $data
     * @param array $parameters
     * @return mixed|string
     */
    public function buildLink($link, $data = null, array $parameters = [])
    {
        if (is_array($link))
        {
            $tempLink = $link;
            $link = $tempLink[0];
            if (!$parameters)
            {
                $parameters = $tempLink[1];
            }
        }


        $parts = explode(':', $link);
        if (isset($parts[1]))
        {
            $type = $parts[0];
            $link = $parts[1];
        }
        else
        {
            $type = null;
        }

        return $this->buildFinalUrl(
            $type,
            $this->buildLinkPath($link, $data, $parameters),
            $parameters
        );
    }

    /**
     * @param $type
     * @param $routeUrl
     * @param array $parameters
     * @return mixed|string
     */
    public function buildFinalUrl($type, $routeUrl, array $parameters = [])
    {
        $queryString = $parameters ? $this->buildQueryString($parameters) : '';
        if ($routeUrl instanceof RouteBuiltLink)
        {
            $url = $routeUrl->getFinalLink($this, $type, $queryString);
        }
        else
        {
            $url = call_user_func($this->formatter($type), $routeUrl, $queryString);
            $url = $this->applyPather($url, $type);
        }

        return $url;
    }

    /**
     * @return string
     */
    protected function getPather()
    {
        return rtrim($this->BaseApp()->request()->getBasePath(), '/') . '/';
    }

    /**
     * @return \Closure
     */
    protected function setPatcher()
    {
        return function($url)
        {
            $url = $this->getPather() . $url;

            return $url;
        };
    }

    /**
     * @param $url
     * @param string $modifier
     * @return mixed|string
     */
    public function applyPather($url, $modifier = '')
    {
        if ($this->setPatcher())
        {
            $pather = $this->setPatcher();
            $url = $pather($url, $modifier);
        }

        if ($url === '')
        {
            $url = '.';
        }

        return $url;
    }

    /**
     * @param $link
     * @param null $data
     * @param array $parameters
     * @return string
     */
    public function buildLinkPath($link, $data = null, array &$parameters = [])
    {
        if (!$link || $link == 'index')
        {
            return '';
        }
        $app = $this->app;

        $parts = explode('/', $link, 2);
        $prefix = $parts[0];
        if (!isset($app->getRoutersByType()[$prefix]))
        {
            return $link;
        }
        $this->manipulateLinkPathInternal($prefix, $parts[1], $data, $parameters);

        $sections = isset($parts[1]) ? explode('/', $parts[1]) : [''];
        $action = '';
        $prefixRoutes = $app->getRoutersByType()[$prefix];
        for ($totalSections = count($sections), $i = $totalSections; $i > 0; $i--)
        {
            $possibleSection = implode('/', array_slice($sections, 0, $i));

            if (isset($prefixRoutes[$possibleSection]))
            {
                return $this->buildRouteUrl(
                    $prefix, $prefixRoutes[$possibleSection], $action, $data, $parameters
                );
            }

            if ($i == $totalSections)
            {
                $action = $sections[$i - 1];
            }
            else
            {
                $action = $sections[$i - 1] . '/' . $action;
            }
        }
        if (isset($prefixRoutes['']))
        {
            return $this->buildRouteUrl(
                $prefix, $prefixRoutes[''], $action, $data, $parameters
            );
        }

        return $link;
    }

    /**
     * @param array $elements
     * @param string $prefix
     * @return string
     */
    public function buildQueryString(array $elements, $prefix = '')
    {
        $output = [];

        foreach ($elements AS $name => $value)
        {
            if (is_array($value))
            {
                if (!$value)
                {
                    continue;
                }

                $encodedName = ($prefix ? $prefix . '[' . urlencode($name) . ']' : urlencode($name));
                $childOutput = $this->buildQueryString($value, $encodedName);
                if ($childOutput !== '')
                {
                    $output[] = $childOutput;
                }
            }
            else
            {
                if ($value === null || $value === false || $value === '')
                {
                    continue;
                }

                $value = strval($value);

                if ($prefix)
                {
                    $output[] = $prefix . '[' . urlencode($name) . ']=' . urlencode($value);
                }
                else
                {
                    $output[] = urlencode($name) . '=' . urlencode($value);
                }
            }
        }

        return implode('&', $output);
    }

    /**
     * @param $prefix
     * @param $path
     * @param $data
     * @param array $parameters
     */
    protected function manipulateLinkPathInternal($prefix, &$path, &$data, array &$parameters) {}

    /**
     * @param $prefix
     * @param array $route
     * @param $action
     * @param null $data
     * @param array $parameters
     * @return string
     */
    protected function buildRouteUrl($prefix, array $route, $action, $data = null, array &$parameters = [])
    {
        $url = $route['RouteFormat'];

        $url = preg_replace_callback(
            '#:(?:\+)?int(_p)?<([a-zA-Z0-9_]+)(?:,([a-zA-Z0-9_]+))?>(/?)#',
            function($match) use ($data, &$parameters)
            {
                $inParams = !empty($match[1]);
                $idKey = $match[2];
                $stringKey = $match[3];
                $trailingSlash = $match[4];

                $search = $inParams ? $parameters : $data;
                if ($search && $search[$idKey] !== null)
                {
                    $idValue = intval($search[$idKey]);

                    if ($inParams)
                    {
                        unset($parameters[$idKey]);
                    }

                    if ($stringKey &&  $search[$stringKey] !== null)
                    {
                        $string = strval($search[$stringKey]);

                        if ($inParams)
                        {
                            unset($parameters[$stringKey]);
                        }

                        if ($this->includeTitleInUrls)
                        {
                            $string = $this->prepareStringForUrl($string);
                            if (strlen($string))
                            {
                                return $string . "." . $idValue . $trailingSlash;
                            }
                        }
                    }

                    return $idValue . $trailingSlash;
                }

                return '';
            },
            $url
        );
        $url = preg_replace_callback(
            '#:(?:\+)?int_int<([a-zA-Z0-9_]+),([a-zA-Z0-9_]+)(?:,([a-zA-Z0-9_]+))?>(/?)#',
            function($match) use ($data, $action)
            {

                $stringKey = $match[1];
                $intKey = $match[1];
                $intKey2 = $match[2];
                $trailingSlash = $match[4];

                if ($data === '-')
                {
                    return '-' . $trailingSlash;
                }

                if ($data && isset($data[$stringKey]))
                {
                    $key = strval($data[$stringKey]);
                    if (strlen($key))
                    {
                        return $key . $trailingSlash;
                    }
                }

                if ($data && $data[$intKey] !== null)
                {
                    $idValue = intval($data[$intKey]);
                    if ($intKey2 && $data[$intKey2] !== null && $this->includeTitleInUrls)
                    {
                        $int = strval($data[$intKey2]);
                        if (strlen($int))
                        {
                            return $int . "." . $idValue . $trailingSlash;
                        }
                    }

                    return $idValue . $trailingSlash;
                }

                return strlen($action) ? '-' . $trailingSlash : '';
            },
            $url
        );
        $url = preg_replace_callback(
            '#:(?:\+)?str(_p)?<([a-zA-Z0-9_]+)>(/?)#',
            function($match) use ($data, &$parameters)
            {
                $inParams = !empty($match[1]);
                $stringKey = $match[2];
                $trailingSlash = $match[3];

                $search = $inParams ? $parameters : $data;

                if ($search && $search[$stringKey] !== null)
                {
                    $key = strval($search[$stringKey]);

                    if ($inParams)
                    {
                        unset($parameters[$stringKey]);
                    }

                    if (strlen($key))
                    {
                        return $key . $trailingSlash;
                    }
                }

                return '';
            },
            $url
        );
        $url = preg_replace_callback(
            '#:page(<([a-zA-Z0-9_]+)>)?(/?)#',
            function($match) use ($data, &$parameters)
            {
                $pageKey = !empty($match[2]) ? $match[2] : 'page';
                $trailingSlash = $match[3];

                if (isset($parameters[$pageKey]))
                {
                    $page = $parameters[$pageKey];
                    unset($parameters[$pageKey]);
                    if ($page === '%page%')
                    {
                        return "page-%page%$trailingSlash";
                    }
                    else
                    {
                        $page = intval($page);
                        if ($page > 1)
                        {
                            return "page-$page$trailingSlash";
                        }
                    }
                }

                return '';
            },
            $url
        );
        $url = preg_replace_callback(
            '#:(?:\+)?any<([a-zA-Z0-9_]+)>(/?)#',
            function($match) use ($data, &$parameters)
            {
                $stringKey = $match[1];
                $trailingSlash = $match[2];

                if ($data && $data[$stringKey] !== null)
                {
                    $key = strval($data[$stringKey]);

                    if (strlen($key))
                    {
                        return $key . $trailingSlash;
                    }
                }

                return '';
            },
            $url
        );

        $url = str_replace('?', '', $url);

        if ($url && $action)
        {
            if (substr($url, -1) != '/')
            {
                $url .= '/';
            }
            $url .= $action;
        }

        else if ($action)
        {
            $url = $action;
        }
        $routeUrl = $prefix . '/' . $url;
        return $routeUrl;
    }

    /**
     * @param $string
     * @param null $romanizeOverride
     * @return mixed|string
     */
    public function prepareStringForUrl($string, $romanizeOverride = null)
    {
        $string = strval($string);
        $romanize = $romanizeOverride === null ? $this->romanizeUrls : (bool)$romanizeOverride;
        $cacheKey = $string . ($romanize ? '|r' : '');

        if (isset($this->stringCache[$cacheKey]))
        {
            return $this->stringCache[$cacheKey];
        }

        if ($romanize)
        {
            $string = utf8_romanize(utf8_deaccent($string));

            $originalString = $string;

            $string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
            if (!$string)
            {
                $string = $originalString;
            }
        }

        $string = strtr(
            $string,
            '`!"$%^&*()-+={}[]<>;:@#~,./?|' . "\r\n\t\\",
            '                             ' . '    '
        );
        $string = strtr($string, ['"' => '', "'" => '']);

        if ($romanize)
        {
            $string = preg_replace('/[^a-zA-Z0-9_ -]/', '', $string);
        }

        $string = preg_replace('/[ ]+/', '-', trim($string));
        $string = strtr($string, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
        $string = urlencode($string);

        $this->stringCache[$cacheKey] = $string;

        return $string;
    }

    /**
     * @param string $type
     * @return \Closure
     */
    protected function formatter($type = 'Pub')
    {
        if($type == 'Pub')
        {
            if (BaseApp::getConfigOptions()->useFriendlyUrls)
            {
                return function($route, $queryString)
                {
                    return $route . (strlen($queryString) ? '?' . $queryString : '');
                };

            }
            else
            {
                return function($route, $queryString)
                {
                    $suffix = $route . (strlen($queryString) ? (strlen($route) ? '&' : '') . $queryString : '');
                    return strlen($suffix) ? 'index.php?' . $suffix : 'index.php';
                };
            }
        }
        else
        {
            return function($route, $queryString) use ($type)
            {
                $suffix = $route . (strlen($queryString) ? '&' . $queryString : '');
                return strlen($suffix) ? $type . '.php?' . $suffix : $type . '.php';
            };
        }
    }
}