<?php


namespace Base\Mvc\Entity;


use Base\BaseApp;

class Entity  implements \ArrayAccess
{
    const INT                   =  0x0001;
    const UINT                  =  0x0002;
    const FLOAT                 =  0x0003;
    const BOOL                  = 0x10004;
    const STR                   =  0x0005;
    const BINARY                =  0x0006;
    const JSON_ARRAY            = 0x10010;
    const LIST_LINES            = 0x10011;
    const LIST_COMMA            = 0x10012;


    const TO_ONE  = 1;
    const TO_MANY = 2;
    /**
     * @var string
     */
    protected $_table;

    /**
     * @var array
     */
    protected $_values = [];

    /**
     * @var array
     */
    protected $_relations = [];

    /**
     * @var array
     */
    protected $_newValues = [];
    protected $_valueCache = [];
    protected $_structure;
    /**
     * @var bool
     */
    protected $insert = false;
    protected $_errors = [];
    protected $_writeRunning = false;
    protected $_writePending;
    protected $_deleted;
    /**
     * Entity constructor.
     * @param Structure $structure
     * @param $PrimaryKey
     * @param array $values
     * @param array $relations
     */
    public function __construct(Structure $structure, array $values = [], array $relations = [])
    {
        $this->_structure = $structure;
        $this->_values = $values;
        $this->_relations = $relations;

    }

    /**
     * @param $key
     * @return mixed|null
     * @throws \Exception
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     * @return mixed|null
     * @throws \Exception
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }
    /**
     * @param $key
     * @return mixed|null
     * @throws \Exception
     */
    public function get($key)
    {
        $structure = $this->_structure;
        if (!empty($structure->columns[$key]))
        {
            return $this->getValue($key);
        }
        if (isset($this->_relations[$key]))
        {
            return $this->_relations[$key];
        }
        if(!empty($structure->relations[$key]))
        {
            return $this->getRelation($key);
        }
        return null;
    }

