<?php
namespace CT\NFA;

class Reader {

    /**
     * Json data as array
     *
     * @var array
     */
    protected $data = [];

    /**
     * Cached states
     *
     * @var State[] $states
     */
    protected $states = null;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->data = json_decode(file_get_contents($file), true);
    }

    /**
     * Get final states name
     *
     * @return mixed
     */
    public function getStartStates()
    {
        return $this->data['first_states'];
    }

    /**
     * Get final states name
     *
     * @return mixed
     */
    public function getFinalStates()
    {
        return $this->data['final_states'];
    }

    /**
     * Get links between states
     *
     * @return Rule[]
     */
    public function getRules()
    {
        $rules = [];

        foreach ($this->data['rules'] as $rule) {
            $rules[] = new Rule($rule[0], $rule[1], $rule[2]);
        }

        return $rules;
    }

    /**
     * Get states
     *
     * @return array
     */
    public function getStates()
    {
        if ( ! is_null($this->states)) {
            return $this->states;
        }

        foreach ($this->data['states'] as $state) {
            $this->states[$state] = new State($state);
            if (in_array($state, $this->getStartStates())) {
                $this->states[$state]->setStart();
            }
            if (in_array($state, $this->getFinalStates())) {
                $this->states[$state]->setFinal();
            }
        }

        return $this->states;
    }

}