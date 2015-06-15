<?php
namespace CT\NFA;

class State {

    /**
     * The state name
     *
     * @var string
     */
    protected $name;

    /**
     * If a state is for start
     *
     * @var bool
     */
    protected $isStart = false;

    /**
     * If a state is final
     */
    protected $isFinal = false;

    /**
     * Directions
     *
     * @var State[]
     */
    protected $directions = [];

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name of the state
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isStart()
    {
        return $this->isStart;
    }

    /**
     * Set the state as a starting one
     *
     * @param bool $value
     */
    public function setStart($value = true)
    {
        $this->isStart = $value;
    }

    /**
     * Check if the state is final
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->isFinal;
    }

    /**
     * Set a state as a final one
     *
     * @param bool $value
     */
    public function setFinal($value = true)
    {
        $this->isFinal = $value;
    }

    /**
     * Add a new direction from the state
     *
     * @param State $state
     * @param $symbol
     */
    public function to(State $state, $symbol)
    {
        $this->directions[$symbol][] = $state;
    }

    /**
     * Get paths
     *
     * @return State[]
     */
    public function getPaths()
    {
        return $this->directions;
    }

    /**
     * Get directions for a given character
     *
     * @param $symbol
     * @return State[]
     */
    public function getDirections($symbol = null)
    {
        return isset($this->directions[$symbol]) ? $this->directions[$symbol] : [];
    }

    /**
     * Export an finite automata from the first state to the last
     *
     * @param State $state
     * @return array
     */
    public static function export(State $state, $isRoot = true)
    {
        static $data = [];
        static $states;

        foreach ($state->getPaths() as $char => $path) {
            $states[$state->getName()] = $state;
            foreach ($path as $dir) {
                $states[$dir->getName()] = $dir;
                $path = $state->getName() . '|' . $char . '|' . $dir->getName();
                if ( ! isset($data[$path])) {
                    $data[$path] = '';
                    self::export($dir, false);
                }
            }
        }

        if ( ! $isRoot) {
            return [];
        }

        $links = [];
        foreach ($data as $link => $val) {
            $links[] = explode('|', $link);
        }

        $start = [];
        $end = [];
        $statesName = [];

        array_walk($states, function (State $state) use (&$start, &$end, &$statesName) {
            $statesName[] = (string) $state;
            if ($state->isStart()) {
                $start[] = (string) $state;
            }
            if ($state->isFinal()) {
                $end[] = (string) $state;
            }
        });

        return json_encode(['states' => $statesName, 'first_states' => $start, 'final_states' => $end, 'rules' => $links], JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}