<?php
namespace Base;

use App\Entity\User;
use Base\Mvc\Entity\DateBase;
use Base\Mvc\Entity\Finder;
use Base\Payment\Payment;
use Base\Reply\Redirect;
use Base\subContent\Reader;
use Base\Util\Date;

/**
 * Class mvcClass
 * @package src\util
 */
class BaseApp
{
    /**
     * @var array
     */
    protected static $phrases = [];

    /**
     * @var array
     */
    protected $includeList = [];

    /**
     * @param $class
     * @param array $set
     * @return mixed
     */
    public static function setNewClass($class, $set = [])
    {
        switch (count($set))
        {
            case 0 : return new $class();
            case 1 : return new $class($set[0]);
            case 2 : return new $class($set[0], $set[1]);
            case 3 : return new $class($set[0], $set[1], $set[2]);
            case 4 : return new $class($set[0], $set[1], $set[2],$set[3]);
        }
        return false;
    }

    /**
     * @param $shortName
     * @return Finder
     */
    public static function finder($shortName)
    {
        return self::getDb()->finder($shortName);
    }

    /**
     * @param $entity
     * @param $id
     * @param array $with
     * @return mixed|null
     * @throws \Exception
     */
    public static function find($entity, $id, $with = [])
    {
        return self::getDb()->find($entity, $id, $with);
    }

    /**
     * @param $table
     * @return Mvc\Entity\Entity
     */
    public static function create($table)
    {
        return self::getDb()->getCreate($table);
    }
    /**
     * @param string $aPath
     * @param bool $aShort
     * @param bool $aCheckIfFileExist
     * @return int|string
     */
    public static function FileGetSize($aPath = '', $aShort = true, $aCheckIfFileExist = true){
        if($aCheckIfFileExist && !file_exists($aPath))
        {
            return 0;
        }
        $size = filesize($aPath);
        if(empty($size))
        {
            return '0 '.($aShort ? 'o':'octets');
        }

        $l = [];
        $l[] = [
            'name' => 'octets',
            'abbr' => 'o',
            'size' => 1
        ];
        $l[] = [
            'name' => 'kilo octets',
            'abbr' => 'ko',
            'size' => 1024
        ];
        $l[] = [
            'name' => 'mega octets',
            'abbr' => 'Mo',
            'size' => 1048576
        ];
        $l[] = [
            'name' => 'giga octets',
            'abbr' => 'Go',
            'size' => 1073741824
        ];
        $l[] = [
            'name' => 'tera octets',
            'abbr' => 'To',
            'size' => 1099511627776
        ];
        $l[] = [
            'name' =>'peta octets',
            'abbr' => 'Po',
            'size' => 1125899906842620
        ];
        foreach($l as $k => $v)
        {
            if($size < $v['size']){
                return round($size / $l[$k-1]['size'], 2).' '.($aShort ? $l[$k-1]['abbr'] : $l[$k-1]['name']);
            }
        }
        $l = end($l);
        return round($size / $l['size'], 2).' '.($aShort ? $l['abbr'] : $l['name']);
    }

    /**
     * @param $string
     * @param $formatter
     * @param null $defaultInfix
     * @return string
     */
    public static function stringToClass($string, $formatter, $defaultInfix = null)
    {
        $parts = explode(':', $string, 3);
        if (count($parts) == 1)
        {
            return $string;
        }

        $prefix = $parts[0];
        if (isset($parts[2]))
        {
            $infix = $parts[1];
            $suffix = $parts[2];
        }
        else
        {
            $infix = $defaultInfix;
            $suffix = $parts[1];
        }

        return $defaultInfix === null
            ? sprintf($formatter, $prefix, $suffix)
            : sprintf($formatter, $prefix, $infix, $suffix);
    }

