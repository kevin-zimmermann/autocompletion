<?php

namespace App\Entity;
use Base\BaseApp;
use Base\Mvc\Entity\Structure;
use Base\Mvc\Entity\Entity;

/**
 * Class Operators
 */

class Operators extends Entity
{

    public function getUrlBypath()
    {
        return (\Base\BaseApp::getBaseLink() != '/' ? \Base\BaseApp::getBaseLink() : '/') . 'data/r6/' . $this->id. '.png';
    }

    public function getAlt()
    {
        return $this->operator_name;
    }

    public function getSide(){
        return BaseApp::phrase('r6_' . preg_replace('#/#','_', $this->side)) ;
    }

    public  function getYear(){
        $operatorReveal = trim($this->operation_reveal);
        $operationReveal = preg_replace('#\s#', '_', $operatorReveal);
        $operationReveal = strtolower($operationReveal);
        return BaseApp::phrase('r6_' . $operationReveal) ;
    }

    public static function getStructure(Structure $structure)
    {
        $structure->table = 'operators';
        $structure->shortName = 'App:Home';
        $structure->primaryKey = 'id';
        $structure->columns = [
            'id' => ['type' => self::UINT, 'autoIncrement' => true],
            'operator_name' => ['type' => self::STR],
            'side' => ['type' => self::STR],
            'operation_reveal' => ['type' => self::STR],
        ];

        return $structure;
    }

}