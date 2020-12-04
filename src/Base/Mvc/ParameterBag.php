<?php

namespace Base\Mvc;
/**
 * Class ParameterBag
 * @package Base\Mvc
 *
 * @property array page
 */
class ParameterBag implements \ArrayAccess
{
    /**
     * @var array
     */
	protected $params;

    /**
     * ParameterBag constructor.
     * @param array $params
     */
	public function __construct(array $params = [])
	{
		$this->params = $params;
	}

    /**
     * @param mixed $key
     * @return mixed|null
     */
	public function offsetGet($key)
	{
		return isset($this->params[$key]) ? $this->params[$key] : null;
	}

    /**
     * @param $key
     * @return mixed|null
     */
	public function __get($key)
	{
		return $this->offsetGet($key);
	}

    /**
     * @param $key
     * @param null $fallback
     * @return mixed|null
     */
	public function get($key, $fallback = null)
	{
		return array_key_exists($key, $this->params) ? $this->params[$key] : $fallback;
	}

    /**
     * @param mixed $key
     * @param mixed $value
     */
	public function offsetSet($key, $value)
	{
		$this->params[$key] = $value;
	}

    /**
     * @param $key
     * @param $value
     */
	public function __set($key, $value)
	{
		$this->offsetSet($key, $value);
	}

    /**
     * @param mixed $key
     * @return bool
     */
	public function offsetExists($key)
	{
		return array_key_exists($key, $this->params);
	}

    /**
     * @param mixed $key
     */
	public function offsetUnset($key)
	{
		unset($this->params[$key]);
	}

    /**
     * @return array
     */
	public function params()
	{
		return $this->params;
	}
}