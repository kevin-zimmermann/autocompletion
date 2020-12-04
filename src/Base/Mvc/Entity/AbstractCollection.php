<?php

namespace Base\Mvc\Entity;

abstract class AbstractCollection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @var array
     */
	protected $entities = [];

	/**
	 * @var bool
	 */
	protected $populated = false;

	abstract protected function populateInternal();

	public function populate()
	{
		if (!$this->populated)
		{
			$this->populated = true;
			$this->populateInternal();
		}

		return $this;
	}

	public function toArray()
	{
		$this->populate();

		return $this->entities;
	}
	public function offsetGet($key)
	{
		return $this->entities[$key];
	}

	public function offsetSet($key, $value)
	{
		$this->entities[$key] = $value;
	}
    /**
     * @param callable $callback
     * @param bool $collectionOnEmpty If true, an empty plucking will return a collection; otherwise, an array
     *
     * @return array|ArrayCollection
     */
    public function pluck(\Closure $callback, $collectionOnEmpty = true)
    {
        $this->populate();

        $output = [];
        $newCollection = true;

        foreach ($this->entities AS $key => $entity)
        {
            $res = $callback($entity, $key);
            if (is_array($res))
            {
                $output[$res[0]] = $res[1];

                if (!($res[1] instanceof Entity))
                {
                    $newCollection = false;
                }
            }
        }

        if (!$output)
        {
            return $collectionOnEmpty ? new ArrayCollection([]) : [];
        }
        else
        {
            return $newCollection ? new ArrayCollection($output) : $output;
        }
    }

    public function pluckNamed($valueField, $keyField = null)
    {
        $i = 0;
        $f = function(Entity $e) use($keyField, $valueField, &$i)
        {
            if ($keyField !== null)
            {
                $key = $e->$keyField;
            }
            else
            {
                $key = $i;
                $i++;
            }

            $value = $e->$valueField;

            return [$key, $value];
        };

        // starts with upper case letter means pulling an entity so give a collection (by convention)
        $collectionOnEmpty = preg_match('/^[A-Z]/', $valueField);

        return $this->pluck($f, $collectionOnEmpty);
    }

	public function offsetExists($key)
	{
		return isset($this->entities[$key]);
	}

	public function offsetUnset($key)
	{
		unset($this->entities[$key]);
	}

	public function getIterator()
	{
		$this->populate();

		return new \ArrayIterator($this->entities);
	}

	public function count()
	{
		$this->populate();

		return count($this->entities);
	}

	public function keys()
	{
		$this->populate();

		return array_keys($this->entities);
	}

	public function first()
	{
		$this->populate();

		return reset($this->entities);
	}

	public function last()
	{
		$this->populate();

		return end($this->entities);
	}

	public function max($field)
    {
        return max($this->pluckNamed($field));
    }

    public function min($field)
    {
        return min($this->pluckNamed($field));
    }
    public function reverse()
    {
        return new ArrayCollection(array_reverse($this->entities));
    }
}