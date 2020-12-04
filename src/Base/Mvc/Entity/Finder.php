<?php
namespace Base\Mvc\Entity;

class Finder
{
    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @var DateBase
     */
    protected $database;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $with = [];

    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var array
     */
    protected $order = null;

    protected $limit = null;
    protected $offset = 0;
    protected $structure;
    protected $values = [];
    protected $joins = [];
    protected $aliasCounter = 1;

    /**
     * Finder constructor.
     * @param DateBase $database
     * @param $structure
     */
    public function __construct(DateBase $database, $structure)
    {
        $this->database = $database;
        $this->structure = $structure;
    }

    /**
     * @return mixed
     */
    public function fetch()
    {
        $query = $this->getQuery();
        $database = $this->database;
        $execute = $database->execute($query);
        $req = $execute->fetchAll(\PDO::FETCH_NUM);
        $output = [];
        $map = $this->getHydrationMap();
        foreach ($req as $k => $v)
        {
            $row = $this->fetchAliasGrouped($v);
            if (!$row)
            {
                return null;
            }
            $entity = $this->database->HydrateFromGrouped($row, $map);
            $output[$entity->getIdentifier()] = $entity;
        }
        return $this->database->getBasicCollection($output);
    }
    /**
     * @return mixed
     */
    protected function getHydrationMap()
    {
        $map = [];
        foreach ($this->joins AS $name => $join)
        {
            if (empty($join['fetch']))
            {
                continue;
            }
            $map[$name] = [
                'alias' => $join['alias'],
                'entity' => $join['entity'],
                'parentRelation' => $join['parentRelation'],
                'relation' => $join['relation'],
                'relationValue' => $join['relationValue']
            ];
        }

        $map = array_reverse($map, true);
        $map[''] = [
            'alias' => $this->structure->table,
            'entity' => $this->structure->shortName,
            'parentRelation' => '',
            'relation' => '',
            'relationValue' => null
        ];
        return $map;
    }
    /**
     * @return int
     */
    public function total()
    {
        $query =  $this->getQuery(['countOnly' => true]);
        $execute = $this->database->execute($query);
        return $execute->fetch()[0];
    }

