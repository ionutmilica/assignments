<?php

class Grammar
{
    protected $grammar;
    protected $rules = [];
    protected $data = [];

    protected $symbols = [];
    protected $terminals = [];

    /**
     * @param string $grammar
     */
    public function __construct($grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * Get rules
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Get simbols
     *
     * @return array
     */
    public function getSymbols()
    {
        return $this->symbols;
    }

    /**
     * Check if is symbol
     *
     * @param $symbol
     * @return bool
     */
    public function isSymbol($symbol)
    {
        return in_array($symbol, $this->symbols);
    }

    /**
     * Get terminals
     *
     * @return array
     */
    public function getTerminals()
    {
        return $this->terminals;
    }

    /**
     * Parse a string
     */
    public function parse()
    {
        $lines = explode(PHP_EOL, $this->grammar);

        foreach ($lines as $line) {
            list($name, $data) = explode('->', $line);

            $parts = explode('|', $data);
            $name = trim($name);

            foreach ($parts as $part) {
                $data = explode(' ', trim($part));

                if (count($data) == 0) $data[] = 'EPSILON';

                $this->rules[] = [
                    'X' => $name,
                    'data' => $data
                ];
                if ( ! in_array($name, $this->symbols)) {
                    $this->symbols[] = $name;
                }
            }
        }

        foreach ($this->rules as $rule) {
            foreach ($rule['data'] as $terminal) {
                if ( ! in_array($terminal, $this->terminals) && ! $this->isSymbol($terminal) && $terminal != 'EPSILON') {
                    $this->terminals[] = $terminal;
                }
            }
        }
    }
}