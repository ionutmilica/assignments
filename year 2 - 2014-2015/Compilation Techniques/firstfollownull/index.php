<?php

require 'Grammar.php';
require 'Algorithm.php';

$grammar = new Grammar(file_get_contents('g2.txt'));
$grammar->parse();

$algorithm = new Algorithm($grammar);
$algorithm->compute();

$algorithm->printTable();