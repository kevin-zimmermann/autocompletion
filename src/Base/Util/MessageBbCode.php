<?php


namespace Base\Util;


use App\Entity\Emoji;
use Base\BaseApp;

class MessageBbCode
{
    public static function parser($message, $edit = false)
    {
        $parser = new Parser(htmlentities($message), $edit);
        return $parser->parser();
    }
    public static function testLengthMessage($message)
    {
        return strlen(preg_replace('/\s/', '', $message));
    }
    public static function renderHtmlToBbCode($message)
    {
        $render = new BBCode\Render($message);
        return $render->renderBbCode();
    }
    public static function parserResume($message, $trimmed = 500)
    {
        $parser = new parserTrimmed($message);
        return $parser->parserTrimmed($trimmed);
    }
    public static function renderSmiley($message)
    {
        $pattern = '#<img.src=".*?".alt=".*?".data-emoji-id="(.*?)".class=".*?">#';
        $message = preg_replace_callback($pattern, [__CLASS__, 'img'], $message);
        return $message;
    }

    /**
     * @param $render
     * @return mixed|string
     * @throws \Exception
     */
    protected static function img($render)
    {
        /** @var Emoji $emoji */
        $emoji = BaseApp::find('App:Emoji', $render[1]);
        $smileyText = explode("\r\n", $emoji->smilie_text);
        return array_filter($smileyText)[0];
    }
}