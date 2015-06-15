<?php
namespace CT\NFA;

use CT\NFA\Log\LoggerInterface;

class Factory {

    /**
     * States used for the NFA
     *
     * @var State[]
     */
    protected static $states = [];

    /**
     * @param State[] $states
     * @param Rule[] $rules
     * @param LoggerInterface $logger
     * @return NFA
     */
    public static function make(array $states, array $rules = [], LoggerInterface $logger = null)
    {
        self::$states = $states;
        self::transformRules($rules);

        $nfa = new NFA();
        $nfa->attachLogger($logger);
        $nfa->setStates(self::$states);
        $nfa->setStartingStates();

        return $nfa;
    }

    /**
     * Transform the rules into state links
     *
     * @param Rule[] $rules
     */
    protected static function transformRules($rules)
    {
        foreach ($rules as $rule) {
            $source = self::$states[$rule->getSource()];
            $destination = self::$states[$rule->getDestination()];
            $source->to($destination, $rule->getSymbol());
        }
    }
}