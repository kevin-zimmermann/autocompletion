<?php


namespace Base\Util;


class parserShortMessage
{
    protected static $htmlEntity = false;
    protected static $startTag = '';
    protected static $endTag = '';
    protected static $countCallback = 0;
    protected static $matchR;
    public static function parserShort($message, $searches = [])
    {
        $message = \Base\Util\MessageBbCode::parser($message);
        $tags = "br|p|div|span|b|a|h1|h3|h2|h4|h5|h6";
        $message = preg_replace('#<(' . $tags . ')(\s[^>]*)?>#i', '', $message);
        $message = preg_replace('#</(' . $tags . ')>#i', '', $message);
        $message = preg_replace('/$\s/', '', $message);
        $message = self::Coup($message, 50);
        return $message;
    }
    public static function Coup($message, $nbChar)
    {
        $subMessage = trim(substr(substr($message,0,$nbChar),0,
            strrpos(substr($message,0,$nbChar)," ")));
        return (strlen($message) > $nbChar ? $subMessage . "..." : $message);
    }
    public static function parserResume($message)
    {
        $message = \Base\Util\MessageBbCode::parser($message);
        return self::textResume($message, 230);
    }
    public static function textResume($text, $nbrCar)
    {
        $LongueurTextBrutSansHtml = strlen(strip_tags($text));
        if($LongueurTextBrutSansHtml < $nbrCar) return $text;
        $MasqueHtmlSplit = '#</?([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?>#';
        $MasqueHtmlMatch = '#<(?:/([a-zA-Z1-6]+)|([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?)>#';
        $text .= ' ';
        $BoutsText = preg_split($MasqueHtmlSplit, $text, -1,  PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $NumberBouts = count($BoutsText);
//        if($NumberBouts == 1)
//        {
//            $longueur = strlen($text);
//            return substr($text, 0, strpos($text, ' ', $longueur > $nbrCar ? $nbrCar : $longueur));
//        }
        $longueur = 0;
        $indexLastBout = $NumberBouts - 1;
        $position = $BoutsText[$indexLastBout][1] + strlen($BoutsText[$indexLastBout][0]) - 1;
        $indexBout = $indexLastBout;
        $rechercheSpace = true;

        foreach( $BoutsText as $index => $bout )
        {
            $longueur += strlen($bout[0]);

            if( $longueur >= $nbrCar )
            {
                $position_fin_bout = $bout[1] + strlen($bout[0]) - 1;

                $position = $position_fin_bout - ($longueur - $nbrCar);

                if( ($positionSpace = strpos($bout[0], ' ', $position - $bout[1])) !== false  )
                {
                    $position = $bout[1] + $positionSpace;
                    $rechercheSpace = false;
                }

                if( $index != $indexLastBout )
                    $indexBout = $index + 1;
                break;
            }
        }

        if( $rechercheSpace === true )
        {
            for($i = $indexBout; $i <= $rechercheSpace; $i++ )
            {
                $position = $BoutsText[$i][1];
                if( ($positionSpace = strpos($BoutsText[$i][0], ' ')) !== false )
                {
                    $position += $positionSpace;
                    break;
                }
            }
        }

        $text = substr($text, 0, $position);

        preg_match_all($MasqueHtmlMatch, $text, $return, PREG_OFFSET_CAPTURE);
        $BoutsTag = [];
        foreach($return[0] as $index => $tag)
        {
            if(isset($retour[3][$index][0]))
            {
                continue;
            }
            if($return[0][$index][0][1] != '/')
            {
                array_unshift($BoutsTag, $return[2][$index][0]);
            }
            else
            {
                array_shift($BoutsTag);
            }
        }

        if(!empty($BoutsTag))
        {
            foreach($BoutsTag as $tag)
            {
                $text .= '</' . $tag . '>';
            }
        }
        if ($LongueurTextBrutSansHtml > $nbrCar)
        {
            $text .= '...';

            $text =  str_replace('</p> [......]', '... </p>', $text);
            $text =  str_replace('</ul> [......]', '... </ul>', $text);
            $text =  str_replace('</div> [......]', '... </div>', $text);
        }

        return $text;
    }
    public static function parserTrimmed($message, $trimmed = 150)
    {
        $message = \Base\Util\MessageBbCode::parser($message);
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
                $startTag = $match[1];
                if(preg_match('#(<(span).*?>)(.*)#i', $messageCurrent, $matches))
                {
                    $messageCurrent = $matches[3];
                    $startTag .= $matches[1];
                    $endTag = '</' . $matches[2] . '>' . '</div>';
                }
                $callback  = preg_replace_callback('#<(u|i|b)>(.*)</(u|i|b)>#s', [__CLASS__, 'getCallbackBase'], $messageCurrent);
                $tags = "u|i|b";
                $callback = preg_replace('#<(' . $tags .')>#s', '', $callback);
                $callback = preg_replace('#</(' . $tags .')>$#s', '', $callback);
                $messageCurrent =  preg_replace_callback('#(.*?)(<i.*?></i>)(.*?)#U', [__CLASS__, 'getFA'], $callback) ;
                $startTag .= self::$startTag;
                $endTag = self::$endTag . $endTag;
                $lengthCurrent = strlen($messageCurrent);
                $lengthOutput += $lengthCurrent;
                if($lengthOutput  < $trimmed)
                {
                    $outputMessage .= $startTag . (self::$htmlEntity ? $messageCurrent : htmlentities($messageCurrent)) . $endTag;
                }
                else
                {
                    $retire = $lengthOutput - $trimmed;
                    $messageCoup = self::Coup($messageCurrent, $retire);
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
                        $outputMessage .= $startTag .  (self::$htmlEntity ? $messageCoup : htmlentities($messageCoup)) . $endTag;
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
                $messageCoup = self::Coup($message, $retire);
                $outputMessage = htmlentities($messageCoup);
            }
            else
            {
                $outputMessage = htmlentities($message);
            }
        }
        return $outputMessage;
    }
    public static function getCallbackBase($match)
    {
        self::$startTag .= '<' . $match[1] . '>';
        self::$endTag = '</' . $match[1] . '>' . self::$endTag;
        self::$matchR = preg_replace_callback('#<(i)>(.*)</(i)>#U', [__CLASS__, 'getCallbackBase'], $match[2]);
        self::$matchR = preg_replace_callback('#<(b)>(.*)</(b)>#', [__CLASS__, 'getCallbackBase'], $match[2]);

        return self::$matchR;
    }
    public static function getFA($match)
    {
        self::$htmlEntity = true;
        return htmlentities($match[1]) . $match[2] . htmlentities($match[3]);
    }
}