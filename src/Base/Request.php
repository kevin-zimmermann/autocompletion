<?php


namespace Base;


class Request
{
    /**
     * @var InputFilterer
     */
    protected $filterer;

    protected $input;
    protected $files;
    protected $cookie;
    protected $server;
    protected $session;
    protected static $customMethodPhpInput = null;
    public function __construct(InputFilterer $filterer, array $input = null, array $files = null, array $cookie = null, array $server = null, $session = null)
    {
        $this->filterer = $filterer;
        if ($input === null)
        {
            if (self::$customMethodPhpInput === null)
            {
                self::$customMethodPhpInput = $this->convertCustomMethodPhpInput();
            }

            $input = self::$customMethodPhpInput + $_POST + $_GET;
        }
        if ($files === null)
        {
            $files = $_FILES;
        }
        if ($cookie === null)
        {
            $cookie = $_COOKIE;
        }
        if ($server === null)
        {
            $server = $_SERVER;
        }
        if ($session === null)
        {
            $session = $_SESSION;
        }

        $this->input = $input;
        $this->files = $files;
        $this->cookie = $cookie;
        $this->server = $server;
        $this->session = $session;
    }

    /**
     * @return array
     */
    protected function convertCustomMethodPhpInput()
    {
        if (!empty($_SERVER['REQUEST_METHOD'])
            && in_array(strtoupper($_SERVER['REQUEST_METHOD']), ['PUT', 'PATCH', 'DELETE'])
            && !empty($_SERVER['CONTENT_TYPE'])
            && $_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded'
        )
        {
            $rawInput = @file_get_contents("php://input");
            if ($rawInput)
            {
                parse_str($rawInput, $extra);
                if (is_array($extra))
                {
                    return $extra;
                }
            }
        }

        return [];
    }
    /**
     * @param $key
     * @param bool $fallback
     * @return bool|mixed
     */
    public function get($key, $fallback = false)
    {
        $subParts = explode('.', $key);
        $key = array_shift($subParts);
        if (array_key_exists($key, $this->input))
        {
            $value = $this->input[$key];
        }
        else
        {
            return $fallback;
        }

        return $this->getSubValue($value, $subParts, $fallback);
    }

    /**
     * @param $value
     * @param array $subParts
     * @param $fallback
     * @return mixed
     */
    protected function getSubValue($value, array $subParts, $fallback)
    {
        while ($subParts)
        {
            if (!is_array($value))
            {
                return $fallback;
            }

            $key = array_shift($subParts);
            if (array_key_exists($key, $value))
            {
                $value = $value[$key];
            }
            else
            {
                return $fallback;
            }
        }

        return $value;
    }

    /**
     * @param $key
     * @param null $type
     * @param null $default
     * @return array|bool|\DateTime|false|float|int|string|string[]|null
     */
    public function filter($key, $type = null, $default = null)
    {
        if (is_array($key) && $type === null)
        {
            $output = [];
            foreach ($key AS $name => $value)
            {
                if (is_array($value))
                {
                    $array = $this->get($name);
                    if (!is_array($array))
                    {
                        $array = [];
                    }
                    $output[$name] = $this->filterer->filterArray($array, $value);
                }
                else
                {
                    $output[$name] = $this->filter($name, $value);
                }
            }

            return $output;
        }
        else
        {
            $value = $this->get($key, $default);

            if (is_string($type) && $type[0] == '?')
            {
                if ($value === null)
                {
                    return null;
                }

                $type = substr($type, 1);
            }

            if (is_array($type))
            {
                if (!is_array($value))
                {
                    $value = [];
                }

                return $this->filterer->filterArray($value, $type);
            }
            else
            {
                return $this->filterer->filter($value, $type);
            }
        }
    }

    /**
     * @param $key
     * @param bool $fallback
     * @return bool|mixed
     */
    public function getCookieRaw($key, $fallback = false)
    {
        if (array_key_exists($key, $this->cookie))
        {
            return $this->cookie[$key];
        }
        else
        {
            return $fallback;
        }
    }
    /**
     * @param $key
     * @param bool $fallback
     * @return bool|mixed
     */
    public function getSessionRaw($key, $fallback = false)
    {
        if (array_key_exists($key, $this->cookie))
        {
            return $this->session[$key];
        }
        else
        {
            return $fallback;
        }
    }

