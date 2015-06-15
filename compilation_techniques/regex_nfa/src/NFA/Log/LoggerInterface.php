<?php
namespace CT\NFA\Log;


use CT\NFA\State;

interface LoggerInterface {

    /**
     * Add the log
     *
     * @param State $state
     * @param $symbol
     * @param $parent
     * @param $idx
     */
    public function log(State $state, $symbol, $parent, $idx);

    /**
     * Display the logs
     *
     * @return mixed
     */
    public function display();
}