<?php
namespace CT\NFA;

use CT\NFA\Log\Logger;
use CT\NFA\Log\LoggerInterface;

class NFA {

    /**
     * NFA cursor position
     *
     * @var int
     */
    protected $cursorPos = 0;

    /**
     * Current states for the automaton
     *
     * @var State[]
     */
    protected $currentStates = [];

    /**
     * All automaton states
     *
     * @var array
     */
    protected $states = [];

    /**
     * Log data
     *
     * @var Logger
     */
    protected $logger = null;

    /**
     * Set first automaton state
     *
     */
    public function setStartingStates()
    {
        $states = $this->getStartingStates();

        foreach ($states as $state) {
            if ($this->logger) $this->logger->log($state, null, null, 0);
        }
        $this->currentStates = $states;
    }

    /**
     * Get starting states
     *
     * @return array
     */
    public function getStartingStates()
    {
        $states = [];

        foreach ($this->states as $state) {
            if ($state->isStart()) {
                $states[] = $state;
            }
        }
        return $states;
    }

    /**
     * Store all states in the automaton
     *
     * @param State[] $states
     */
    public function setStates(array $states)
    {
        $this->states = $states;
    }

    /**
     * Attach a logger to the afn
     * @param LoggerInterface $logger
     */
    public function attachLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Get nfa logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Read an char and change current states
     *
     * @param $char
     */
    public function readChar($char)
    {
        $this->currentStates = $this->getPathsForSymbol($this->currentStates, $char);
        $this->cursorPos++;
    }

