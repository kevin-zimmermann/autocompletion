<?php


namespace Base\Util;


class parserTrimmed
{
    protected $htmlEntity = false;
    protected $startTag = '';
    protected $endTag = '';
    protected $countCallback = 0;
    protected $matchR;
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function parserTrimmed($trimmed = 40)
    {
        $message = \Base\Util\MessageBbCode::parser($this->message);
//        var_dump($message);
        preg_match_all('#(<(span)(.*?)>|<(div)(.*?)>|)(.*?)(<br>|</span>|</div>)#i', $message, $matches, PREG_SET_ORDER);
        $outputMessage = "";
        $lengthOutput = 0;
        if(!empty($matches))
        {

            foreach ($matches as $match)
            {
                if(empty($match[0]))
                {
                    continue;
                }
                if($match[0] == "</div>")
                {
                    continue;
                }

                $messageCurrent = $match[6];
                $endTag = $match[7];
                if($endTag == '<br>' && !empty($match[4]) && $match[4] != '<br>')
                {
                    $endTag .=  '</' . $match[4] . '>';
                }
                $startTag = $match[1];
                if(preg_match('#(<(span).*?>)(.*)#i', $messageCurrent, $matches))
                {
                    $messageCurrent = $matches[3];
                    $startTag .= $matches[1];
                    $endTag = '</' . $matches[2] . '>' . '</div>';
                }
                $messageCurrent =  $this->basicBBCode($messageCurrent);
                $startTag .= $this->startTag;
                $endTag = $this->endTag . $endTag;
                $lengthCurrent = strlen($messageCurrent);
                $lengthOutput += $lengthCurrent;

                if($lengthOutput  < $trimmed)
                {
                    $outputMessage .= $startTag . $messageCurrent . $endTag;
                }
                else
                {
                    $retire = $lengthOutput - $trimmed;
                    $messageCoup = $this->Coup($messageCurrent, $retire);
                    if($messageCoup == '...')
                    {
                        if(preg_match('#(.*)(<br>|</div>)$#', $outputMessage, $matches))
                        {
                            $outputMessage = $matches[1] . $messageCoup . $matches[2];
                        }
                        else
                        {
                            $outputMessage .= $messageCoup;
                        }

                    }
                    else
                    {
                        $outputMessage .= $startTag .  $messageCoup . $endTag;
                    }
                    break;
                }
            }
        }
        else
        {
            $lengthMessage = strlen($message);
            if($lengthMessage > $trimmed)
            {
                $retire = $lengthMessage - $trimmed;
                $messageCoup = $this->Coup($message, $retire);
                $messageCurrent =  $this->basicBBCode($messageCoup);
                $outputMessage = $messageCurrent;
            }
            else
            {
                $messageCurrent =  $this->basicBBCode($message);
                $outputMessage = $messageCurrent;
            }
        }
//        var_dump($outputMessage);
        return $outputMessage;
    }
    public function getCallbackBase($match)
    {
        $this->startTag .= '<' . $match[1] . '>';
        $this->endTag = '</' . $match[1] . '>' . $this->endTag;
        $this->matchR = preg_replace_callback('#<(i)>(.*)</(i)>#U', [__CLASS__, 'getCallbackBase'], $match[2]);
        $this->matchR = preg_replace_callback('#<(b)>(.*)</(b)>#', [__CLASS__, 'getCallbackBase'], $match[2]);

        return $this->matchR;
    }
    public function getFA($match)
    {
        $this->htmlEntity = true;
        $message = $match[3];
        $message = preg_replace('#^</i>#', '', $message);
        return htmlentities($match[1]) . $match[2] . '</i>' . htmlentities($match[3]);
    }
    protected function coup($message, $retire)
    {
        return \Base\Util\parserShortMessage::textResume($message, 1);
    }
    protected function basicBBCode($message)
    {
        $callback  = preg_replace_callback('#<(u|i|b)>(.*)</(u|i|b)>#s', [__CLASS__, 'getCallbackBase'], $message);
        $tags = "u|i|b";
        $callback = preg_replace('#<(' . $tags .')>#s', '', $callback);
        $callback = preg_replace('#</(' . $tags .')>$#s', '', $callback);
        return  preg_replace_callback('#(.*?)(<i.*?>)(.*?)#U', [__CLASS__, 'getFA'], $callback) ;
    }
}