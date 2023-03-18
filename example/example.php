<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config/pathServer.php';

use Restfull\Filesystem\File;

$file = new File(__DIR__ . '/files/ps4.txt');
$file->write('batman, dragon ball z kakarot e entre outros.');
$file->close();