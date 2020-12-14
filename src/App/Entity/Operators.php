<?php


use Base\BaseApp;
use Base\Mvc\Entity\Structure;

/**
 * Class Operators
 */

class Operators
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = 'operators';
        $structure->shortName = 'App:Home';
        $structure->primaryKey = 'id';
        $structure->columns = [
            'id' => ['type' => self::UINT, 'autoIncrement' => true],
            'operator_name' => ['type' => self::STR],
            'side' => ['type' => self::STR, 'default' => 0],
            'operation_reveal' => ['type' => self::STR, 'default' => 0],
        ];

        return $structure;
    }
}