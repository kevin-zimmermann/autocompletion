<?php
namespace Base\Util\BBCode;

class Render
{
    protected $message;
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * @param $message
     * @return string|string[]|null
     */
    public function renderBbCode()
    {
        $message = $this->message;
        $message = html_entity_decode($message);
        $message = preg_replace('#<font.*?style="">(.*)</font>#', '$1', $message);
        $message = $this->divBBCode($message);
        $message = $this->urlBBCode($message);
        $message = $this->basicBBCode($message);
        $message = $this->fontBBCode($message);
        $message = $this->liBBCode($message);
        $message = $this->deleteTag($message);
        return $message;
    }
    protected function liBBCode($message)
    {
        return preg_replace('#(<li(.*?)>)(.*?)(<\/li>)#', "-$3\r\n", $message);
    }
    protected function urlBBCode($message)
    {
        $pattern = "#(\[(URL|url)=([^]]*)\]([^[]*)\[/(URL|url)\])|(?P<method>https?://|www)(?P<url>.([a-zA-Z0-9\-\/.]+))#";
        $message = preg_replace('#<(a)(.(href="(.*)"(.(class="(link-insert)"))))?>(.*)<(/a)>#siU', '[URL=$4]$8[/URL]', $message);
        $message = preg_replace_callback('#<(a)(.(href="(?P<url>.*)"))?>(.*)<(/a)>#', [__CLASS__, 'getUrl'], $message);
        $message = preg_replace_callback($pattern, [$this, 'getUrl'], $message);

        return $message;
    }
    protected function divBBCode($message)
    {
        $message = preg_replace_callback('#<(p)(.*(style="(.*)"))?>(.*)<(/p)>#siU', [$this, 'divChange'], $message);
        return $message;
    }
    protected function divChange($render)
    {
        if(!empty($render[4]))
        {
            $fontAlign = preg_replace('#(text-align:.*?)(.*?)(;)#', '$2', $render[4]);
            $fontAlign = preg_replace('/\s+/','',$fontAlign);

            $fontAlign = strtoupper($fontAlign);
            $return = "\r\n[" . $fontAlign . ']' . $render[5] . '[/' . $fontAlign .']';

            return $return;
        }
        else
        {
            $return = "\r\n" . $render[5];
            return $return;
        }
    }
    protected function basicBBCode($message)
    {
        $tags = 'b|i|u|s|strong|em|strike';

        $message = preg_replace('#<(' . $tags . ')(\s[^>]*)?>#i', '[$1]', $message);
        $message = preg_replace('#</(' . $tags . ')>#i', '[/$1]', $message);
        return $message;
    }
    protected function deleteTag($message)
    {
        $tags = 'span|br|font|pre|h1|h2|h3|h4|h5|h6|p|ul';
        $message = preg_replace('#<(' . $tags . ')(\s[^>]*)?>#i', '', $message);
        $message = preg_replace('#</(' . $tags . ')>#i', '', $message);
        return $message;
    }
    protected function fontBBCode($message)
    {
        $message = preg_replace_callback('#<(font)(.(style|color)=\"(.*?)\")?>(.*?)<(/font)>#', [$this, 'sizeChange'], $message);
        return $message;
    }
    protected function sizeChange($value)
    {

        if(preg_match('#<font.?(style|color)=\"(.*).*?\">.*#', $value[5], $match))
        {
            $output = "";
            if($value[3] == 'color')
            {
                $output .= '[COLOR=' . $value[4]. ']';
            }
            elseif ($value[3] == "style")
            {
                $px = preg_replace('#(font-size:(.*?))(.*)(px;)#', '$3', $value[4]);
                $output .= '[SIZE=' . $px . ']';
            }
            if($match[1] == "color")
            {
                $output .= '[COLOR=' . $match[2]. ']';
            }
            elseif ($match[1] == "style")
            {
                $px = preg_replace('#(font-size:(.*?))(.*)(px)#', '$3', $match[2]);
                $output .= '[SIZE=' . $px . ']';
            }
            $output .= $value[5];
            if($match[1] == "color")
            {
                $output .= '[/COLOR]';
            }
            elseif ($match[1] == "style")
            {
                $output .= '[/SIZE]';
            }
            if($value[3] == "color")
            {
                $output .= '[/COLOR]';
            }
            elseif ($value[3] == "style")
            {
                $output .= '[/SIZE]';
            }
            $output = preg_replace('#\s(.*);]#', '$1]', $output);
            return $output;
        }
        if(preg_match('#(.(style|color)=\"(.*?)\")(.(style|color)=\"(.*?)\")#si', $value[2], $match))
        {
            $output = "";
            if($match[2] == "color")
            {
                $output .= '[COLOR=' . $match[3]. ']';
            }
            elseif ($match[2] == "style")
            {
                $px = preg_replace('#(font-size:(.*?))(.*)(px;)#', '$3', $match[3]);
                $px = preg_replace('/\s+/', '', $px);
                $output .= '[SIZE=' . $px . ']';
            }
            if($match[5] == "color")
            {
                $output .= '[COLOR=' . $match[6]. ']';
            }
            elseif ($match[5] == "style")
            {
                $px = preg_replace('#(font-size:(.*?))(.*)(px;)#', '$3', $match[6]);
                $output .= '[SIZE=' . $px . ']';
            }
            $output .= $value[5];
            if($match[5] == "color")
            {
                $output .= '[/COLOR]';
            }
            elseif ($match[5] == "style")
            {
                $output .= '[/SIZE]';
            }
            if($match[2] == "color")
            {
                $output .= '[/COLOR]';
            }
            elseif ($match[2] == "style")
            {
                $output .= '[/SIZE]';
            }
            $output = preg_replace('#=\s(.*);]#', '$1]', $output);
            return $output;
        }
        else
        {
            if($value[3] == 'style')
            {
                $px = preg_replace('#(font-size:(.*?))(.*)(px;)#', '$3', $value[4]);
                return '[SIZE=' . $px . ']' . $value[5] . '[/SIZE]';
            }
            elseif ($value[3] == 'color')
            {
                return '[COLOR=' . str_replace(';', '', $value[4]) . ']' . $value[5] . '[/COLOR]';
            }
        }

    }
    /**
     * @param $render
     * @return mixed|string
     */
    protected function getUrl($render)
    {
        if(isset($render['method']))
        {
            $url = $render['method'] . $render['url'];
            return "[URL=" . $url . ']' . $this->getTitleByUrl($url) . '[/URL]';
        }
        elseif (isset($render['url']))
        {

            $url = $render['url'];
            return "[URL=" . $url . ']' . $this->getTitleByUrl($url) . '[/URL]';
        }
        return $render[0];
    }

    /**
     * @param $url
     * @return false|string
     */
    public function getTitleByUrl($url)
    {
        $html_source = file_get_contents($url);
        $baliseDebut = "<title>";
        $pos1 = strpos( $html_source, $baliseDebut ) + strlen( $baliseDebut );
        $baliseFin = "</title>";
        $pos2 = strpos( $html_source ,$baliseFin );
        return substr( $html_source, $pos1, $pos2 - $pos1 );
    }
}