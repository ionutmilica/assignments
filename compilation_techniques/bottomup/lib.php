<?php

class BottomUp
{
    protected $grammar;
    protected $parsed = [];
    protected $data = [];
    public $matrix = [];
    protected $line = [];

    /**
     * @param string $grammar
     */
    public function __construct($grammar)
    {
        $this->data = [
            'first'  => [],
            'first+' => [],
            'first*' => [],
            'last'   => [],
            'last+'  => [],
            'last*'  => []
        ];

        $this->grammar = $grammar;
    }

    public function getMatrix()
    {
        $this->parse();
        $this->getF();
        $this->getFPS();
        $this->getL();
        $this->getLPS();
        $this->prepareTable();
        $this->completeTable();
        $this->printTable();
    }

    public function findRule($name)
    {
        foreach ($this->parsed as $n => $p) {
            foreach ($p as $px) {
                if ($px == $name) return $n;
            }
        }
        return null;
    }

    protected function prepareTable()
    {
        $tmp = [];
        foreach ($this->parsed as $data) {
            $tmp = array_merge($tmp, $data);
        }
        $tmp = implode('', $tmp);
        $symbols = array_unique(str_split($tmp));
        $matrix = null;

        foreach ($symbols as $x) {
            foreach ($symbols as $y) {
                $matrix[$x][$y] = '';
            }
        }

        $this->matrix = $matrix;
        $this->line = array_values($symbols);
    }

    protected function completeTable()
    {
        // =
        foreach ($this->parsed as $data) {
            foreach ($data as $d) {
                $i = 0;
                $j = 1;
                while ($j < strlen($d)) {
                    $first  = $d[$i];
                    $last   = $d[$j];
                    // =
                    $this->fillMatrix($first, $last, '=');
                    // <
                    if (isset($this->data['first+'][$last])) {
                        foreach ($this->data['first+'][$last] as $s) {
                            $this->fillMatrix($first, $s, '<');
                        }
                    }
                    // >
                    if (isset($this->data['last+'][$first])) {
                        $A = $this->data['last+'][$first];
                        $B = isset($this->data['first*'][$last]) ? $this->data['first*'][$last] : [$last];
                        foreach ($A as $a) {
                            foreach ($B as $b) {
                                $this->fillMatrix($a, $b, '>');
                            }
                        }
                    }

                    //
                    $i++;
                    $j++;
                }
            }
        }
    }

    protected function fillMatrix($x, $y, $val) {
        $value = $this->matrix[$x][$y];
        if (is_array($value)) {
            if ( ! in_array($val, $value)) {
                $val = array_merge($value, [$val]);
            }
        } elseif ($value !== '') {
            $val = [$value, $val];
        }
        $this->matrix[$x][$y] = $val;
    }

    protected function printTable()
    {
        $str = '<table border="1"><tr><th>&nbsp;</th>';
        $n = 1;
        foreach ($this->line as $symbol) {
            $str .= sprintf('<th style="width: 40px;">%s</th>', $symbol);
            $n++;
        }
        $str .= '</tr>';

        $i = 0;
        foreach ($this->line as $x) {
            $str .= '<tr>';
            $str .= '<td style="width: 40px;"><b>'.$this->line[$i].'</b></td>';
            foreach ($this->line as $y) {
                $str .= sprintf('<td>%s</td>', is_array($this->matrix[$x][$y]) ? implode(',',$this->matrix[$x][$y]) : $this->matrix[$x][$y]);
            }
            $str .= '</tr>';
            $i++;
        }
        $str .= '</table>';

        echo $str;
    }

    /**
     * Parse a string
     */
    public function parse()
    {
        $lines = explode(PHP_EOL, $this->grammar);

        foreach ($lines as $line) {
            $line = str_replace(' ', '', $line);
            list($name, $data) = explode('->', $line);
            $this->parsed[$name] = explode('|', $data);
        }
    }

    public function getF()
    {
        foreach ($this->parsed as $name => $data) {
            $this->data['first'][$name] = [];
            foreach ($data as $rule) {
                $firstSymbol = $rule[0];
                if ( ! in_array($firstSymbol, $this->data['first'][$name])) {
                    $this->data['first'][$name][] = $firstSymbol;
                }
            }
        }
    }

    public function getFPS()
    {
        $first = array_reverse($this->data['first'], true);

        foreach ($first as $name => $data) {
            $this->data['first+'][$name] = $data;
            $this->data['first*'][$name] = array_unique(array_merge([$name], $data));

            unset($first[$name]);
            break;
        }

        foreach ($first as $name => $data) {
            $fp = [];
            foreach ($data as $symbol) {
                if ($symbol != $name && isset($this->data['first+'][$symbol])) {
                    $fp = array_merge($fp, $this->data['first+'][$symbol]);
                }
                $fp[] = $symbol;
            }
            $this->data['first+'][$name] = array_unique($fp);
            $this->data['first*'][$name] = array_unique(array_merge([$name], $fp));
        }
    }

    public function getLPS()
    {
        $last = array_reverse($this->data['last'], true);

        foreach ($last as $name => $data) {
            $this->data['last+'][$name] = $data;
            $this->data['last*'][$name] = array_unique(array_merge([$name], $data));

            unset($last[$name]);
            break;
        }

        foreach ($last as $name => $data) {
            $lp = [];
            foreach ($data as $symbol) {
                if ($symbol != $name && isset($this->data['last+'][$symbol])) {
                    $lp = array_merge($lp, $this->data['last+'][$symbol]);
                }
                $lp[] = $symbol;
            }
            $this->data['last+'][$name] = array_unique($lp);
            $this->data['last*'][$name] = array_unique(array_merge([$name], $lp));
        }
    }

    public function getL()
    {
        foreach ($this->parsed as $name => $data) {
            $this->data['last'][$name] = [];
            foreach ($data as $rule) {
                $lastSymbol = $rule[strlen($rule) - 1];
                if ( ! in_array($lastSymbol, $this->data['last'][$name])) {
                    $this->data['last'][$name][] = $lastSymbol;
                }
            }
        }
    }
}
