<?php
require 'lib.php';

$grammar = new BottomUp(file_get_contents('grammar.txt'));
#$grammar = new BottomUp('S->aSSb|ab');
$grammar->getMatrix();

echo '<br/><br/>';

$table = $grammar->matrix;
$p = 'aaabaababbbabb';
#$p = 'aabaabaababbbb';

while (1) {
    $i = 0;
    $j = 1;
    $start = -1;
    $length = 0;

    while ($j < strlen($p)) {
        $tableValue = $table[$p[$i]][$p[$j]];
        if ($tableValue == '<') {
            $start = $j;
            $length = 0;
        }
        if ($start > -1) $length++;
        if ($tableValue == '>') {
			if ($start == -1) $start = 0;
            break;
        }
        $i++;
        $j++;
    }

    $chars = str_split($p);
    if ($start != -1) {
        $chars[$start] = '<b>'.$chars[$start];
        $chars[$start + $length  - 2] = $chars[$start + $length - 2].'</b>';
    }
    echo implode('', $chars) . '<br/><br/>';

    $secv = substr($p, $start, $length - 1);
    $nonTerminal = $grammar->findRule($secv);

    $p = substr_replace($p, $nonTerminal, $start, $length - 1);

    if ( $start == -1) break;
}