    /**
     * @param $class
     * @param $type
     * @return string|string[]|null
     */
    public static function classToString($class, $type)
    {
        return preg_replace('@[/\\\]' . $type . '[/\\\]Controller[/\\\]@', ':', $class);
    }
    /**
     * @return DateBase
     */
    public static function getDb()
    {
        return self::setNewClass('Base\Mvc\Entity\DateBase', [self::getConfig()['db']]);
    }
    /**
     * @return \ArrayObject
     */
    public static function getConfigOptions()
    {
        return new \ArrayObject(self::getConfig()['option'],\ArrayObject::ARRAY_AS_PROPS);
    }
    /**
     * @return array
     */
    public static function getConfig()
    {
        $config = [];
        include 'Config.php';
        return $config;
    }

    /**
     * @return Phrase
     */
    public static function classPhrase()
    {
        return self::setNewClass('Base\Phrase');
    }

    /**
     * @param $phrases
     */
    public static function setPhrase($phrases)
    {
        self::$phrases += $phrases;
    }

    /**
     * @param $phraseKey
     * @param array $values
     * @return string|string[]
     */
    public static function phrase($phraseKey, array $values = [])
    {
        $classPhrase = self::classPhrase()->setPhrase(self::$phrases);
        return $classPhrase->getPhrase($phraseKey, $values);
    }

    /**
     * @return string|string[]|null
     */
    public static function getBaseLink()
    {
        $base = self::request()->getBaseUrl();
        $base = preg_replace('#(index|admin)\.php#', '', $base);
        return $base;
    }
    /**
     * @return Date
     */
    public static function date()
    {
        return self::setNewClass('Base\Util\Date', [self::getConfigOptions()->timeZone]);
    }

    /**
     * @param $dateTime
     * @return string
     */
    public static function renderHtmlDate($dateTime)
    {
        return self::date()->renderHtmlDate($dateTime);
    }

    /**
     * @return string
     */
    public static function time()
    {
        return self::date()->getTimeByTimeZoneFormatUnix();
    }

    /**
     * @return string|string[]
     */
    public static function getRootDirectory()
    {
        $dir = dirname(__DIR__);
        return str_replace('\src', '', $dir);
    }

    /**
     * @param $class
     */
    protected function __loader($class)
    {
        $posGuzzleHttp = strpos($class, 'GuzzleHttp\\');
        $posPsr = strpos($class, 'Psr\\');
        if($posGuzzleHttp !== false)
        {
            $posPsr7 = strpos($class, 'GuzzleHttp\Psr7\\');
            $promises = strpos($class, 'GuzzleHttp\Promise\\');
            if(!in_array('guzzlehttp/functions.php', $this->includeList))
            {
                $this->includeList[] = 'guzzlehttp/functions.php';
                include 'src/Vendor/guzzlehttp/guzzle/src/functions.php';
            }
            if(!in_array('guzzlehttp/psr7/functions.php', $this->includeList))
            {
                $this->includeList[] = 'guzzlehttp/psr7/functions.php';
                include 'src/Vendor/guzzlehttp/psr7/src/functions.php';
            }
            if(!in_array('guzzlehttp/promises/functions.php', $this->includeList))
            {
                $this->includeList[] = 'guzzlehttp/promises/functions.php';
                include 'src/Vendor/guzzlehttp/promises/src/functions.php';
            }
            if($posPsr7 !== false)
            {
                $nameClass =  substr($class, 16);
                $classInclude = preg_replace('#\\\\#', '/', $nameClass) ;
                if(!in_array('guzzlehttp/psr7' . $classInclude . '.php', $this->includeList))
                {
                    $this->includeList[] = 'guzzlehttp/psr7/' . $classInclude . '.php';
                    include 'src/Vendor/guzzlehttp/psr7/src/' . $classInclude . '.php';
                }
            }
            elseif($promises !== false)
            {
                $nameClass =  substr($class, 19);
                $classInclude = preg_replace('#\\\\#', '/', $nameClass) ;
                if(!in_array('guzzlehttp/promises/' . $classInclude . '.php', $this->includeList))
                {
                    $this->includeList[] = 'guzzlehttp/psr7/' . $classInclude . '.php';
                    include 'src/Vendor/guzzlehttp/promises/src/' . $classInclude . '.php';
                }
            }
            else
            {
                $nameClass =  substr($class, 11);
                $classInclude = preg_replace('#\\\\#', '/', $nameClass) ;
                if(!in_array('guzzlehttp/' . $classInclude . '.php', $this->includeList))
                {
                    $this->includeList[] = 'guzzlehttp/' . $classInclude . '.php';
                    include 'src/Vendor/guzzlehttp/guzzle/src/' . $classInclude . '.php';
                }
            }


        }
        elseif($posPsr !== false)
        {
            $nameClass =  substr($class, 17);
            $classInclude = preg_replace('#\\\\#', '/', $nameClass) ;
            if(!in_array('psr/' . $classInclude . '.php', $this->includeList))
            {
                $this->includeList[] = 'psr/' . $classInclude . '.php';
                include 'src/Vendor/psr/http-message/src/' . $classInclude . '.php';
            }
        }
        else
        {
            $classInclude = preg_replace('#\\\\#', '/', $class) ;
            $this->includeList[] = $classInclude . '.php';
            include 'src/' . $classInclude . '.php';
        }
    }

