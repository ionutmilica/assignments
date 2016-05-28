<?php
namespace CT\NFA\Regex;

class Tuple {

    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get tuple contents
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}