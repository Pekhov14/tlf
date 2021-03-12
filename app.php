<?php

//error_reporting(0);

/*
 * TODO:
 * Подчитать колличевство файлов
 * При каждом переведенном отображать % сделанного и #...
 * Добавить проверку на слеши
 */

require_once ('vendor/autoload.php');

use Dejurin\GoogleTranslateForFree;

class TranslateFolder {
    private $source     = 'ru';
    private $target     = 'uk';
    private $attempts   = 5;
    private $pattern    = "/(?<=[\s|=]').*(?<!')(?<!;)/";
    private $pathToOldDir;
    private $pathToNewDir;
    private $languageFiles;
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
		$this->setPathProperties($dirName, $newDir);

		if (!is_dir($this->pathToNewDir)) {
			mkdir($this->pathToNewDir, 0777, true);
		}

		$di = new RecursiveDirectoryIterator($dirName,RecursiveDirectoryIterator::SKIP_DOTS);
		$it = new RecursiveIteratorIterator($di);

        $start = microtime(true);

		$i = 0;
		$hashes = $this->generateDots();

		foreach($it as $file) {
			$fileInfo = pathinfo($file);

			if ($fileInfo['extension'] === "php") {
				$output = [];
				$output[] = 'Working: ' . $i  . '% ' . $hashes;
				$output[] = 'Please wait while our monkeys finish translating';

				$this->replaceCommandOutput($output);

				usleep(100000);
				$hashes[$i] = '#';

                $this->translateFile($it->getSubPath(), $fileInfo['basename'], $it->getSubPathName());

                $i++;
            }
        }
        echo PHP_EOL . PHP_EOL . 'Full Time: ' . round(microtime(true) - $start, 2).' s.' . PHP_EOL;
    }

    private function setPathProperties($dirName, $newDir) {
		$pathToLanguageFiles = explode('/', $dirName);
		$oldDir = array_pop($pathToLanguageFiles);
		$this->languageFiles = implode('/', $pathToLanguageFiles);
		$this->pathToNewDir = $this->languageFiles . '/' . $newDir;
		$this->pathToOldDir = $this->languageFiles . '/' . $oldDir . '/';
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
        $hashes = '';
        for ($j=0; $j<=100; $j++) {
            $hashes .= '.';
        }
        return $hashes;
    }

    private function translateFile($pathToFile, $nameFile, $fullName) {
        foreach($this->getLines($this->pathToOldDir . $fullName) as $line) {

            preg_match($this->pattern, $line, $matches);
            $resultLine = $line;

            if($matches) {
                $translate = $this->tr->translate($this->source, $this->target, $matches[0], $this->attempts);
                $translate = addslashes($translate);

                $resultLine = preg_replace($this->pattern, $translate, $line);
            }

            if (!is_dir($this->pathToNewDir . $pathToFile)) {
                mkdir($this->pathToNewDir . $pathToFile, 0777, true);
            }

            file_put_contents($this->pathToNewDir . $pathToFile . '/'. $nameFile, $resultLine, FILE_APPEND | LOCK_EX);

            sleep(rand(1, 2));
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