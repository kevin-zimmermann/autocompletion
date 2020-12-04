<?php
namespace Base\Mvc\Entity;

use Base\BaseApp;
use Base\Mvc\Entity\Finder;
use Base\Mvc\Entity\ArrayCollection;

/**
 * Class DateBase
 * @package Entity
 */
class DateBase
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \PDO|null
     */
    protected $db = null;

    /**
     * @var bool|false|\PDOStatement
     */
    protected $statement;

    protected $structures;
    protected $entities;
    /**
     * DateBase constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $db = new \PDO('mysql:host='. $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['dbname'] . ";charset=utf8", $config['username'], $config['password']);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
        $this->db = $db;
    }
    /**
     * @param $shortName
     * @return mixed
     */
    public function getEntityClassName($shortName)
    {
        return BaseApp::stringToClass($shortName, '%s\Entity\%s');
    }
    /**
     * @param string $shortName
     *
     * @return Structure
     */
    public function getEntityStructure($shortName)
    {
        $className = $this->getEntityClassName($shortName);

        if (!isset($this->structures[$className]))
        {
            $structure = $className::getStructure(new Structure());
            $structure->shortName = $shortName;

            $this->structures[$className] = $structure;
        }
        return $this->structures[$className];
    }

    /**
     * @param $shortName
     * @return \Base\Mvc\Entity\Finder
     */
    public function finder($shortName)
    {
        $structure = $this->getEntityStructure($shortName);
        return new Finder($this, $structure);
    }
    /**
     * @param $shortName
     * @param $id
     * @param null $with
     * @return mixed|null
     * @throws \Exception
     */
    public function find($shortName, $id, $with = null)
    {
        if ($id === null || $id === false)
        {
            return null;
        }

        $finder = $this->finder($shortName);
        $finder->whereId($id);
        if ($with)
        {
            $finder->with($with);
        }
        return $finder->fetchOne();
    }
    /**
     * @param array $entities
     *
     * @return ArrayCollection
     */
    public function getBasicCollection(array $entities)
    {
        return new ArrayCollection($entities);
    }
    /**
     * @param Entity $entity
     */
    public function detachEntity(Entity $entity)
    {
        $keys = $entity->getIdentifierValues();
        if (!$keys)
        {
            return;
        }

        $primary = $this->getEntityCacheLookupString($keys);
        $class = get_class($entity);

        unset($this->entities[$class][$primary]);
    }

    /**
     * @param $shortName
     * @param array $values
     * @param array $relations
     * @param int $options
     * @return Entity
     */
    public function instantiateEntity($shortName, array $values = [], array $relations = [], $options = 0)
    {
        $className = $this->getEntityClassName($shortName);
        $structure = $this->getEntityStructure($shortName);
        $entity = new $className($structure, $values, $relations);

        return $entity;
    }
    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * @param Entity $entity
     * @throws \Exception
     */
    public function attachEntity(Entity $entity)
    {
        $keys = $entity->getIdentifierValues();
        if (!$keys)
        {
            throw new \Exception("Cannot attach an entity without a valid primary key");
        }

        $primary = $this->getEntityCacheLookupString($keys);
        $class = get_class($entity);

        $this->entities[$class][$primary] = $entity;
    }

    /**
     * @param array $values
     * @return string
     */
    protected function getEntityCacheLookupString(array $values)
    {
        return implode('\x1E', $values);
    }
    /**
     * @param array $relation
     * @param Entity $entity
     * @param string $fetchType
     * @return Finder
     * @throws \Exception
     */
    public function getRelationFinder(array $relation, Entity $entity, $fetchType = 'current')
    {
        $finder = $this->finder($relation['entity']);

        $conditions = $relation['conditions'];
        if (!is_array($conditions))
        {
            $conditions = [$conditions];
        }

        foreach ($conditions AS $condition)
        {
            if (is_string($condition))
            {
                $finder->where($condition, '=', $entity->getValue($condition));
            }
            else
            {
                list($field, $operator, $value) = $condition;


                if (count($condition) > 3)
                {
                    $readValue = '';
                    foreach (array_slice($condition, 2) AS $v)
                    {
                        if ($v && $v[0] == '$')
                        {
                            $readValue .= $entity->getValue(substr($v, 1));
                        }
                        else
                        {
                            $readValue .= $v;
                        }
                    }
                    $finder->where($field, $operator, $readValue);
                }
                else if (is_string($value) && $value && $value[0] == '$')
                {
                    $finder->where($field, $operator, $entity->getValue(substr($value, 1)));
                }
                else
                {
                    $finder->where($field, $operator, $value);
                }
            }
        }

        if (!empty($relation['with']))
        {
            foreach ((array)$relation['with'] AS $extraWith)
            {
                $finder->with($extraWith);
            }
        }
        if (!empty($relation['order']))
        {
            $finder->order($relation['order']);
        }
        return $finder;
    }

    /**
     * @param array $relation
     * @param Entity $entity
     * @return array|Entity|FinderCollection|mixed|null
     * @throws \Exception
     */
    public function getRelation(array $relation, Entity $entity)
    {
        $finder = $this->getRelationFinder($relation, $entity);
        if ($relation['type'] == Entity::TO_ONE)
        {
            $result = $finder->fetchOne();
            if (!$result)
            {
                $result = null;
            }
        }
        else
        {
            if (!empty($relation['key']))
            {
                $result = new FinderCollection($finder, $relation['key']);
            }
            else
            {
                $result = $finder->fetch();
            }
        }
        return $result;
    }

    /**
     * @param array $row
     * @param array $map
     * @return mixed
     */
    public function HydrateFromGrouped(array $row, array $map)
    {
        $entityRelations = [];
        $finderRelations = [];
        foreach ($map AS $name => $info)
        {
            $data = (array)$row[$info['alias']];
            $entity = $this->instantiateEntity(
                $info['entity'],
                $data,
                isset($entityRelations[$name]) ? $entityRelations[$name] : []
            );
            if ($info['relationValue'] !== null)
            {
                $finderRelations[$info['parentRelation']][$info['relation']][$info['relationValue']] = $entity;
            }
            else
            {
                $entityRelations[$info['parentRelation']][$info['relation']] = $entity;
            }
        }
        return $entityRelations[''][''];
    }
    /**
     * @param $data
     * @return false|string
     */
    public function quote($data)
    {
        if(is_array($data))
        {
            $output = [];
            foreach ($data AS $value)
            {
                $output[] = $this->quote($value);
            }
            return implode(', ', $output);
        }
        return $this->db->quote($data);
    }

    /**
     * @param $request
     * @param array $args
     * @return bool|false|\PDOStatement
     */
    public function execute($request, array $args = [])
    {
        $db = $this->db;
        if(!empty($args)){
            $this->statement = $db->prepare($request);
            $this->statement->execute($args);
        }
        else
        {
            $this->statement = $db->query($request);
        }
        return $this->statement;
    }

    /**
     * @param $query
     * @param $amount
     * @param int $offset
     * @return string
     */
    public function limit($query, $amount, $offset = 0)
    {
        $offset = max(0, intval($offset));

        if ($amount === null)
        {
            if (!$offset)
            {
                return $query;
            }
            $amount = 1000000;
        }
        $amount = max(1, intval($amount));

        return "$query\nLIMIT $amount" . ($offset ? " OFFSET $offset" : '');
    }
    /**
     * disconnect BDD
     */
    public function disconnect()
    {
        $this->db = null;
    }
    /**
     * @param $table
     * @param array $values
     * @param array $otherValues
     * @return int
     */
    public function insert($table, array $values, array $otherValues = [])
    {
        if(!$values)
        {
            return 0;
        }
        $columns = [];
        foreach ($values as $key => $value)
        {
            $columns[] = $key;
        }
        $otherColumns['Columns'] = [];
        $otherColumns['value'] = [];
        if(!empty($otherValues))
        {
            foreach ($otherValues as $otherKey => $otherValue)
            {
                $otherColumns['Columns'][] = $otherKey;
                $otherColumns['value'][] = $otherValue;
            }
        }
        $outerColumns = !empty($otherColumns['Columns']) ? ', ' . implode(', ', $otherColumns['Columns']) : '';
        $otherValues = !empty($otherColumns['value']) ? ', ' . implode(', ', $otherColumns['value']) : '';
        $request = "INSERT INTO " . $table .
            "(" . implode(', ', $columns) . $outerColumns . ") VALUE(:" . implode(', :', $columns) . $otherValues .  ');' ;
        $res = $this->execute($request, $values);
        return $res->rowCount();
    }

    /**
     * @param $table
     * @param array $cols
     * @param $where
     * @param array $params
     * @param string $modifier
     * @param string $order
     * @param int $limit
     * @return int
     */
    public function update($table, array $cols, $where, $params = [], $modifier = '', $order = '', $limit = 0)
    {
        if (!$cols)
        {
            return 0;
        }

        $sqlValues = [];
        $bind = [];
        foreach ($cols AS $col => $value)
        {
            $bind[] = $value;
            $sqlValues[] = "`$col` = ?";
        }

        $bind = array_merge($bind, is_array($params) ? $params : [$params]);

        $res = $this->execute(
            "UPDATE $modifier `$table` SET " . implode(', ', $sqlValues)
            . ' WHERE ' . ($where ? $where : '1=1')
            . ($order ? " ORDER BY $order" : '')
            . ($limit ? ' LIMIT ' . intval($limit) : ''),
            $bind
        );
        return $res->rowCount();
    }

    /**
     * @param $table
     * @param $where
     * @param array $params
     * @param string $modifier
     * @param string $order
     * @param int $limit
     * @return int
     */
    public function delete($table, $where, $params = [], $modifier = '', $order = '', $limit = 0)
    {
        $res = $this->execute(
            "DELETE $modifier FROM `$table` WHERE " . ($where ? $where : '1=1')
            . ($order ? " ORDER BY $order" : '')
            . ($limit ? ' LIMIT ' . intval($limit) : ''),
            $params
        );
        return $res->rowCount();

    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * @param $table
     * @return mixed|null
     */
    public function getPrimaryKey($table)
    {
        $execute = $this->execute("SELECT *  FROM " . $table);
        for ($i = 0; $i < $execute->columnCount(); $i++) {
            $meta = $execute->getColumnMeta($i);
            if(in_array('primary_key', $meta['flags']))
            {
                return $meta['name'];
            }
        }
        return null;
    }

    /**
     * @param $shortName
     * @return Entity
     */
    public function getCreate($shortName)
    {
        return $this->instantiateEntity($shortName);
    }

    /**
     * @param array $where
     * @return array
     */
    public function getWhereUpdateDelete(array $where)
    {
        $output = [];
        foreach ($where as $whereKey => $whereValue)
        {
            $operator = $whereValue[1];
            $condition = $whereValue[0] . " " . $operator . " " . $whereValue[2];
            $output[] = $condition;
        }
        return $output;
    }
}