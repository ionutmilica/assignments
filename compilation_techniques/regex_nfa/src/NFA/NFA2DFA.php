<?php
namespace CT\NFA;

class NFA2DFA {

    /**
     * @param State[] $inputStates
     * @return array
     */
    public function closure(array $inputStates)
    {
        $output = $inputStates;

        while (true) {
            $statesToAdd = [];
            foreach ($output as $state) {
                foreach ($state->getDirections(null) as $path) {
                    if ( ! in_array($path, $output) && ! in_array($path, $statesToAdd)) {
                        $statesToAdd[] = $path;
                    }
                }
            }
            if (count($statesToAdd) == 0) break;

            $output = array_merge($output, $statesToAdd);
        }

        usort($output, function($a, $b) {
            return strcmp($a->getName(), $b->getName()) > 0;
        });

        return $output;
    }

    /**
     * @param $inputStates
     * @param $symbol
     * @return array
     */
    public function visit($inputStates, $symbol)
    {
        $states = [];

        foreach ($inputStates as $state) {
            foreach ($state->getDirections($symbol) as $path) {
                $states[] = $path;
            }
        }

        return $this->closure($states);
    }

    protected $idx = 0;
    protected $start = null;
    protected $states = [];
    protected $st = [];
    protected $nid = [];
    protected $nid_id = 1;

    /**
     * Construct a new nfa
     *
     * @param $state
     */
    function make($state)
    {
        $stateName = toStr($state);
        if (!isset($this->states[$stateName])) {
            $this->nid[$stateName] = (string) ($this->nid_id++);
            $this->states[$stateName] = new State($this->nid[$stateName]);
        }

        if ($this->idx == 0) {
            $this->start = $this->states[$stateName];
            $this->start->setStart();
        }
        $this->idx++;

        $input = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));

        foreach ($input as $char) {
            $closure = $this->visit($state, $char);
            $name = toStr($closure);
            $isAccepting = false;
            array_walk($closure, function (State $state) use (&$isAccepting) {
                if ($state->isFinal()) $isAccepting = true;
            });

            if ($name != '') {
                if (isset($this->states[$name])) {
                    $this->states[$stateName]->to($this->states[$name], $char);
                    $this->states[$stateName]->setFinal($isAccepting);
                } else {
                    $this->nid[$name] = (string) ($this->nid_id++);
                    $this->states[$name] = new State($this->nid[$name]);
                    $this->states[$stateName]->to($this->states[$name], $char);
                    $this->states[$name]->setFinal($isAccepting);
                    $this->make($closure);
                }
            }
        }
    }

    /**
     * Get start
     *
     * @return null
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get states
     *
     * @return State[]
     */
    public function getStates()
    {
        $states = [];
        foreach ($this->states as $state) {
            $states[(string)$state->getName()] = $state;
        }
        return $states;
    }

    public function getSt()
    {
        return $this->st;
    }
}

function printAS(array $states) {
    $list = [];
    foreach ($states as $state) {
        $list[] = $state->getName();
    }
    var_dump($list);
}

function toStr(array $states) {
    $list = [];
    foreach ($states as $state) {
        $list[] = $state->getName();
    }
    return implode(',', $list);
}