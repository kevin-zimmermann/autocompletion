<?php


namespace Base\Util;


use Base\App;

class PageNave
{
    /**
     * @var App|null
     */
    protected  $app = null;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $context = 'index';

    protected $phraseMorePage = ['start' => '...', 'end' => '...'];

    /**
     * PageNave constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->type = $app->getType();
        $this->context = $this->type == 'Pub' ? $this->type : strtolower($this->type);
    }

    /**
     * @param $phraseMorePage
     * @return $this
     */
    public function setPhraseMorePage($phraseMorePage = [])
    {
        $this->phraseMorePage = array_merge($this->phraseMorePage ,$phraseMorePage);
        return $this;
    }
    /**
     * @param array $options
     * @return string
     */
    public function page(array $options)
    {
        $config = array_merge([
            'pageParam' => 'page',

            'page' => 0,
            'perPage' => 0,
            'total' => 0,
            'range' => 2,

            'link' => '',
            'data' => null,
            'params' => [],

            'hashtag' => ''
        ], $options);

        $hashtag = '';
        if(!empty($config['hashtag']))
        {
            $hashtag = '#' . $config['hashtag'];
        }
        $perPage = intval($config['perPage']);
        if ($perPage <= 0)
        {
            return '';
        }

        $total = intval($config['total']);
        if ($total <= $perPage)
        {
            return '';
        }
        $totalPages = ceil($total / $perPage);
        $current = intval($config['page']);
        $current = max(1, min($current, $totalPages));
        $range = intval(2);

        $startInner = max(2, $current - $range);
        $endInner = min($current + $range, $totalPages - 1);
        if ($startInner <= $endInner)
        {
            $innerPages = range($startInner, $endInner);
        }
        else
        {
            $innerPages = [];
        }

        $hasSkipStart = ($startInner > 2);
        $hasSkipEnd = ($endInner + 1 < $totalPages);
        $prev = false;
        $outputHtml = "<nav class='pageNav-inner'><div class=\"pageNav\">";
        $app = $this->app;
        if ($current > 1)
        {
            $prevPageParam = $current - 1;
            if ($prevPageParam <= 1)
            {
                $prevPageParam = null;
            }
            $prev = $app->buildLink($this->context . ':' . $config['link'], $config['data'], $config['params'] + [$config['pageParam'] => $prevPageParam]) . $hashtag;
        }
        $next = false;
        if ($current < $totalPages)
        {
            $next = $app->buildLink($this->context . ':' . $config['link'], $config['data'], $config['params'] + [$config['pageParam'] => $current + 1]) . $hashtag;
        }
        if($prev)
        {
            $outputHtml .= $this->buttonNextPrev($prev, \Base\BaseApp::phrase('prev'));
        }
        $outputHtml .= "<ul class=\"pageNav-main\">";
        $outputHtml .= $this->renderHtmlButtonNumber($config, $innerPages, $totalPages, $hasSkipStart, $hasSkipEnd, $current);
        $outputHtml .= "</ul>";
        if($next)
        {
            $outputHtml .= $this->buttonNextPrev($next, \Base\BaseApp::phrase('next'));
        }
        $outputHtml .= "</div>";

        $outputHtml .= "<div class='pageNav-simple'>";
        if($current > 1)
        {
            $outputHtml .= $this->buttonFirstLast($config['link'], $config['data'], $config['params'], 1, 'first', $hashtag);
        }
        if($prev)
        {
            $outputHtml .= $this->buttonNextPrev($prev, \Base\BaseApp::phrase('prev'));
        }
        $outputHtml .= $this->renderHtmlSimple($current, $totalPages);
        if($next)
        {
            $outputHtml .= $this->buttonNextPrev($next, \Base\BaseApp::phrase('next'));
        }
        if($current < $totalPages)
        {
            $outputHtml .= $this->buttonFirstLast($config['link'], $config['data'], $config['params'], $totalPages, 'last', $hashtag);
        }
        $outputHtml .= "</div>";
        return $outputHtml . "</nav>";
    }

    /**
     * @param array $config
     * @param $innerPages
     * @param $totalPages
     * @param $hasSkipStart
     * @param $hasSkipEnd
     * @param $current
     * @return string
     */
    protected function renderHtmlButtonNumber(array $config, $innerPages, $totalPages, $hasSkipStart, $hasSkipEnd, $current)
    {
        $hashtag = '';
        if(!empty($config['hashtag']))
        {
            $hashtag = '#' . $config['hashtag'];
        }

        $output = $this->PageLink(1, $config['link'], $config['data'], $current, $config['params'], $hashtag);
        if($hasSkipStart)
        {
            $output .= $this->morePage('start');
        }
        foreach ($innerPages as $page)
        {
            $output .= $this->PageLink($page, $config['link'], $config['data'], $current, $config['params'], $hashtag);
        }
        if($hasSkipEnd)
        {
            $output .= $this->morePage('end');
        }
        $output .= $this->PageLink($totalPages, $config['link'], $config['data'], $current, $config['params'], $hashtag);
        return $output;
    }

    /**
     * @param $page
     * @param $link
     * @param $data
     * @param $current
     * @param $params
     * @param $hashtag
     * @return string
     */
    protected function PageLink($page, $link, $data, $current, $params, $hashtag)
    {
        $pageLink = $this->app->buildLink($this->context . ':' . $link, $data, $params + ['page' => $page > 1 ? $page : null]) . $hashtag;
        $class = $current == $page ? 'pageNav-page--current' : '';
        return "<li class=\"pageNav-page $class  \"><a href=\"$pageLink\">{$page}</a></li>";
    }

    /**
     * @param $type
     * @return string
     */
    protected function morePage($type)
    {
        $phrase = $this->phraseMorePage[$type];
        return "<li class=\"pageNav-page pageNave-empty \">$phrase</li>";
    }

    /**
     * @param $link
     * @param $type
     * @param string $phrase
     * @return string
     */
    protected function buttonNextPrev($link, $type)
    {
        $phrase = ucfirst($type);
        return "<a href=\"$link\" class=\"pageNav-jump pageNav-jump--$type\">$phrase</a>";
    }

    /**
     * @param $current
     * @param $last
     * @return string
     */
    protected function renderHtmlSimple($current, $last)
    {
        return "<a class=\"pageNavSimple pageNavSimple-current\" >$current de $last</a>";
    }

    /**
     * @param $link
     * @param $data
     * @param $params
     * @param $page
     * @param $type
     * @param $hashtag
     * @return string
     */
    protected function buttonFirstLast($link, $data, $params, $page, $type, $hashtag)
    {
        $link = $this->app->buildLink($this->context . ':' . $link, $data, $params + ['page' => $page > 1 ? $page : null]) . $hashtag;
        $phraseType = ucfirst($type);
        return "<a href=\"$link\" class=\"pageNavSimple pageNaveSimple-first-last pageNavSimple-$type\" ><i aria-hidden=\"true\"></i> <span class=\"u-srOnly\">$phraseType</span></a>";
    }
}