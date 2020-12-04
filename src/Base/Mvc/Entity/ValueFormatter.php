<?php


namespace Base\Mvc\Entity;


class ValueFormatter
{
    /**
     * @param $type
     * @param $value
     * @return array|bool|false|mixed|string[]|null
     */
    public static function decodeValueFromSource($type, $value)
    {
        if ($value === null)
        {
            return $value;
        }

        switch ($type)
        {
            case Entity::JSON_ARRAY:
                $result = @json_decode($value, true);
                if (!is_array($result))
                {
                    $result = [];
                }
                return $result;

            case Entity::LIST_LINES:
                return $value === '' ? [] : preg_split('/\r?\n/', $value);

            case Entity::LIST_COMMA:
                return $value === '' ? [] : explode(',', $value);

            case Entity::BOOL:
                return $value ? true : false;

            default:
                return $value;
        }
    }
    /**
     * @param $type
     * @param $value
     * @param array $columnOptions
     * @return array|bool|false|mixed|string[]|null
     */
    public static function decodeValueFromSourceExtended($type, $value, array $columnOptions = [])
    {
        if ($value === null)
        {
            return $value;
        }

        $value = self::decodeValueFromSource($type, $value);

        if (
            ($type == Entity::LIST_COMMA || $type == Entity::LIST_LINES)
            && !empty($columnOptions['list']['type'])
        )
        {
            switch ($columnOptions['list']['type'])
            {
                case 'int':
                case 'uint':
                case 'posint':
                    $value = array_map('intval', $value);
            }
        }

        return $value;
    }
    /**
     * @param $type
     * @param $value
     * @return false|int|string|null
     */
    public static function encodeValueForSource($type, $value)
    {
        if ($value === null)
        {
            return $value;
        }

        switch ($type)
        {
            case Entity::BOOL:
                return $value ? 1 : 0;
            case Entity::JSON_ARRAY:
                return json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR);

            case Entity::LIST_LINES:
                return implode("\n", $value);

            case Entity::LIST_COMMA:
                return implode(',', $value);

            default:
                return $value;
        }
    }
}