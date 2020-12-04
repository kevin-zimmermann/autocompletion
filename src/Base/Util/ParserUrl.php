<?php


namespace Base\Util;


use App\Entity\Emoji;
use Base\BaseApp;

class ParserUrl
{
    public static function url($message)
    {
        $pattern = "#(\[(URL|url)=([^]]*)\]([^[]*)\[/(URL|url)\])|(?P<method>https?://|www)(?P<url>.([a-zA-Z0-9\-\/.]+))#";
        $message = preg_replace_callback($pattern, [__CLASS__, 'getUrl'], $message);
        $emojis = BaseApp::finder('App:Emoji')
            ->order('display_order')
            ->fetch();
        /** @var Emoji $emoji */
        foreach ($emojis as $emoji)
        {
            $img = '<img src="' . BaseApp::getBaseLink() . $emoji->image_url . '" alt="' . $emoji->title . '" data-emoji-id="' . $emoji->emoji_id . '" class="smiley-in-content">';
            $smileyText = explode("\r\n", $emoji->smilie_text);
            foreach (array_filter($smileyText) as $text)
            {
                $string = preg_quote($text);
                $message = preg_replace("#$string#", $img, $message);
            }
        }
        return $message;
    }
    /**
     * @param $render
     * @return mixed|string
     */
    protected static function getUrl($render)
    {
        $url = $render[0];
        $parserUrl = parse_url($url);
        if(!isset($parserUrl['scheme']))
        {
            $url = 'https://' . $url;
        }
        return '<a href="' . $url . '" target="_bank">' . $url . '</a>';
    }
}