    /**
     * @param $key
     * @return Entity
     */
    public function getRelation($key)
    {
        $relations = $this->_structure->relations;
        if (!array_key_exists($key, $this->_relations))
        {
            $this->_relations[$key] = $this->getDb()->getRelation($relations[$key], $this);
        }
        return $this->_relations[$key];
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getValue($key)
    {
        $columns = $this->_structure->columns;
        if (empty($columns[$key]))
        {
            throw new \InvalidArgumentException("Unknown column $key");
        }
        $column = $columns[$key];
        if (array_key_exists($key, $this->_newValues))
        {
            $value = ValueFormatter::decodeValueFromSourceExtended($column['type'], $this->_newValues[$key], $column);
        }
        elseif (array_key_exists($key, $this->_values))
        {
            $value = ValueFormatter::decodeValueFromSourceExtended($column['type'], $this->_values[$key], $column);
        }
        else if (array_key_exists('default', $column))
        {
            $value = $column['default'];
        }
        else
        {
            $value = null;
        }
        return $value;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * return true or false
     */
    protected function _preSave() {}

    /**
     * @param bool $newTransaction
     * @return Entity|bool
     * @throws \Exception
     */
    public final function save($newTransaction = true)
    {
        if(!$this->preSave())
        {
            return false;
        }
        $db = $this->getDb();
        $isInsert = $this->isInsert();
        if ($newTransaction)
        {
            $db->beginTransaction();
        }

        try
        {
            $this->_saveToSource();

            if ($isInsert)
            {
                $this->getDb()->attachEntity($this);
            }
            $this->_postSave();

        }
        catch (\Exception $e)
        {
            if ($newTransaction)
            {
                $db->rollback();
            }

            throw $e;
        }
        if ($newTransaction)
        {
            $db->commit();
        }
        $newDbValues = $this->_newValues;
        $columns = $this->_structure->columns;
        foreach ($newDbValues AS $column => $value)
        {
            $newDbValues[$column] = ValueFormatter::encodeValueForSource($columns[$column]['type'], $value);
        }
        $this->_saveCleanUp($newDbValues);
        return true;
    }

    /**
     *
     */
    protected function _postSave() {}
    /**
     * @return bool
     */
    public final function preSave()
    {
        // write will be pending after calling this; this means call it only once
        if ($this->_writePending != 'save')
        {
            $this->_preSave();

            if ($this->isInsert())
            {
                $this->_fillInsertDefaults();
            }
            $this->_validateRequirements();

            $this->_writePending = 'save';
        }

        return count($this->_errors) == 0;
    }
    protected function _validateRequirements()
    {
        foreach ($this->_structure->columns AS $key => $column)
        {
            if (empty($column['required']))
            {
                continue;
            }


            if ($this->isUpdate() && !array_key_exists($key, $this->_newValues))
            {
                continue;
            }

            $value = $this->getValue($key);
            $exists = array_key_exists($key, $this->_newValues) || array_key_exists($key, $this->_values);

            if (!empty($column['nullable']) && $value === null && $exists)
            {
                continue;
            }

            if (!$exists || $value === '' || $value === [] || $value === null)
            {
                if (is_string($column['required']))
                {
                    $column['required'];
                }
                else
                {
                   return 'please_enter_value_for_required_field_x';
                }
            }
        }
    }
    protected function _fillInsertDefaults()
    {
        foreach ($this->_structure->columns AS $key => $column)
        {
            if (array_key_exists($key, $this->_newValues))
            {
                continue;
            }
            if (array_key_exists('default', $column))
            {
                $this->_setInternal($key, $column['default']);
            }
            else if (!empty($column['nullable']))
            {
                $this->_setInternal($key, null);
            }
        }
    }
    /**
     * @return array
     * @throws \Exception
     */
    protected function _saveToSource()
    {
        $db = $this->getDb();
        $structure = $this->_structure;
        $columns = $structure->columns;

        $save = $this->_newValues;
        foreach ($save AS $column => $value)
        {
            if (!isset($columns[$column]))
            {
                throw new \LogicException("Colonne inconnue $column a été trouvée dans les données à sauvegarder");
            }

            $save[$column] = ValueFormatter::encodeValueForSource($columns[$column]['type'], $value);
        }

        if ($save)
        {
            if ($this->isInsert())
            {
                $db->insert($structure->table, $save);
                $this->_fillAutoIncrement($db->lastInsertId(), $save);
            }
            else
            {
                $db->update($structure->table, $save, $this->_getUpdateCondition());
            }
        }

        return $save;
    }
    /**
     * @param array $newDbValues
     */
    protected function _saveCleanUp(array $newDbValues)
    {
        $this->_writePending = false;
        $this->_writeRunning = false;
        $this->_values = array_merge($this->_values, $newDbValues);
        $this->_newValues = [];
        $this->_errors = [];

        foreach ($newDbValues As $key => $null)
        {
            unset($this->_valueCache[$key]);
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _getUpdateCondition()
    {
        $conditions = [];
        $db = $this->getDb();
        foreach ((array)$this->_structure->primaryKey AS $key)
        {

            $value = $this->_values[$key];
            if ($value === null)
            {
                throw new \Exception("Trouvé nul dans la clé primaire de l'entité. A-t-il été appelé avant d'être sauvegardé ?");
            }
            $conditions[] = "`$key` = " . $db->quote($value);
        }
        if (!$conditions)
        {
            throw new \Exception("Aucune clé primaire définie pour l'entité " . get_class($this));
        }

        return implode(' AND ', $conditions);
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    protected function _setInternal($key, $value)
    {
        if (!isset($this->_structure->columns[$key]))
        {
            throw new \InvalidArgumentException("La colonne $key est inconnue");
        }
        $column = $this->_structure->columns[$key];
        $skipInvalidate = ($this->isInsert() && !empty($column['autoIncrement']) && $value === null);

        if ($this->_columnValueIsDifferent($key, $value, $column))
        {
            $this->_newValues[$key] = $value;
            if (!$skipInvalidate)
            {
                $this->_invalidateCachesOnChange($key);
            }

            return true;
        }
        else if (array_key_exists($key, $this->_newValues))
        {
            unset($this->_newValues[$key]);

            if (!$skipInvalidate)
            {
                $this->_invalidateCachesOnChange($key);
            }

            return true;
        }

        return false;
    }
    /**
     * @param $key
     * @param $value
     * @param array $column
     * @return bool
     */
    protected function _columnValueIsDifferent($key, $value, array $column)
    {
        return (
            !array_key_exists($key, $this->_values)
            || $value !== ValueFormatter::decodeValueFromSourceExtended($column['type'], $this->_values[$key], $column)
        );
    }
    /**
     * @param $key
     */
    protected function _invalidateCachesOnChange($key)
    {
        unset($this->_relations[$key]);

        foreach ($this->_structure->relations AS $relationName => $relation)
        {
            $conditions = $relation['conditions'];
            if (!is_array($conditions))
            {
                $conditions = [$conditions];
            }

            foreach ($conditions AS $condition)
            {
                if (is_string($condition))
                {
                    if ($condition == $key)
                    {
                        unset($this->_relations[$relationName]);
                    }
                }
                else if (count($condition) > 3)
                {
                    foreach (array_slice($condition, 2) AS $v)
                    {
                        if ($v && $v[0] == '$' && substr($v, 1) == $key)
                        {
                            unset($this->_relations[$relationName]);
                            break;
                        }
                    }
                }
                else if (is_string($condition[2]) && $condition[2][0] == '$' && substr($condition[2], 1) == $key)
                {
                    unset($this->_relations[$relationName]);
                }

                if (is_array($condition) && is_string($condition[0]) && $condition[0][0] == '$' && substr($condition[0], 1) == $key)
                {
                    unset($this->_relations[$relationName]);
                }
            }
        }
    }

    /**
     * @param $value
     * @param array $newSourceValues
     * @return bool
     */
    protected function _fillAutoIncrement($value, array &$newSourceValues)
    {
        foreach ($this->_structure->columns AS $key => $column)
        {
            if (!empty($column['autoIncrement']))
            {
                $this->_setInternal($key, $value);
                $newSourceValues[$key] = $value;
                return true;
            }
        }
    }
    /**
     * @return bool
     */
    public function isUpdate()
    {
        return $this->exists();
    }

    /**
     * @return bool
     */
    public function exists()
    {
        if ($this->_deleted)
        {
            return false;
        }

        return $this->_values ? true : false;
    }
    /**
     * @return bool
     */
    public function isInsert()
    {
        if ($this->_deleted)
        {
            return false;
        }

        return $this->_values ? false : true;
    }

    /**
     * @return $this
     */
    public function insert()
    {
        $this->insert = true;
        return $this;
    }
    /**
     * @return array|null
     */
    public function getIdentifierValues()
    {
        $values = [];
        foreach ((array)$this->_structure->primaryKey AS $key)
        {
            $value = $this->getValue($key);
            if ($value === null)
            {
                return null;
            }
            $values[$key] = $value;
        }
        if (!$values)
        {
            throw new \LogicException("Aucune clé primaire définie pour l'entité " . get_class($this));
        }
        return $values;
    }
    /**
     * @return string|null
     */
    public function getIdentifier()
    {
        $keys = $this->getIdentifierValues();
        return $keys ? implode('-', $keys) : null;
    }

    /**
     * @param bool $throw
     * @param bool $newTransaction
     * @return bool
     * @throws \Exception
     */
    public final function delete($throw = true, $newTransaction = true)
    {
        if ($this->_deleted)
        {
            return true;
        }
        if (!$this->exists())
        {
            throw new \LogicException("Cannot delete a non-saved entity");
        }
        if ($this->_newValues)
        {
            throw new \LogicException("Cannot delete an entity that has been partially updated");
        }


        $db = $this->getDb();

        if ($newTransaction)
        {
            $db->beginTransaction();
        }

        $this->_writeRunning = true;
        $this->_deleted = true;

        $rowAffected = $db->delete($this->_structure->table, $this->_getUpdateCondition());

        if ($rowAffected)
        {
            $this->_postDelete();
        }
        if ($newTransaction)
        {
            $db->commit();
        }

        $this->getDb()->detachEntity($this);
        $this->_writePending = false;
        $this->_writeRunning = false;

        return true;
    }
    /**
     *
     */
    protected function _preDelete() {}

    /**
     *
     */
    protected function _postDelete() {}
    /**
     * @param Structure $structure
     * @return Structure
     * @throws \LogicException
     */
    public static function getStructure(Structure $structure)
    {
        throw new \LogicException(get_called_class() . '::getStructure() must be overridden');
    }

    /**
     * @param $shortName
     * @return mixed
     */
    protected function finder($shortName)
    {
        return BaseApp::finder($shortName);
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->_newValues[$key] = $value;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value) {}

    /**
     * @param mixed $key
     * @return bool|void
     */
    public function offsetExists($key) {}

    /**
     * @param mixed $key
     */
    public function offsetUnset($key)
    {
        throw new \LogicException('Entity offsets may not be unset');
    }

    /**
     * @return bool|mixed
     */
    protected function getDb()
    {
        return BaseApp::getDb();
    }
}