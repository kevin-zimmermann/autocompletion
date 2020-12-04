<?php


namespace Base\Util;

use Base\App;
use Base\Router\Route;

/**
 * Class breadcrumbs
 * @package Base\Util
 */
class breadcrumbs
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var Route|null
     */
    protected $route;

    /**
     * @var array
     */
    protected $sources = [];

    /**
     * @var array
     */
    protected $class = [];

    /**
     * @var array
     */
    protected $parentClass = [];

    /**
     * breadcrumbs constructor.
     * @param App $app
     * @param Route|null $route
     */
    public function __construct(App $app, Route $route = null)
    {
        $this->app = $app;
        $this->route = $route;
    }

    /**
     * @return string|null
     */
    public  function getBreadcrumbBase()
    {
        $parent = $this->route->getParent();
        if(!empty($parent))
        {
            return $this->renderHtml($parent['label'], $this->app->buildLink($parent['href']));
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isBreadcrumb()
    {
        return !empty($this->getBreadcrumb());
    }

    /**
     * @param $sources
     */
    public function setBreadcrumb($sources)
    {
        $this->sources = $sources;
    }

    /**
     * @param array $class
     * @return $this
     */
    public function addClass(array $class)
    {
        $this->class = array_merge($class, $this->class);
        return $this;
    }

    /**
     * @param array $class
     * @return $this
     */
    public function addParentClass(array $class)
    {
        $this->parentClass = array_merge($class, $this->parentClass);
        return $this;
    }

    /**
     * @return string
     */
    public function getParenClass()
    {
        return implode(' ', $this->parentClass);
    }

    /**
     * @return string
     */
    public function getParentClassInString()
    {
        return implode(' ', $this->parentClass);
    }

    /**
     * @return string|null
     */
    public function getBreadcrumb()
    {
        if(empty($this->sources))
        {
            return $this->getBreadcrumbBase();
        }
        else
        {
            $outPut = $this->getBreadcrumbBase();
            foreach ($this->sources as $source)
            {
                $outPut .= $this->renderHtml($source['label'], $source['href']);
            }
            return $outPut;
        }
    }

    /**
     * @param $label
     * @param $link
     * @return string
     */
    public function renderHtml($label, $link)
    {
        $class = implode(' ', $this->class);

        return "<li class='{$class}' itemprop=\"itemListElement\">
                    <a href=\"{$link}\">
                        <span >{$label}</span>
                    </a>
                </li> ";
    }
}