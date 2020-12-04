<?php


namespace Base\Util;


class Parser
{
    /**
     * @var array
     */
    protected $BbCodes = [
        'right' => [
            'BbCodes' => 'RIGHT',
            'replace' => '<div style="text-align:right;">$2</div>',
            'edit' => '<p style="text-align:right;">$2</p>',
            'editable' => true,
        ],
        'center' => [
            'BbCodes' => 'CENTER',
            'replace' => '<div style="text-align:center;">$2</div>',
            'edit' => '<p style="text-align:center;">$2</p>',
            'editable' => true,
        ],
        'left' => [
            'BbCodes' => 'LEFT',
            'replace' => '<div style="text-align:left;">$2</div>',
            'edit' => '<p style="text-align:left;">$2</p>',
            'editable' => true,
        ],
        'link' => [
            'BbCodes' => 'URL',
            'replace' => '<a href="$2" target="_blank">$3</a>',
            'params' => true,
            'editable' => true,
        ],

        'color' => [
            'BbCodes' => 'COLOR',
            'replace' => '<span style="color:$2;">$3</span>',
            'edit' => '<font color="$2">$3</font>',
            'params' => true,
            'editable' => true,
        ],
        'fa' => [
            'BbCodes' => 'FA',
            'replace' => '<i class="$2"></i>',
            'editable' => false,
        ],
        'size' => [
            'BbCodes' => 'SIZE',
            'replace' => '<span style="font-size: $2px;">$3</span>',
            'edit' => '<font style="font-size:$2px;">$3</font>',
            'params' => true,
            'editable' => true,
        ],

    ];
    protected $message;
    /**
     * @var bool
     */
    protected $parserEdit = false;
    public function __construct($message, $edit = false)
    {
        $this->parserEdit = $edit;
        $this->message = $message;
    }

    /**
     * @param $message
     * @return string|string[]|null
     */
    public function parser()
    {
        $message = $this->message;
        $message = preg_replace('/^\s+/', '', $message);

        if($this->parserEdit)
        {
            $message = '<p>' . preg_replace('/[\r\n]+/', '<br /></p><p>', $message);
            $tags = 'b|i|u|s|strong|em|strike';
            $tags .= '|' . strtoupper($tags);
            $message = preg_replace('#\[(' . $tags . ')\]([^[]*)#', '<$1>$2', $message);
            $message = preg_replace('#\[/(' . $tags . ')\]#', '</$1>', $message);
        }
        else
        {
            $message = preg_replace('/[\r\n]+/', '<br />', $message);
            $message = nl2br($message);
            $tags = 'b|i|u|s|strong|em|strike';
            $tags .= '|' . strtoupper($tags);
            $message = preg_replace('#\[(' . $tags . ')\]([^[]*)#', '<$1>$2', $message);
            $message = preg_replace('#\[/(' . $tags . ')\]#', '</$1>', $message);
        }


        foreach ($this->BbCodes as  $key => $bbCode)
        {
            $BBCodeType = $bbCode['BbCodes'] . '|' . strtolower($bbCode['BbCodes']);

            if(($this->parserEdit && $bbCode['editable']) || !$this->parserEdit)
            {
                if(isset($bbCode['params']))
                {
                    $preg = '#\[(' . $BBCodeType .')=([^]]*)\](.*?)\[/(' . $BBCodeType .')\]#';
                }
                else
                {
                    $preg = '#\[(' . $BBCodeType .')\](.*?)\[/(' . $BBCodeType .')\]#';
                }

                if(!$this->parserEdit)
                {
                    $message = preg_replace($preg, $bbCode['replace'], $message);
                }
                else
                {
                    $BBCodeTest = $bbCode['BbCodes'];
                    $message = preg_replace('/\s+(\[\/' . $BBCodeTest . '\])/', '$1', $message);
                    $message = preg_replace('#<(br|p)>|<(/p)>(\[\/' . $BBCodeTest . '])#', '$3', $message);
//                    preg_replace_callback($preg, [$this, 'test'], $message);
                    $message = preg_replace($preg, isset($bbCode['edit']) ? $bbCode['edit'] : $bbCode['replace'], $message);
                }
            }
        }
        $message = preg_replace("#<(span)(.style=\"(.*?)\")>(.*)<(span)>#", '$1',$message);
        if(!$this->parserEdit)
        {
            $message = preg_replace('#<(/div)>(.*?)<(br)>#', '</div>', $message);
            $message = preg_replace('#<(/div)><(/span)><(br)>#si', '</$1></$2>', $message);
        }
        return $message;
    }
//    protected function test($render)
//    {
//        var_dump($render);
//    }
}