    /**
     * @throws \Exception
     */
    public function Autoloader()
    {
        spl_autoload_register([$this , '__loader']);
    }

    /**
     * @return bool|mixed
     */
    public static function InputFilterer()
    {
        return self::setNewClass('Base\InputFilterer');
    }
    /**
     * @return Request
     */
    public static function request()
    {
        return self::setNewClass('Base\Request', [self::InputFilterer()]);
    }

    /**
     * @return Mvc\Entity\Entity|User
     * @throws \Exception
     */
    public static function visitor()
    {
        if(isset($_SESSION['user_id']))
        {
            $user = self::finder('App:User')
                ->where(self::getConfigOptions()->user_id, $_SESSION[self::getConfigOptions()->SessionUserId])
                ->fetchOne();

            if($user)
            {
                return $user;
            }
            else
            {
                return self::getDb()->instantiateEntity('App:User');
            }

        }
        else
        {
            return self::getDb()->instantiateEntity('App:User');
        }

    }

    /**
     * @return Mvc\Entity\Entity|null
     * @throws \Exception
     */
    public static function VisitorAdmin()
    {
        $visitor = self::visitor();
        $id = $visitor->{self::getConfigOptions()->user_id};
        if(!isset($id))
        {
            return null;
        }
        if(!$visitor->isAdmin())
        {
            return null;
        }
        return $visitor;
    }

    /**
     * @param $shortName
     * @return string
     */
    public static function repository($shortName)
    {
        $className = self::stringToClass($shortName, '%s\Repository\%s');
        return self::setNewClass($className);
    }

    /**
     * @param $key
     * @param null $type
     * @param null $default
     * @return array|bool|\DateTime|false|float|int|string|string[]|null
     */
    public static function filter($key, $type = null, $default = null)
    {
        return self::request()->filter($key, $type, $default);
    }

    /**
     * @return \ArrayObject
     */
    public static function getListPurchasable()
    {
        $lists = self::getConfigOptions()->purchasable;
        return new \ArrayObject($lists,\ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param $baseLink
     * @return App
     */
    public static function newApp($baseLink)
    {
        return new App($baseLink);
    }

    /**
     * @return \Base\subContent\Reader
     */
    public static function http()
    {
        return self::setNewClass('Base\subContent\Reader');
    }

    /**
     * @param $var
     * @param bool $echo
     * @param bool $escape
     * @return string|string[]|null
     */
    public static function dumpSimple($var, $echo = true, $escape = true)
    {
        ob_start();
        print_r($var);
        $dump = ob_get_clean();
        $dump = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $dump);

        if (PHP_SAPI == 'cli')
        {
            $output = $dump;
        }
        else
        {
            if ($escape)
            {
                $output = '<pre>' . htmlspecialchars($dump) . '</pre>';
            }
            else
            {
                $output = $dump;
            }
        }

        if ($echo)
        {
            echo $output;
        }
        return $output;
    }
}