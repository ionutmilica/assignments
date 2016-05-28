<?php

use CT\NFA\Log\Logger;
use CT\NFA\NFA;
use CT\NFA\NFA2DFA;
use CT\NFA\Regex\Parser;
use CT\NFA\State;

require '../../vendor/autoload.php';

$txt = isset($_POST['txt']) ? $_POST['txt'] : 'acd';
$regex = isset($_POST['regex']) ? $_POST['regex'] : '(a|b)*c*(d|e*)';

list($s0, $final) = (new Parser())->parse($regex)->toArray();

$tr = new NFA2DFA();
$tr->make($tr->closure([$s0]));

$nfa = new NFA();
$nfa->attachLogger(new Logger());
$nfa->setStates($tr->getStates());
$nfa->setStartingStates([$tr->getStart()]);
$nfa->toMinimal();

if ($isOk = $nfa->run($txt)) {
    $message = sprintf('%s is accepted by the NFA!', $txt);
} else {
    $message = sprintf('%s is not accepted by the NFA!', $txt);
}

?>
<?php echo $message ?><br/>
<?php if ($isOk): ?>
    Drum: <b><?php $nfa->getLogger()->display() ?></b><br/>
<?php endif; ?>
<form action="index.php" method="post">
    Regex: <br/><input type="txt" name="regex" value="<?php echo $regex; ?>"><br/>
    Cuvant: <br/><input type="text" name="txt" value="<?php echo $txt; ?>"><br/>
    <?php if($isOk): ?>
        Config:<br/>
        <textarea><?php  echo \CT\NFA\State::export($nfa->getStartingStates()[0]); ?></textarea><br/>
    <?php endif; ?>
    <input type="submit" name="submit" value="Verifica!">
</form>