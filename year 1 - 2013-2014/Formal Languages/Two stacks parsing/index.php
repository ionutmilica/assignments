<?php

require 'Lib/Autoloader.php';

$loader = new Autoloader;
$loader->setIncludePath(dirname(__FILE__));
$loader->register();

use Lib\Parser as Parser;
use Lib\EmptyStackAccessException as EmptyStackAccessException;

$result = '';
$expression = '2*5-1+(5-22)';

try
{
	$parser = new Parser($expression);
	$result = $parser->evaluate();
} 
catch (EmptyStackAccessException $ex)
{
	echo 'Your expression is incorrect.';
	exit;
}

echo $expression . ' = ' . $result;