<?php

namespace Frozzare\Redirects;

class Rule
{
    /**
     * Country value.
     *
     * @var array
     */
    public $country = [];

    /**
     * Force value.
     *
     * @var bool
     */
    public $force = false;

    /**
     * From value.
     *
     * @var string
     */
    public $from = '';

    /**
     * Language value.
     *
     * @var array
     */
    public $language = [];

    /**
     * Params value.
     *
     * @var array
     */
    public $params = [];

    /**
     * Status vlaue.
     *
     * @var int
     */
    public $status = 301;

    /**
     * To value.
     *
     * @var string
     */
    public $to = '';

    /**
     * Rule constructor.
     *
     * @param array $fields
     */
    public function __construct($fields = [])
    {
        foreach ($fields as $key => $value) {
            if (isset($this->$key)) {
                $this->$key = $value;
            }
        }
    }
}
