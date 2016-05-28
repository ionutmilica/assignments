<?php
/**
    Milica Ionut Catalin
    Grupa 4, Subgrupa 5
 */

function parsePerm($perm)
{
    $perm = str_replace(['(', ')', ' '], '', $perm);
    return explode(',', $perm);
}

function formatPerm($perm)
{
    return '('. implode(', ', $perm) . ')';
}

function firstKey($array) {
    foreach ($array as $k => $v)
        return $k;
    return null;
}

/**
 * 
 *
 * @param $perm
 * @return array
 */
function toCycle($perm)
{
    $used = [];
    foreach ($perm as $p) {
        $used[$p] = 1;
    }
    $cycles = [[]];
    $lastCycle = 0;
    $sw = 0;
    $idx = 0;

    do {
        ksort($used);
        if ($sw == 0) {
            $element = $perm[$idx];
            if ( ! in_array($element, $cycles[$lastCycle])) {
                $cycles[$lastCycle][] = $element;
            }
            unset($used[$element]);
            $sw = 1;
        } else {
            $firstC = $cycles[$lastCycle][0];

            if ($firstC == array_search($idx + 1, $perm) + 1) {
                unset($used[$idx + 1]);
                if ( ! in_array($idx + 1, $cycles[$lastCycle])) {
                    $cycles[$lastCycle][] = $idx + 1;
                }
                ksort($used);
                $sw = 0;
                if (count($used) > 0) {
                    $idx = array_search(firstKey($used), $perm);
                    $cycles[$lastCycle + 1] = [];
                    $lastCycle++;
                } else {
                    break;
                }
            } else {
                unset($used[$idx + 1]);
                if ( ! in_array($idx + 1, $cycles[$lastCycle])) {
                    $cycles[$lastCycle][] = $idx + 1;
                }
                $idx = array_search($idx + 1, $perm);
                $sw = 0;
            }
        }
    } while (1);

    return $cycles;
}

function factorial($n)
{
    $total = 1;
    for ($i = 1; $i <= $n; $i++) {
        $total *= $i;
    }
    return $total;
}

function countSameType($perm)
{
    $n = count($perm);
    $lambda = [];
    for ($i = 1; $i <= $n; $i++) {
        $lambda[$i] = 0;
    }
    $cycles = toCycle($perm);
    foreach ($cycles as $cycle) {
        $lambda[count($cycle)]++;
    }
    $sub = 1;
    foreach ($lambda as $l) {
        $sub *= factorial($l);
    }
    foreach ($lambda as $k => $l) {
        $sub *= pow($k + 1, $l);
    }
    return factorial($n) / $sub;
}

function permRank($perm)
{
    $n = count($perm);
    $q = [];

	if ($n == 1)
        return 0;

    for ($i = 1; $i < $n; $i++){
        if ($perm[$i] < $perm[0]) {
            $q[$i - 1] = $perm[$i];
        } else {
            $q[$i - 1] = $perm[$i] - 1;
        }
    }
    return permRank(array_slice($q, 0, $n - 1)) + ($perm[0] - 1) * factorial($n - 1);
}

function nextPerm($perm)
{
    $n = count($perm);
    $i = $n - 2;
	while ($perm[$i] > $perm[$i + 1])
		$i--;
	$j = $n - 1;
	while ($perm[$j] < $perm[$i])
		$j--;
	$tmp = $perm[$i];
	$perm[$i] = $perm[$j];
	$perm[$j] = $tmp;
	for ($k = 0; $k < ($n - $i - 1) / 2; $k++){
        $tmp = $perm[$i + 1 + $k];
		$perm[$i + 1 + $k] = $perm[$n - 1 - $k];
		$perm[$n - 1 - $k] = $tmp;
	}
	return $perm;
}

$result = 'No results found !';

if (isset($_POST['permutation']) && stripos($_POST['permutation'], '(') === 0) {
    $perm = parsePerm($_POST['permutation']);
    switch ($_POST['action']) {
        case 'Next perm':
            $result = formatPerm(nextPerm($perm));
            break;
        case 'Perm Rank':
            $result = permRank($perm);
            break;
        case 'Count same type':
            $result = countSameType($perm);
            break;
    }
}

?>
<html>
    <head>
        <title>Calculator de permutari</title>
    </head>
    <body>
        <h1>Calculator TGC !</h1>
        <form action="index.php" method="post">
            <label for="permutation">Permutare: </label>
            <input type="text" id="permutation" name="permutation" value="<?php echo isset($_POST['permutation']) ? $_POST['permutation'] : '' ?>"/><br/>
            <input type="submit" name="action" value="Next perm">
            <input type="submit" name="action" value="Perm Rank">
            <input type="submit" name="action" value="Count same type">
        </form>
        <div id="result">Rezultat: <b><?php echo $result; ?></b></div>
    </body>
</html>
