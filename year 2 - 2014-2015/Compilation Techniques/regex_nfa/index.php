<?php

$html = '<h1>Tehnici de compilare</h1><ul>';

$directories = new DirectoryIterator('.');
foreach ($directories as $directory) {
    $name = $directory->getBasename();
    if ($directory->isDir() && $name[0] != '.') {
        $html .= sprintf('<li><a href="%s">%s</a></li>', $name, $name);
    }
}
$html .= '</ul>';

echo $html;