<?php
//error_reporting(0);
/*
 * TODO:
 * Подчитать колличевство файлов
 * При каждом переведенном отображать % сделанного и #...
 * Решить вопрос с путиями
 */

require_once ('vendor/autoload.php');

use Dejurin\GoogleTranslateForFree;

class TranslateFolder {
    private $source     = 'ru';
    private $target     = 'uk';
    private $attempts   = 5;
    private $pattern    = "/(?<=[\s|=]').*(?<!')(?<!;)/";
//    private $outputFile = './files/category_out.php';

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
		if (!is_dir(__DIR__ . '/files/' . $newDir)) {
			mkdir(__DIR__ . '/files/' . $newDir, 0777, true);
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

    private function translateFile($pathToFile, $nameFile, $fullName) {

		// TODO: решить проблему с путем '/files/ru-ru/'
        // $dirName
        // раз сплойдить брать последнюю часть, и рядом соавать апку ua-ua
        foreach($this->getLines(__DIR__ . '/files/ru-ru/' . $fullName) as $line) {

            preg_match($this->pattern, $line, $matches);

            $resultLine = $line;

            if($matches) {
                $translate = $this->tr->translate($this->source, $this->target, $matches[0], $this->attempts);
                $translate = addslashes($translate);

                $resultLine = preg_replace($this->pattern, $translate, $line);
            }

            if (!is_dir(__DIR__ . '/files/ua-ua/' . $pathToFile)) {
                mkdir(__DIR__ . '/files/ua-ua/' . $pathToFile, 0777, true);
            }

            file_put_contents(__DIR__ . '/files/ua-ua/' . $pathToFile . '/'. $nameFile, $resultLine, FILE_APPEND | LOCK_EX);

            sleep(rand(1, 7));
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