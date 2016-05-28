<?php

//Autoloading
require 'Lib/Autoloader.php';

$loader = new Autoloader;
$loader->setIncludePath(dirname(__FILE__));
$loader->register();

use Lib\Parser as Parser;
use Lib\EmptyStackAccessException;

$expression = '';

for($i = 1; $i < $argc; $i++) 
{
	$expression .= $argv[$i];	
}

try 
{
	$parser = new Parser($expression);
	echo $expression . ' = ' . $parser->evaluate();
} 
catch(EmptyStackAccessException $e) 
{
	echo 'Empty stack';
}

echo PHP_EOL;

exit(0);
