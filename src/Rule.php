<?php

namespace Frozzare\Redirects;

use Countable;

class Rule implements Countable
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [
        'country'  => [],
        'force'    => false,
        'from'     => '',
        'language' => [],
        'params'   => [],
        'status'   => 301,
        'to'       => '',
    ];

    /**
     * Create a new rule.
     *
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_merge($this->items, $items);
    }

    /**
     * Count rule values.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Get rule items.
     *
     * @return array
     */
    public function items()
    {
        return $this->items;
    }

    /**
    * Get a rule value.
    *
    * @param  string $key
    *
    * @return mixed
    */
    public function &__get($key)
    {
        return $this->items[$key];
    }

   /**
    * Determine if the given rule value exists.
    *
    * @param  string $key
    *
    * @return bool
    */
    public function __isset($key)
    {
        return isset($this->items[$key]);
    }

   /**
    * Set a rule value.
    *
    * @param  string $key
    *
    * @param  mixed  $value
    */
    public function __set($key, $value)
    {
        $this->items[$key] = $value;
    }

   /**
    * Unset a container value.
    *
    * @param  string $key
    */
    public function __unset($key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
    }
}
