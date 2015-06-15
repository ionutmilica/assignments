<?php

class Algorithm
{
    /**
     * @var Grammar
     */
    private $grammar;

    /**
     * Multimea first
     *
     * @var array
     */
    protected $first = [];

    /**
     * Multimea follow
     *
     * @var array
     */
    protected $follow = [];

    /**
     * Multimea null
     *
     * @var array
     */
    protected $nullable = [];

    /**
     * @param Grammar $grammar
     */
    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    public function compute()
    {
        foreach ($this->grammar->getSymbols() as $symbol) {
            $this->first[$symbol] = [];
            $this->follow[$symbol] = [];
            $this->nullable[$symbol] = false;
        }

        foreach ($this->grammar->getTerminals() as $terminal) {
            $this->first[$terminal] = [$terminal];
        }

        $this->computeNullable();
        $this->computeFirst();
        $this->computeFollow();
    }

    /**
     * Compute the nullable symbols
     */
    public function computeNullable()
    {
        $rules = $this->grammar->getRules();

        foreach ($rules as $production) {
            $X = $production['X'];
            if ( ! $this->canInstantiate($production['data'])) {
                $this->nullable[$X] = true;
            }
        }

        do {
            $wasModified = false;

            foreach ($this->grammar->getRules() as $production) {
                $data = $production['data'];
                $X = $production['X'];

                $nullable = true;
                foreach ($data as $symbol) {
                    if ( ! $this->isNullable($symbol)) {
                        $nullable = false;
                        break;
                    }
                }
                if ($nullable == true && $this->nullable[$X] == false) {
                    $this->nullable[$X] = true;
                    $wasModified = true;
                }
            }
        } while ($wasModified);
    }

    /**
     * Compute first set
     */
    public function computeFirst()
    {
        do {
            $wasModified = false;

            foreach ($this->grammar->getRules() as $production) {
                $data = $production['data'];
                $n = count($data);
                $X = $production['X'];

                if ( ! $this->canInstantiate($data)) {
                    continue;
                }

                $this->union($this->first[$X], $this->first[$data[0]], $wasModified);

                for ($i = 1; $i < $n; $i++) {
                    if ($this->areNullable(0, $i - 1, $data)) {
                        $this->union($this->first[$X], $this->first[$data[$i]], $wasModified);
                    }
                }
            }

        } while ($wasModified);
    }

    /**
     * Compute follow set
     */
    public function computeFollow()
    {
        $rules = $this->grammar->getRules();
        $this->follow[$rules[0]['X']] = ['$'];

        do {
            $wasModified = false;

            foreach ($this->grammar->getRules() as $production) {
                $data = $production['data'];
                $n = count($data);
                $X = $production['X'];
                for ($i = 0; $i < $n; $i++) {
                    $this->ensureList($this->follow, $data[$i]);

                    if ($this->areNullable($i + 1, $n, $data)) {
                        if ($this->grammar->isSymbol($data[$i])) {
                            $this->union($this->follow[$data[$i  ]], $this->follow[$X], $wasModified);
                        }
                    }
                    for ($j = $i + 1; $j < $n; $j++) {
                        $this->ensureList($this->follow, $data[$i]);
                        $this->ensureList($this->first, $data[$j]);

                        if ($this->areNullable($i, $j, $data)) {
                            if ($this->grammar->isSymbol($data[$i])) {
                                $this->union($this->follow[$data[$i]], $this->first[$data[$j]], $wasModified);
                            }
                        }
                    }
                }
            }
        } while ($wasModified);
    }

    /**
     * Print follow, first, nullable table
     */
    public function printTable()
    {
        $str = '<table border="1" width="50%" height="20%">';
        $str .= '<tr><th>Symbol</th><th>nullable</th><th>first</th><th>follow</th></tr>';
        foreach ($this->grammar->getSymbols() as $symbol) {
            $str .= '<tr>'.sprintf('<td>%s</td><td>%s</td><td>%s</td><td>%s</td>', $symbol,
                    $this->nullable[$symbol] ? 'da' : 'nu',
                    implode(', ', $this->first[$symbol]),
                    implode(', ', $this->follow[$symbol])
                ).'</tr>';
        }
        $str .= '</table>';

        echo $str;
    }


    /**
     * Check if is nullable
     *
     * @param $symbol
     * @return bool
     */
    protected function isNullable($symbol)
    {
        if ($this->grammar->isSymbol($symbol)) {
            return $this->nullable[$symbol];
        }
        return $symbol == 'EPSILON' ? true : false;
    }

    /**
     * Check if values are nullable
     *
     * @param $start
     * @param $last
     * @param $data
     * @return bool
     */
    protected function areNullable($start, $last, $data)
    {
        if ( ! $this->canInstantiate($data)) {
            return false;
        }

        if ($last < 0 || count($data) <= $last) {
            return true;
        }

        $isNullable = true;
        for ($i = $last; $i <= $start; $i++) {
            if ( ! $this->isNullable($data[$i])) {
                $isNullable = false;
                break;
            }
        }

        return $isNullable;
    }

    /**
     * Ensure us that we will have an array
     *
     * @param $array
     * @param $pos
     */
    protected function ensureList(&$array, $pos)
    {
        if ($pos == 'EPSILON') return;

        if ( ! isset($array[$pos])) {
            $array[$pos] = [];
        }
    }

    /**
     * Check if can instantiate
     *
     * @param $productions
     * @return bool
     */
    protected function canInstantiate($productions)
    {
        return ! (count($productions) == 1 && $productions[0] == 'EPSILON');
    }

    /**
     * Check if two arrays are the same
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    protected function isSame(array $a, array $b)
    {
        return count(array_diff($a, $b)) == 0;
    }

    /**
     * Make union for 2 sets
     *
     * @param array $a
     * @param array $b
     * @param bool $wasModified
     * @return array
     */
    public function union(array &$a, array $b, &$wasModified)
    {
        $new = array_unique(array_merge($a, $b));

        if ( ! $this->isSame($new, $a)) {
            $wasModified = true;
            $a = $new;
        }

        return $new;
    }

    /**
     * Dump the data
     */
    public function dump()
    {
        var_dump($this->first, $this->follow, $this->nullable);
    }
}
