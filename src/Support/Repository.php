<?php

namespace Honeybadger\Support;

class Repository implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return array
     */
    public function set(string $key, $value) : array
    {
        $this->items[$key] = $value;

        return $this->items;
    }

    /**
     * @param  string   $key
     * @param  mixed  $value
     * @return array
     */
    public function __set(string $key, $value) : array
    {
        return $this->set($key, $value);
    }

    /**
     * @return array
     */
    public function all() : array
    {
        return $this->items;
    }

    /**
     * @param  string|int $offset
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param  int|string  $offset
     * @return void
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @param  int|string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value) : void
    {
        $this->items[$offset] = $value;
    }

    /**
     * @param  int|string  $offset
     * @return void
     */
    public function offsetUnset($offset) : void
    {
        unset($this->items[$offset]);
    }
}