    /**
     * @param $nameSession
     * @param $value
     */
    public function setSession($nameSession, $value)
    {
        $_SESSION[$nameSession] = $value;
        $this->session[$nameSession] = $value;
    }

    /**
     * @return string
     */
    public function getRoutePath()
    {
        $routePath = ltrim($this->getExtendedUrl(), '/');
        return $this->getRoutePathInternal($routePath);
    }

    /**
     * @param null $requestUri
     * @return bool|mixed|string|null
     */
    public function getExtendedUrl($requestUri = null)
    {
        $baseUrl = $this->getBaseUrl();
        $basePath = $this->getBasePath();
        if ($requestUri === null)
        {
            $requestUri = $this->getRequestUri();
        }

        if (strpos($requestUri, $baseUrl) === 0)
        {
            return strval(substr($requestUri, strlen($baseUrl)));
        }
        else if (strpos($requestUri, $basePath) === 0)
        {
            return strval(substr($requestUri, strlen($basePath)));
        }
        else
        {
            return $requestUri;
        }
    }

    /**
     * @param $routePath
     * @return string
     */
    protected function getRoutePathInternal($routePath)
    {
        if (strlen($routePath) == 0)
        {
            return '';
        }

        if ($routePath[0] == '?')
        {
            $routePath = substr($routePath, 1);

            $nextArg = strpos($routePath, '&');
            if ($nextArg !== false)
            {
                $routePath = substr($routePath, 0, $nextArg);
            }

            if (strpos($routePath, '=') !== false)
            {
                return ''; // first bit has a "=" so it's named
            }
        }
        else
        {
            $queryStart = strpos($routePath, '?');
            if ($queryStart !== false)
            {
                $routePath = substr($routePath, 0, $queryStart);
            }
        }

        return strval($routePath);
    }
    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return strtolower($this->getServer('REQUEST_METHOD'));
    }
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
     * @return bool|false|mixed|string
     */
    public function getBaseUrl()
    {
        $baseUrl = $this->getServer('SCRIPT_NAME', '');
        $basePath = dirname($baseUrl);

        if (strlen($basePath) <= 1)
        {
            return $baseUrl;
        }

        $requestUri = $this->getRequestUri();
        if (!strlen($requestUri))
        {
            return '/';
        }

        if (strpos($requestUri, $basePath) === 0)
        {
            return $baseUrl;
        }
        $qsPos = strpos($requestUri, '?');
        if ($qsPos !== false)
        {
            $requestUriNoQs = substr($requestUri, 0, $qsPos);
        }
        else
        {
            $requestUriNoQs = $requestUri;
        }

        $requestPos = strpos($baseUrl, $requestUriNoQs);
        if ($requestPos)
        {
            $realBaseUrl = substr($baseUrl, $requestPos);
            if ($realBaseUrl)
            {
                return $realBaseUrl;
            }
        }

        return $baseUrl;
    }
    /**
     * @return bool|mixed+
     */
    public function getRequestUri()
    {
        if ($this->getServer('IIS_WasUrlRewritten') === '1')
        {
            $unencodedUrl = $this->getServer('UNENCODED_URL', '');
            if ($unencodedUrl !== '')
            {
                return $unencodedUrl;
            }
        }

        return $this->getServer('REQUEST_URI', '');
    }
    /**
     * @return false|string
     */
    public function getBasePath()
    {
        $baseUrl = $this->getBaseUrl();

        if (is_string($baseUrl) && strlen($baseUrl))
        {
            $lastSlash = strrpos($baseUrl, '/');
            if ($lastSlash) // intentionally skipping for false and 0
            {
                return substr($baseUrl, 0, $lastSlash);
            }
        }

        return '/';
    }

    /**
     * @return array|false|int|string|null
     */
    public function getServerNameUrl()
    {
        return $this->getHttpOrHttps() . parse_url($this->getServer('HTTP_REFERER'), PHP_URL_HOST);
    }
    public function getHttpOrHttps()
    {
        if ($this->getServer('HTTP_REFERER'))
        {
            if ($this->getServer('HTTPS') == 'on')
            {
                return 'https://';
            }
            else
            {
                return 'http://';
            }
        }
        else
        {
            return 'http://';

        }
    }
    /**
     * @return bool
     */
    public function isXhr()
    {
        return ($this->getServer('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
    }
}