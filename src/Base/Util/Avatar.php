<?php


namespace Base\Util;


class Avatar
{
    protected static $avatarDefaultStylingCache = [];
    public static function getRelativeLuminance($r, $g = null, $b = null)
    {
        if (is_array($r))
        {
            $b = $r[2];
            $g = $r[1];
            $r = $r[0];
        }

        $scaler = function($color)
        {
            $color /= 255;
            if ($color <= 0.03928)
            {
                return $color / 12.92;
            }
            else
            {
                return pow(($color + 0.055) / 1.055, 2.4);
            }
        };

        $r = $scaler($r);
        $g = $scaler($g);
        $b = $scaler($b);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
    public static function getColorAvatarStyle($username)
    {
        $bytes = md5($username, true);
        $backgroundR = round(6 * ord($bytes[0]) / 6);
        $backgroundG = round(6 * ord($bytes[1]) / 6);
        $backgroundB = round(6 * ord($bytes[2]) / 6);

        if($backgroundR < 15)
        {
            $backgroundR += 30;
        }
        elseif($backgroundR > 220)
        {
            $backgroundR -= 23;
        }
        elseif ($backgroundR > 170)
        {
            $backgroundR -= 50;
        }

        if($backgroundG > 180)
        {
            $backgroundG -= 25;
        }
        elseif ($backgroundG < 60)
        {
            $backgroundG += 30;
        }
        elseif($backgroundG < 130)
        {
            $backgroundG += 25;
        };
        if($backgroundB > 150)
        {
            $backgroundG -= 2;
        }
        elseif($backgroundB < 60)
        {
            $backgroundB += 10;
        }
        elseif ($backgroundB < 130)
        {
            $backgroundB += 25;
        }
        $divider = 11;
        if(self::getRelativeLuminance($backgroundR, $backgroundG, $backgroundB) > 0.179)
        {
            $colorR = round(5 * ord($bytes[0]) / $divider);
            $colorG = round(6 * ord($bytes[1] ) / $divider);
            $colorB = round(6 * ord($bytes[2]) / $divider);
            if($colorR < 10)
            {
                $colorR -= 2;
            }
            if($colorG > 120)
            {
                $colorG -= 5;
            }
            elseif($colorG > 100)
            {
                $colorG -= 84;
            }
        }
        else
        {
            $colorR = round(5 * (ord($bytes[0]) + 46) / 5);
            $colorG = round(5 * (ord($bytes[1]) + 88) / 4);
            $colorB = round(5 * (ord($bytes[2]) + 66) / 4);
        }
        self::$avatarDefaultStylingCache[$username] = [
            'background' => [
                'r' => $backgroundR,
                'g' => $backgroundG,
                'b' => $backgroundB,
            ],
            'color' => [
                'r' => $colorR,
                'g' => $colorG,
                'b' => $colorB,
            ]
        ];
        return self::$avatarDefaultStylingCache[$username];
    }
    public static function getAvatar($Id, $size = "s")
    {
        $renderLogo = "<span class=\"avatar-u2-s\">$username[0]</span>";
        if(file_exists('data/r6/' . $Id . '.jpg'))
        {
            $renderLogo = "<img src=\"" .  \Base\BaseApp::getBaseLink() . "data/avatar/" . $Id  .".jpg\" alt=\"" . $Id . "\">";
        }
        return "<span class=\"avatarWrapper\">
                    <span class=\"avatar avatar--$size avatar--default\" data-user-id=\"$Id\"
                                  style=\"background-color:$background; color: $color\">
                                  $renderLogo
		                </span>
		         </span>";
    }
}