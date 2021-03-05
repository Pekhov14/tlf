<?php

require_once ('vendor/autoload.php');

use \Dejurin\GoogleTranslateForFree;

$source   = 'ru';
$target   = 'uk';
$attempts = 5;

$pattern    = "/\s'(.+?)'/";
$outputFile = './files/category_out.php';

$tr = new GoogleTranslateForFree();

function getLines(string $path) {
	$file = fopen($path, 'r');

	if(!$file) {
		throw new \Exception();
	}

	while ($line = fgets($file)) {
		yield $line;
	}

	fclose($file);
}

foreach(getLines('./files/category.php') as $line) {
	preg_match($pattern, $line, $matches);

	$resultLine = $line;

	if($matches) {
		$translate = ' ';
		$translate .= $tr->translate($source, $target, $matches[0], $attempts);
		$resultLine = preg_replace($pattern, $translate, $line);
	}

	file_put_contents($outputFile, $resultLine, FILE_APPEND | LOCK_EX);
}

echo 'Success' . PHP_EOL;