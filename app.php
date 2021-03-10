<?php

/*
 * TODO:
 * Подчитать колличевство файлов
 * При каждом переведенном отображать % сделанного и #...
 * Рекурсивно обходить всю папку и переводить *
 */

require_once ('vendor/autoload.php');

use \Dejurin\GoogleTranslateForFree;

class TranslateFolder {
    private $source     = 'ru';
    private $target     = 'uk';
    private $attempts   = 5;
    private $pattern    = "/\s'(.+?)'/";
    private $outputFile = './files/category_out.php';

    private $tr;

    public function __construct($params)
    {
        if(!isset($params[1]) || !isset($params[2])) {
            trigger_error('My creator wants you to pass two arguments');
            die();
        }

        $this->tr = new GoogleTranslateForFree();

        $this->run($params[1], $params[2]);
    }

    public function run($dirName, $newDir) {
        mkdir($newDir, 0777, true);

		$di = new RecursiveDirectoryIterator($dirName,RecursiveDirectoryIterator::SKIP_DOTS);
		$it = new RecursiveIteratorIterator($di);

//        foreach($it as $fileinfo){
//		    var_dump($it->getSubPathName());
//		    var_dump($it->getSubPath());
//        }
//
//		die();

//        echo $dirName . ' ' . $newDir .  PHP_EOL;
        $start = microtime(true);

		$i = 0;
		$hashes = $this->generateDots();

		foreach($it as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) === "php") {
//				echo $file, PHP_EOL;

                // Передать имя путь и имя файла
                 $this->translateFile($file);

                $output = [];
                $output[] = 'Working: ' . $i  . '% ' . $hashes;
                $output[] = 'Please wait while our monkeys finish translating';

                $this->replaceCommandOutput($output);
                // sleep(1);
                usleep(100000);
                $hashes[$i] = '#';
                $i++;
            }
        }
        echo PHP_EOL . PHP_EOL . 'Full Time: ' . round(microtime(true) - $start, 2).' s.' . PHP_EOL;
    }

    private function getLines(string $path) {
        $file = fopen($path, 'r');

        if(!$file) {
            throw new \Exception();
        }

        while ($line = fgets($file)) {
            yield $line;
        }

        fclose($file);
    }

    private function generateDots() {
        $hashs = '';
        for ($j=0; $j<=100; $j++) {
            $hashs .= '.';
        }
        return $hashs;
    }

    private function translateFile($pathToFile) {
        foreach($this->getLines($pathToFile) as $line) {
            preg_match($this->pattern, $line, $matches);

            $resultLine = $line;

            if($matches) {
                $translate = ' ';
                $translate .= $this->tr->translate($this->source, $this->target, $matches[0], $this->attempts);
                $resultLine = preg_replace($this->pattern, $translate, $line);
            }


            if (!is_dir(__DIR__ . '/files/ua-ua/7777')) {
                mkdir(__DIR__ . '/files/ua-ua/7777', 0777);
            }

                file_put_contents(__DIR__ . '/files/ua-ua/7777/test.txt', $resultLine, FILE_APPEND | LOCK_EX);
        }
    }

    private function replaceCommandOutput(array $output) {
        static $oldLines = 0;
        $numNewLines = count($output) - 1;

        if ($oldLines == 0) {
            $oldLines = $numNewLines;
        }

        echo implode(PHP_EOL, $output);
        echo chr(27) . "[0G";
        echo chr(27) . "[" . $oldLines . "A";

        $numNewLines = $oldLines;
    }
};


(new TranslateFolder($argv));