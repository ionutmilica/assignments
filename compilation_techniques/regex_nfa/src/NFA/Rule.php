<?php
namespace CT\NFA;

class Rule {

    /**
     * Source state name
     *
     * @var mixed
     */
    private $source;

    /**
     * Symbol used to describe the change of the state
     *
     * @var mixed
     */
    private $symbol;

    /**
     * Destination state name
     *
     * @var mixed
     */
    private $destination;

    /**
     * @param $source
     * @param $symbol
     * @param $destination
     */
    public function __construct($source, $symbol, $destination)
    {
        $this->source = $source;
        $this->symbol = $symbol;
        $this->destination = $destination;
    }

    /**
     * Get source state name from the rule
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get destination state name from the rule
     *
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Get symbol from the rule
     *
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }
}