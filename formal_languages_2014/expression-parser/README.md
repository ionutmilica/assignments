Expression Parser
=========
<br/>
A simple expression parser written in PHP-OOP.

CLI usage:
--------------

``` php
php cli.php "10+(2+4)^2
php cli.php 5+10
php cli.php 22  %  10
```
Plain PHP usage:
----------------

``` php
<?php

require 'Lib/Autoloader.php';

$loader = new Autoloader;
$loader->setIncludePath(dirname(__FILE__));
$loader->register();

use Lib\Parser as Parser;
use Lib\EmptyStackAccessException as EmptyStackAccessException;

$result = '';
$expression = '2^(5-2)'; // your expression here

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
```
