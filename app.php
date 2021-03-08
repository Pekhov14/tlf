<?php

/*
 * TODO:
 * Добавить создание папки с именем второва параметра
 * Добавить визуальный эфект в терменал
 * Подчитать колличевство файлов
 * При каждом переведенном отображать % сделанного и #...
 * Рекурсивно обходить всю папку и переводить
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
        echo $dirName . ' ' . $newDir .  PHP_EOL;
        $start = microtime(true);
        $this->translateFile();
        echo PHP_EOL . 'Full Time: ' . round(microtime(true) - $start, 2).' s.' . PHP_EOL;
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


//$i = 0;
//
//while ($i <= 100) {
//    $output = [];
//
//    for ($k=0; $k <= $i; $k++) {
//        $hashs[$k] = '#';
//    }
//    $output[] = 'Working ' . $i . '% ' . $hashs;
//    $output[] = 'Time: ' . round(microtime(true) - $start, 2) . 's';
//    replaceCommandOutput($output);
//
////    work
//    usleep(100000);
//
//    $i++;
//}

    private function translateFile() {
        $i = 0;
        $hashs = $this->generateDots();
        $output = [];

        foreach($this->getLines('./files/category.php') as $line) {
            preg_match($this->pattern, $line, $matches);

            $resultLine = $line;

            if($matches) {
                $translate = ' ';
                $translate .= $this->tr->translate($this->source, $this->target, $matches[0], $this->attempts);
                $resultLine = preg_replace($this->pattern, $translate, $line);
            }

            file_put_contents($this->outputFile, $resultLine, FILE_APPEND | LOCK_EX);

            for ($k=0; $k <= $i; $k++) {
                $hashs[$k] = '#';
            }

            $output[] = 'Working ' . $i . '% ' . $hashs;
//            $output[] = 'Time: ' . round(microtime(true) - $start, 2) . 's';
            $this->replaceCommandOutput($output);
            $i++;
        }
    }

    public function replaceCommandOutput(array $output) {
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