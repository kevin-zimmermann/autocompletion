<?php


namespace Base\Util;


class Arr
{
    /**
     * @param array $arr
     * @return \ArrayObject
     */
    public static function ArrayToObject(array $arr)
    {
        return new \ArrayObject($arr,\ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param array $first
     * @param array $second
     * @return array
     */
    public static function mapMerge(array $first, array $second)
    {
        $args = func_get_args();
        unset($args[0]);

        foreach ($args AS $arg)
        {
            if (!is_array($arg) || !$arg)
            {
                continue;
            }
            foreach ($arg AS $key => $value)
            {
                if (is_array($value) && isset($first[$key]) && is_array($first[$key]))
                {
                    $first[$key] = self::mapMerge($first[$key], $value);
                }
                else
                {
                    $first[$key] = $value;
                }
            }
        }

        return $first;
    }
}