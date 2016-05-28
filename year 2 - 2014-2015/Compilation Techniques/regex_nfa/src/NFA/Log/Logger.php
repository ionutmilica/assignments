<?php
namespace CT\NFA\Log;

use CT\NFA\State;

class Logger implements LoggerInterface {

    /**
     * Holds all the logs
     *
     * @var array
     */
    protected $logs = [];

    /**
     * Add the log
     *
     * @param State $state
     * @param $symbol
     * @param $parent
     * @param $idx
     */
    public function log(State $state, $symbol, $parent, $idx)
    {
        $this->logs[$idx][] = [
            'state' => $state,
            'symbol' => $symbol,
            'parent' => $parent,
        ];
    }

    /**
     * Displays the log
     *
     * @param null $level
     * @param int $tail
     * @return array
     */
    protected function getRealLogs($level = null, $tail = -1)
    {
        $found = [];
        if ($level < 0) {
            return [];
        }
        if ($level === null) {
            $level = count($this->logs) - 1;
        }
        foreach ($this->logs[$level] as $pos => $log) {
            if ($tail == -1) {
                $condition = $log['state']->isFinal();
            } else {
                $condition = $pos == $tail;
            }

            if ($condition) {
                $found[] = $log;
                $found = array_merge($found, $this->getRealLogs($level - 1, $log['parent']));
            }
        }
        return $found;
    }

    /**
     *
     */
    public function display()
    {
        $logs = array_reverse($this->getRealLogs());

        foreach ($logs as $i => $log) {
            if ($i == 0) {
                echo ($log['state']->getName());
            } else {
                echo ' -- <sup>'.$log['symbol'].'</sup> -- ' .($log['state']->getName());
            }
        }
    }
}