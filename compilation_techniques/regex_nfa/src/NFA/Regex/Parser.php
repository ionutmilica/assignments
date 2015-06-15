<?php
namespace CT\NFA\Regex;

use CT\NFA\State;
use SplStack;

class Operator {
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getPriority()
    {
        return self::priority($this->name);
    }

    public function isRightToLeft()
    {
        return false;
    }

    public static function isOperator($op)
    {
        return $op == "." || $op == "|" || $op == "*" || $op == ")" || $op == '(';
    }

    public static function isOperatorExtended($op)
    {
        return self::isOperator($op);
    }

    public static function priority($op)
    {
        switch ($op) {
            case '(': return 0;
            case ')': return 0;
            case '|': return 1;
            case '.': return 2;
            case '*': return 3;

        }
        return -1;
    }
}

class Parser {

    /**
     * @var
     */
    protected $id = 1;


    /**
     * Before parse
     *
     * @param $expr
     * @return array
     */
    protected function beforeParse($expr)
    {
        $chars = str_split($expr);
        $xchars = $chars;
        $err = 0;
        for ($i = 0; $i < count($chars) - 1; $i++) {
            if ( (! Operator::isOperatorExtended($chars[$i]) && ! Operator::isOperatorExtended($chars[$i + 1]))
                || $chars[$i] == '*' && ! Operator::isOperator($chars[$i + 1])
                || $chars[$i] == '*' && $chars[$i + 1] == '(') {
                array_insert_after($xchars, $i + $err, '.');
                $err++;
            }
        }
        return $xchars;
    }

    /**
     * Parse expression
     *
     * @param $expr
     * @return mixed
     * @throws \Exception
     */
    public function parse($expr)
    {
        $chars = $this->beforeParse($expr);
        $operators = new SplStack();
        $output = [];

        foreach ($chars as $token) {
            if ( ! Operator::isOperator($token)) {
                $start = new State($this->id++);
                $end = new State($this->id++);
                $end->setFinal();
                $start->to($end, $token);
                $output[] = new Tuple([$start, $end]);
            } else {
                if ( ! $operators->count() || $token == '(') {
                    $operators->push(new Operator($token));
                    continue;
                }
                $lastOperator = $operators->top();

                if ($token == ")") {
                    while ($lastOperator->name != "(") {
                        $output[] = $lastOperator->name;
                        $operators->pop();
                        $lastOperator = $operators->top();
                    }
                    $operators->pop();
                    continue;
                }

                if ($lastOperator->getPriority() >= Operator::priority($token)) {
                    while ($operators->count() > 0 && (
                            $lastOperator->getPriority() >= Operator::priority($token) &&
                            !(new Operator($token))->isRightToLeft())) {
                        $output[] = $lastOperator->name;
                        $operators->pop();

                        if ($operators->count() > 0)
                            $lastOperator = $operators->top();
                    }
                }
                $operators->push(new Operator($token));
            }
        }
        while ($operators->count() > 0) {
            $output[] = $operators->top()->name;
            $operators->pop();
        }
        $operands = new SplStack();
        foreach ($output as $token) {
            if (Operator::isOperator($token)) {
                switch ($token) {
                    case '|':
                        $o1 = $operands->pop();
                        $o2 = $operands->pop();
                        list($s1, $e1) = $o1->toArray();
                        list($s2, $e2) = $o2->toArray();
                        $s = new State($this->id++);
                        $s->setStart(true);
                        $e = new State($this->id++);
                        $e->setFinal(true);
                        $s->to($s1, null);
                        $s->to($s2, null);
                        $e1->to($e, null);
                        $e2->to($e, null);
                        $e1->setFinal(false);
                        $e2->setFinal(false);
                        $operands->push(new Tuple([$s, $e]));
                        break;
                    case '.':
                        $o1 = $operands->pop();
                        $o2 = $operands->pop();
                        list($s1, $e1) = $o2->toArray();
                        list($s2, $e2) = $o1->toArray();
                        $e1->setFinal(false);
                        $e1->to($s2, null);
                        $operands->push(new Tuple([$s1, $e2]));
                        break;
                    case '*':
                        $o1 = $operands->pop();
                        list($s, $e) = $o1->toArray();
                        $s->to($e, null);
                        $e->to($s, null);
                        $s1 = new State($this->id++);
                        $e2 = new State($this->id++);
                        $e2->setFinal();

                        $s1->to($s, null);
                        $s1->to($e2, null);

                        $e->to($e2, null);
                        $e->to($s, null);
                        $e->setFinal(false);
                        $operands->push(new Tuple([$e, $s]));
                        break;
                    default: throw new \Exception('Invalid operator');
                }
            }
            else {
                $operands->push($token);
            }
        }

        if ($operands->count() == 1) {
            return $operands->top();
        }

        throw new \Exception('Parsing for expression '.$expr.' has failed!');
    }

    public function display(State $state)
    {
        foreach ($state->getPaths() as $char => $path) {
            foreach ($path as $dir) {
                $char = $char == null ? 'Epsilon' : $char;
                echo $state->getName() .' -> '. $char. '  -> ' . $dir->getName() .'<br/>';
                $this->display($dir);
            }
        }
    }
}



/**
 * Insert after pos in array
 *
 * @param $haystack
 * @param string $needle
 * @param $stuff
 * @return array|int
 */
function array_insert_after(&$haystack, $needle = '', $stuff){
    if (! is_array($haystack) ) return $haystack;

    $new_array = array();
    for ($i = 2; $i < func_num_args(); ++$i){
        $arg = func_get_arg($i);
        if (is_array($arg)) $new_array = array_merge($new_array, $arg);
        else $new_array[] = $arg;
    }

    $i = 0;
    foreach($haystack as $key => $value){
        ++$i;
        if ($key == $needle) break;
    }

    $haystack = array_merge(array_slice($haystack, 0, $i, true), $new_array, array_slice($haystack, $i, null, true));

    return $i;
}