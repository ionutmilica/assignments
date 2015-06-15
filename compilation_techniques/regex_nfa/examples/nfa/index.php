<?php

use CT\NFA\Factory;
use CT\NFA\Log\Logger;
use CT\NFA\Reader;

require '../../vendor/autoload.php';

$defaultText = 'abc';
$defaultFile = 'latest.json';

$txt = isset($_GET['txt']) ? $_GET['txt'] : $defaultText;
$reader = new Reader($defaultFile);

$nfa = Factory::make($reader->getStates(), $reader->getRules(), new Logger());

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
<form action="index.php" method="get">
    Cuvant: <br/><input type="text" name="txt" value="<?php echo $txt; ?>"><br/>
    <?php if($isOk): ?>
        Config:<br/>
    <textarea><?php  echo \CT\NFA\State::export($nfa->getStartingStates()[0]); ?></textarea><br/>
    <?php endif; ?>
    <input type="submit" name="submit" value="Verifica!">
</form>