    /**
     * Read a string
     *
     * @param $str
     */
    public function readString($str)
    {
        $len = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $this->readChar($str[$i]);
        }
    }

    /**
     * Check if NFA is in an accepting state
     *
     * @return bool
     */
    public function accepting()
    {
        $accepting = 0;

        array_walk($this->currentStates, function (State $state) use (&$accepting) {
            if ($state->isFinal()) $accepting++;
        });

        return $accepting > 0;
    }

    /**
     * Check if a word is valid for the NFA
     *
     * @param $text
     * @return bool
     */
    public function run($text)
    {
        $this->readString($text);

        return $this->accepting();
    }

    /**
     * Get letters
     *
     * @return array
     */
    public function getLetters()
    {
        $letters = [];
        foreach ($this->states as $state) {
            $keys = array_keys($state->getPaths());
            $let = array_diff($keys, $letters);
            $letters = array_merge($letters, $let);
        }
        return $letters;
    }

    /**
     * Make the automaton complete
     */
    public function complete()
    {
        $letters = $this->getLetters();

        $fakeState = new State('Fake');
        foreach ($letters as $letter) {
            $fakeState->to($fakeState, $letter);
        }

        $linked = false;
        foreach ($this->states as $state) {
            $toAdd = array_diff($letters, array_keys($state->getPaths()));
            foreach ($toAdd as $dir) {
                $state->to($fakeState, $dir);
                $linked = true;
            }
        }

        if ($linked) {
            $this->states[$fakeState->getName()] = $fakeState;
        }
    }

    /**
     *
     */
    protected $finals = [];
    protected $others = [];

    /**
     * Transform the automaton into the minimal one using Myhill-Nerode theorem
     */
    public function toMinimal()
    {
        $this->complete();

        /**
         * Check if its marked
         *
         * @param $pairs
         * @param $a
         * @param $b
         * @return bool
         */
        function isMarked($pairs, $a, $b) {
            if ($a == $b) false;
            foreach ($pairs as $pair) {
                list($_a, $_b, $marked) = $pair;
                $_a = $_a->getName();
                $_b = $_b->getName();
                if ($a == $_a && $b == $_b && $marked) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Check if group has final states
         *
         * @param $group
         * @return bool
         */
        function groupHasFinal($group) {
            foreach ($group as $g) {
                if ($g->isFinal()) return true;
            }
            return false;
        }

        /**
         * Check if group has starting states
         *
         * @param $group
         * @return bool
         */
        function groupHasStart($group) {
            foreach ($group as $g) {
                if ($g->isStart()) return true;
            }
            return false;
        }

        /**
         * Get group for this
         *
         * @param $groups
         * @param $state
         * @return bool|int|string
         */
        function whichGroup(array $groups, $state) {
            foreach ($groups as $gid => $states) {
                if (in_array($state, $states)) {
                    return (string) $gid;
                }
            }
            return false;
        }

        foreach ($this->states as $state) {
            if ($state->isFinal()) {
                $this->finals[(string)$state->getName()] = $state;
            } else {
                $this->others[(string)$state->getName()] = $state;
            }
        }

        $origStates = $this->states;
        $states = array_values($this->states);

        $pairs = [];

        for ($i = 0; $i < count($states) - 1; $i++) {
            for ($j = count($states) - 1; $j >= 1 + $i; $j--) {
                $pairs[] = [$states[$i], $states[$j], false];
            }
        }

        // First wave
        foreach ($pairs as $key => $pair) {
            list($a, $b, $mark) = $pair;
            if (!$this->sameGroup($a->getName(), $b->getName())) {
                $pairs[$key][2] = true;
            }
        }

        // Last wave
        do {
            $hasMarks = false;
            foreach ($pairs as $key => $pair) {
                list($a, $b, $mark) = $pair;
                if ($mark) continue;
                foreach ($this->getLetters() as $letter) {
                    $a_dir = $a->getDirections($letter)[0];
                    $b_dir = $b->getDirections($letter)[0];


                    if (isMarked($pairs, $a_dir->getName(), $b_dir->getName()) ||
                        isMarked($pairs, $b_dir->getName(), $a_dir->getName())
                       ) {
                        $pairs[$key][2] = true;
                        $hasMarks = true;
                        break;
                    }
                }
            }
        } while ($hasMarks);

        $grouped = [];
        foreach ($pairs as $pair) {
            if ($pair[2] == false) {
                foreach ($grouped as $gid => $group) {
                    foreach ([$pair[0], $pair[1]] as $state) {
                        if (in_array($state, $group)) {
                            $grouped[$gid] = array_unique(array_merge($grouped[$gid], [$pair[0], $pair[1]]));
                            continue;
                        }
                    }
                }
                $grouped[] = [$pair[0], $pair[1]];
                unset($origStates[$pair[0]->getName()], $origStates[$pair[1]->getName()]);
            }
        }

        // Prepare groups
        $origStates = array_values($origStates);
        foreach ($origStates as $key => $state) {
            if ($state->getName() != 'Fake') {
                $origStates[$key] = [$state];
            } else unset($origStates[$key]);
        }
        $grouped = array_merge($grouped, $origStates);

        $newStates = [];
        // Reconstruction - Step 1
        foreach ($grouped as $gid => $group) {
            $group = is_array($group) ? $group : [$group];
            $newState = new State((string)$gid);
            $newState->setFinal(groupHasFinal($group));
            $newState->setStart(groupHasStart($group));
            $newStates[(string)$gid] = $newState;
        }

        foreach ($grouped as $gid => $group) {
            $state = is_array($group) ? $group[0] : $group;
            foreach ($this->getLetters() as $letter) {
                $dir = $state->getDirections($letter)[0];
                if ($dir->getName() != 'Fake') {
                    $newStates[(string)$gid]->to($newStates[whichGroup($grouped, $dir)], $letter);
                }
            }
        }

        $this->states = $newStates;
        $this->setStartingStates();
    }

    /**
     * Check if 2 states are in the same group
     *
     * @param $a
     * @param $b
     * @return bool
     */
    protected function sameGroup($a, $b)
    {
        if ($a === $b) {
            return true;
        }
        if (isset($this->finals[$a]) && isset($this->finals[$b])) {
            return true;
        }
        if (isset($this->others[$a]) && isset($this->others[$b])) {
            return true;
        }
        return false;
    }

    /**
     * Get all possible paths for the current states
     * and character
     *
     * @param $paths State[]
     * @param $symbol
     * @return State[]
     */
    protected function getPathsForSymbol($paths, $symbol)
    {
        $newPaths = [];
        foreach ($paths as $pos => $path) {
            $directions = $path->getDirections($symbol);
            foreach ($directions as $direction) {
                if ( ! in_array($direction, $newPaths)) $newPaths[] = $direction;
                if ($this->logger) $this->logger->log($direction, $symbol, $pos, $this->cursorPos + 1);
            }
        }
        return $newPaths;
    }

}