    /**
     * @return Entity|null
     */
    public function fetchOne()
    {
        $query = $this->getQuery();

        $database = $this->database;
        $execute = $database->execute($query);
        $result = $execute->fetch(\PDO::FETCH_NUM);
        if (!$result)
        {
            return null;
        }
        $rows = $this->fetchAliasGrouped($result);
        if (!$rows)
        {
            return null;
        }
        $map = $this->getHydrationMap();
        $entity = $this->database->HydrateFromGrouped($rows, $map);

        return $entity;
    }
    /**
     * @param $row
     * @return array
     */
    protected function fetchAliasGrouped($row)
    {
        $rows = [];
        foreach ($row as $key => $data)
        {
            $ColumnMeta = $this->database->execute($this->getQuery(), $this->values)->getColumnMeta($key);

            $rows[$ColumnMeta['table']][$ColumnMeta['name']] = $data;
        }
        return $rows;
    }
    /**
     * @param $row
     * @return array
     */
    protected function HydrationRelation($row)
    {
        $map = [
            '_relation' => [],
            '_value' => []
        ];
        foreach ($row as $key => $value)
        {
            if(isset($this->with[$key]))
            {
                $map['_relation'][$key] = $value;
            }
            else
            {
                $map['_value'] = $value;
            }
        }
        return $map;
    }
    /**
     * @param $condition
     */
    protected function writeSqlCondition($condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * @param $condition
     * @param null $operator
     * @param null $value
     * @return $this
     * @throws \Exception
     */
    public function where($condition, $operator = null, $value = null)
    {
        $argCount = func_num_args();
        switch ($argCount)
        {
            case 1: $condition = $this->buildCondition($condition); break;
            case 2: $condition = $this->buildCondition($condition, $operator); break;
            case 3: $condition = $this->buildCondition($condition, $operator, $value); break;

            default: $condition = call_user_func_array([$this, 'buildCondition'], func_get_args());
        }
        $this->writeSqlCondition($condition);

        return $this;
    }

    /**
     * @param $condition
     * @param null $operator
     * @param null $value
     * @return string
     * @throws \Exception
     */
    protected function buildCondition($condition, $operator = null, $value = null)
    {
        $argCount = func_num_args();
        if($argCount == 1)
        {
            $conditions = [];
            if ($this->arrayRepresentsCondition($condition))
            {
                $conditions[] = $this->buildConditionFromArray($condition);
            }
            else
            {
                foreach ($condition AS $name => $value)
                {
                    if (is_int($name) && is_array($value))
                    {
                        $conditions[] = $this->buildConditionFromArray($value);
                    }
                    else
                    {
                        $conditions[] = $this->buildCondition($name, $value);
                    }
                }
            }
            return $conditions ? implode(' AND ', $conditions) : "1";
        }
        $lhs = $this->columnSqlName($condition);
        if ($argCount == 2)
        {
            $value = $operator;
            $operator = '=';
        }
        $operator = strtoupper($operator);
        switch ($operator)
        {
            case '=':
            case '<>':
            case '!=':
            case '>':
            case '>=':
            case '<':
            case '<=':
            case 'LIKE':
            case 'NOT LIKE':
            case 'BETWEEN':
                break;

            default:
                throw new \InvalidArgumentException("Opérateur $operator n'est pas valable");
        }
        $hasValue = true;
        if ($value === null)
        {
            switch ($operator)
            {
                case '=':
                    $operator = 'IS NULL';
                    $hasValue = false;
                    break;

                case '<>':
                case '!=':
                    $operator = 'IS NOT NULL';
                    $hasValue = false;
                    break;
            }
        }
        if (!$hasValue)
        {
            return "$lhs $operator";
        }
        $quoted = $this->database->quote($value);
        if (!is_array($value))
        {
            switch ($operator)
            {
                case 'BETWEEN':
                    throw new \InvalidArgumentException("Entre les opérateurs, il faut des valeurs de tableau");
                    break;
            }

            return "$lhs $operator $quoted";
        }
        switch ($operator)
        {
            case '=':
                if (strlen($quoted))
                {
                    $condition = "$lhs IN (" . $quoted . ')';
                }
                else
                {
                    $condition = '0'; // can't match
                }
                break;

            case '<>':
            case '!=':
                if (strlen($quoted))
                {
                    $condition = "$lhs NOT IN (" . $quoted . ')';
                }
                else
                {
                    $condition = '1';
                }
                break;
            case 'LIKE':
            case 'NOT LIKE':
                $parts = [];
                foreach ($value AS $v)
                {
                    if (strlen($v))
                    {
                        $parts[] = "$lhs $operator " . $this->database->quote($v);
                    }
                }
                if ($parts)
                {
                    $condition = implode($operator == 'LIKE' ? ' OR ' : ' AND ', $parts);
                }
                else
                {
                    $condition = '1';
                }
                break;
            case 'BETWEEN';
                $min = $value[0];
                $max = $value[1];
                $condition = "$lhs BETWEEN " . $this->database->quote($min) . ' AND ' . $this->database->quote($max);
                break;

            default:
                throw new \InvalidArgumentException("L'opérateur $operator n'est pas valable avec un tableau de valeurs");
        }
        return $condition;
    }

    /**
     * @param $field
     * @return array
     * @throws \Exception
     */
    public function resolveFieldToTableAndColumn($field)
    {
        $parts = explode('.', $field);
        if(count($parts) == 1)
        {
            $field = $this->getColumnAlias($this->structure, $field);
            if (!isset($this->structure->columns[$field]))
            {
                throw new \InvalidArgumentException("Unknown column $field on {$this->structure->shortName}");
            }

            return [$this->structure->table, $field];
        }
        $column = array_pop($parts);
        $joinInfo = $this->buildWith(implode('.', $parts), false);

        $joinStructure = $joinInfo['structure'];
        $column = $this->getColumnAlias($joinStructure, $column);
        if (!isset($joinStructure->columns[$column]))
        {
            throw new \InvalidArgumentException("Colonne inconnue $column sur la relation $joinInfo[relation] ({$joinStructure->shortName})");
        }

        return [$joinInfo['alias'], $column];
    }

    /**
     * @param $column
     * @return string
     * @throws \Exception
     */
    public function columnSqlName($column)
    {
        list($table, $field) = $this->resolveFieldToTableAndColumn($column);
        return "`$table`.`$field`";
    }
    /**
     * @param Structure $structure
     * @param $column
     * @return mixed
     */
    protected function getColumnAlias(Structure $structure, $column)
    {
        if ($structure->columnAliases && isset($structure->columnAliases[$column]))
        {
            $column = $structure->columnAliases[$column];
        }

        return $column;
    }

    /**
     * @param array $value
     * @return mixed|string
     * @throws \Exception
     */
    protected function buildConditionFromArray(array $value)
    {
        switch (count($value))
        {
            case 1: return $this->buildCondition($value[0]);
            case 2: return $this->buildCondition($value[0], $value[1]);
            case 3: return $this->buildCondition($value[0], $value[1], $value[2]);
            default: return call_user_func_array([$this, 'buildCondition'], $value);
        }
    }
    /**
     * @param array $array
     * @return bool
     */
    protected function arrayRepresentsCondition(array $array)
    {
        if (!isset($array[0]))
        {
            return false;
        }

        foreach ($array AS $k => $null)
        {
            if (!is_int($k))
            {
                return false;
            }
        }

        if (is_array($array[0]))
        {
            return false;
        }

        return true;
    }

    /**
     * @param $name
     * @param bool $mustExist
     * @return $this
     * @throws \Exception
     */
    public function with($name, $mustExist = false)
    {
        if (is_array($name))
        {
            foreach ($name AS $join)
            {
                $this->buildWith($join, true, $mustExist);
            }
        }
        else
        {
            $this->buildWith($name, true, $mustExist);
        }

        return $this;
    }

    /**
     * @param array $conditionA
     * @param array|null $conditionB
     * @return $this
     */
    public function whereOr(array $conditionA, array $conditionB = null)
    {
        $args = $conditionB === null ? $conditionA : func_get_args();
        $conditions = [];
        foreach ($args AS $k => $arg)
        {
            if (is_array($arg) && $this->arrayRepresentsCondition($arg))
            {
                $conditions[] = $this->buildConditionFromArray($arg);
            }
            else if (is_array($arg))
            {
                $conditions[] = $this->buildCondition($arg);
            }
            else
            {
                throw new \InvalidArgumentException("Argument $k is not an array/FinderExpression");
            }
        }

        $this->writeSqlCondition("(" . implode(") OR (", $conditions) . ")");

        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function whereId($id)
    {
        $primaryKey = $this->structure->primaryKey;

        if (is_array($primaryKey) && count($primaryKey) === 1)
        {
            $primaryKey = reset($primaryKey);
        }

        if (is_array($primaryKey))
        {
            if (!is_array($id))
            {
                throw new \InvalidArgumentException("La clé primaire est composée, mais l'identification n'est pas donnée sous forme de tableau");
            }
            foreach ($primaryKey AS $i => $key)
            {
                if (array_key_exists($key, $id))
                {
                    $this->where([[$key, '=', $id[$key]]]);
                }
                else if (array_key_exists($i, $id))
                {
                    $this->where([[$key, '=', $id[$i]]]);
                }
                else
                {
                    throw new \InvalidArgumentException("Clé de tableau $key ou $i devant exister dans l'ID");
                }
            }
        }
        else
        {
            $this->where([[$primaryKey, '=', $id]]);
        }

        return $this;
    }

    /**
     * @param $name
     * @param bool $fetch
     * @param bool $fundamental
     * @param bool $mustExist
     * @return mixed|null
     * @throws \Exception
     */
    protected function buildWith($name, $fetch = false, $mustExist = false)
    {
        $parts = explode('.', $name);
        $partialName = '';
        $structure = $this->structure;
        $joinTable = $structure->table;
        $finalJoin = null;

        foreach ($parts AS $part)
        {

            $hasRelationValue = explode('|', $part, 2);
            if (isset($hasRelationValue[1]))
            {
                $relationValue = $hasRelationValue[1];
                $relationName = $hasRelationValue[0];
            }
            else
            {
                $relationName = $part;
                $relationValue = null;
            }

            if (empty($structure->relations[$relationName]))
            {
                throw new \LogicException("Relation ou alias inconnu $relationName consulté sur {$structure->table}");
            }
            $parentJoin = $partialName;
            $partialName = ($partialName ? $partialName . '.' : '') . $part;
            $relation = $structure->relations[$relationName];
            $relationStructure = $this->database->getEntityStructure($relation['entity']);
            if ($relationValue !== null)
            {
                if (empty($relation['key']))
                {
                    throw new \Exception("Tenter d'obtenir une valeur spécifique d'une relation qui ne la soutient pas");
                }
                $relation['type'] = Entity::TO_ONE;
            }

            if ($relation['type'] !== Entity::TO_ONE)
            {
                throw new \Exception("Ne rejoint que les relations de soutien TO_ONE actuellement");
            }
            if (isset($this->joins[$partialName]))
            {
                $finalJoin = $this->joins[$partialName];
                $joinTable = $finalJoin['alias'];
                $structure = $relationStructure;
                if ($fetch)
                {
                    $this->joins[$partialName]['fetch'] = true;
                }
                if ($mustExist)
                {
                    $this->joins[$partialName]['exists'] = true;
                }
                continue;

            }

            $alias = $relationStructure->table . '_' . $relationName . '_' . $this->aliasCounter++;

            $joinConditions = [];
            $conditions = $relation['conditions'];
            if (!is_array($conditions))
            {
                $conditions = [$conditions];
            }
            foreach ($conditions AS $condition)
            {
                if (is_string($condition))
                {
                    $joinConditions[] = "`$alias`.`$condition` = `$joinTable`.`$condition`";
                }
                else
                {
                    list($field, $operator, $value) = $condition;

                    if (count($condition) > 3)
                    {
                        $readValue = [];
                        foreach (array_slice($condition, 2) AS $v)
                        {
                            if ($v && $v[0] == '$')
                            {
                                $readValue[] = "`$joinTable`.`" . substr($v, 1) . '`';
                            }
                            else
                            {
                                $readValue[] = $this->database->quote($v);
                            }
                        }

                        $value = 'CONCAT(' . implode(', ', $readValue) . ')';
                    }
                    else if ($value instanceof \Closure)
                    {
                        $value = $value('join', $joinTable);
                    }
                    else if (is_string($value) && $value && $value[0] == '$')
                    {
                        $value = "`$joinTable`.`" . substr($value, 1) . '`';
                    }
                    else if (is_array($value))
                    {
                        if (!$value)
                        {
                            throw new \LogicException("Les conditions d'adhésion à un réseau exigent une valeur");
                        }

                        $value = '(' . $this->database->quote($value) . ')';
                    }
                    else
                    {
                        $value = $this->database->quote($value);
                    }

                    if ($field[0] == '$')
                    {
                        $fromJoinAlias = "`$joinTable`.`" . substr($field, 1) . '`';
                    }
                    else
                    {
                        $fromJoinAlias = "`$alias`.`$field`";
                    }

                    $joinConditions[] = "$fromJoinAlias $operator $value";
                }
            }
            if ($relationValue !== null)
            {
                $relation['key'] = $this->getColumnAlias($relationStructure, $relation['key']);
                $joinConditions[] = "`$alias`.`$relation[key]` = " . $this->database->quote($relationValue);
            }
            $this->joins[$partialName] = [
                'table' => $relationStructure->table,
                'structure' => $relationStructure,
                'alias' => $alias,
                'parentAlias' => $joinTable,
                'condition' => implode(' AND ', $joinConditions),
                'fetch' => $fetch,
                'exists' => $mustExist,

                'parentRelation' => $parentJoin,
                'relation' => $relationName,
                'relationValue' => $relationValue,
                'entity' => $relation['entity'],
            ];

            $joinTable = $alias;
            $structure = $relationStructure;
            $finalJoin = $this->joins[$partialName];
        }

        return $finalJoin;
    }

    /**
     * @param $field
     * @param string $direction
     * @return $this
     * @throws \Exception
     */
    public function order($field, $direction = 'ASC')
    {
        $direction = $direction ? strtoupper($direction) : 'ASC';

        if(is_array($field))
        {
            if (count($field) == 2 && isset($field[1]) && is_string($field[1]))
            {
                switch (strtoupper($field[1]))
                {
                    case 'ASC':
                    case 'DESC':
                        // this is ['column', 'ASC'] format
                        return $this->order($field[0], $field[1]);
                }
            }

            foreach ($field AS $entry)
            {
                if (is_array($entry))
                {
                    $this->order($entry[0], $entry[1] ?? $direction);
                }
                else
                {
                    $this->order($entry, $direction);
                }
            }
        }
        else
        {
            $lhs = $this->columnSqlName($field);
            $this->order = " ORDER BY " . $lhs . " " . $direction;
        }

        return $this;
    }
    /**
     * @param $page
     * @param $perPage
     * @param int $thisPageExtra
     *
     * @return Finder
     */
    public function limitByPage($page, $perPage, $thisPageExtra = 0)
    {

        $page = intval($page);
        if ($page < 1)
        {
            $page = 1;
        }

        $perPage = intval($perPage);
        if ($perPage < 1)
        {
            $perPage = 1;
        }

        $thisPageExtra = intval($thisPageExtra);
        if ($thisPageExtra < 0)
        {
            $thisPageExtra = 0;
        }

        $this->offset = ($page - 1) * $perPage;
        $this->limit = $perPage + $thisPageExtra;

        return $this;
    }

    /**
     * @param $limit
     * @param null $offset
     * @return $this
     */
    public function limit($limit, $offset = null)
    {

        $this->limit = $limit === null ? null : intval($limit);
        if ($offset !== null)
        {
            $this->offset = intval($offset);
        }

        return $this;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function fetchRaw(array $options = [], $type = 'All')
    {
        $results = $this->database->execute($this->getQuery($options));
        if($type == 'All')
        {
            return $results->fetchAll();
        }
        return $results->fetch();
    }

    /**
     * @param $column
     * @return mixed
     */
    public function fetchColumns($column)
    {
        if (is_array($column) && func_num_args() == 1)
        {
            $columns = $column;
        }
        else
        {
            $columns = func_get_args();
        }
        return $this->fetchRaw(['fetchOnly' => $columns]);

    }
    /**
     * @param $column
     * @return mixed
     */
    public function fetchColumn($column)
    {
        if (is_array($column) && func_num_args() == 1)
        {
            $columns = $column;
        }
        else
        {
            $columns = func_get_args();
        }
        return $this->fetchRaw(['fetchOnly' => $columns], 'one');

    }
    /**
     * @param array $options
     * @return string
     */
    protected function getQuery(array $options = [])
    {
        $structure = $this->structure;
        $table = $structure->table;
        if ($this->conditions)
        {
            $where = ' WHERE (' . implode(') AND (', $this->conditions) . ')';
        }
        else
        {
            $where = '';
        }
        $options = array_merge([
            'countOnly' => false,
            'fetchOnly' => null
        ], $options);
        $countOnly = $options['countOnly'];
        $fetchOnly = $options['fetchOnly'];
        if (is_array($fetchOnly))
        {

            foreach ($fetchOnly AS $key => $fetchValue)
            {
                $fetch[] = $fetchValue;
            }
        }
        else
        {
            $fetch[] = '`' . $table . '`.*';
        }
        $joins = [];
        foreach ($this->joins AS $join)
        {
            $joinType = $join['exists'] ? 'INNER' : 'LEFT';
            $joins[] = " $joinType JOIN `$join[table]` AS `$join[alias]` ON ($join[condition])";
            $fetch[] = "`$join[alias]`.*";
        }

        $limit = $this->limit;
        $offset = $this->offset;
        if ($countOnly)
        {
            return "
				SELECT COUNT(*)
				FROM $table
				" . implode("\n", $this->with) . "
				$where
			";
        }
        $q = $this->database->limit(
            'SELECT ' . implode(', ', $fetch) .
            ' FROM ' . $table  . implode("\n", $joins) .
            $where . ' ' .
            $this->order, $limit, $offset
        );
        return $q;
    }
}