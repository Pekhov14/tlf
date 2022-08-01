<?php

error_reporting(0);

require_once ('vendor/autoload.php');

use Dejurin\GoogleTranslateForFree;

class TranslateFolder {
    private $source;
    private $target;
    private $attempts   = 5;
    private $pattern    = "/(?<=[\s|=](\"|')).*(?<!(\"|'))(?<!;)/";
    private $pathToOldDir;
    private $pathToNewDir;
    private $languageFiles;
	private $tr;
    private $fastTranslate;
    private static $numberFiles;

    public const fixedInterest = 100;

	public function __construct($params)
    {
        [$application, $dirName, $newDir, $isFastTranslate, $source, $target] = $params;

        if(!isset($dirName, $newDir)) {
            trigger_error('My creator wants you to pass two arguments');
            die();
        }
        $this->tr = new GoogleTranslateForFree();

        $dirName = rtrim($dirName, '/');
        $newDir .= (substr($newDir, -1) == '/' ? '' : '/');

        $this->pathToNewDir = $newDir;
        $this->pathToOldDir = $dirName;
        $this->fastTranslate = (isset($isFastTranslate) && $isFastTranslate == true) ? true : false;
        $this->source = $source ?? 'ru';
        $this->target = $target ?? 'uk';

        $this->run();
    }

    public function run()
    {
		$this->setPathProperties($this->pathToOldDir, $this->pathToNewDir);

		if (!is_dir($this->pathToNewDir)) {
			mkdir($this->pathToNewDir, 0777, true);
		}

		$di = new RecursiveDirectoryIterator($this->pathToOldDir,RecursiveDirectoryIterator::SKIP_DOTS);
		$it = new RecursiveIteratorIterator($di);

		self::setNumberFiles($it);

        $start = microtime(true);

		$numberTranslatedFiles = 0;

		foreach($it as $file) {
			$fileInfo = pathinfo($file);

			if ($fileInfo['extension'] === "php") {

                $percentTranslatedFiles = self::getPercentTranslatedFiles($numberTranslatedFiles);

				$output = [];
				$output[] = 'Working: '
                    . $percentTranslatedFiles  . '% '
                    . $this->getHashes($percentTranslatedFiles);
				$output[] = 'Please wait while our monkeys finish translating';

				$this->replaceCommandOutput($output);

				usleep(100000);

                $this->translateFile($it->getSubPath(), $fileInfo['basename'], $it->getSubPathName());

                $numberTranslatedFiles++;
            }
        }
        echo PHP_EOL . PHP_EOL . 'Full Time: ' . round(microtime(true) - $start, 2).' s.' . PHP_EOL;
    }

    private static function setNumberFiles($it): void
    {
        self::$numberFiles = count($it);
    }

    private static function getPercentTranslatedFiles($numberTranslatedFiles)
    {
        return ceil(($numberTranslatedFiles / self::$numberFiles) * self::fixedInterest);
    }

    private function setPathProperties($dirName, $newDir)
    {
		$pathToLanguageFiles = explode('/', $dirName);
		$oldDir = array_pop($pathToLanguageFiles);
		$this->languageFiles = implode('/', $pathToLanguageFiles);
		$this->pathToNewDir = $this->languageFiles . '/' . $newDir;
		$this->pathToOldDir = $this->languageFiles . '/' . $oldDir . '/';
	}

    private function getLines(string $path)
    {
        $file = fopen($path, 'r');

        if(!$file) {
            throw new \RuntimeException();
        }

        while ($line = fgets($file)) {
            yield $line;
        }

        fclose($file);
    }

    private function getHashes($numberDots) {
	    $dots = '';

        if ((int)$numberDots === 0) {
            for ($i = 1; $i <= self::fixedInterest; $i++) {
                $dots .= '.';
            }
        } elseif ($numberDots > 0) {
            for ($i = 0; $i <= $numberDots; $i++) {
                $dots .= '#';
            }
        }

	    $hashes = strlen($dots);

        if ($hashes !== self::fixedInterest) {
            $otherPoints = self::fixedInterest - $hashes;

            for ($i = 0; $i <= $otherPoints; $i++) {
                $dots .= '.';
            }
        }

        return $dots;
    }

    private function translateFile($pathToFile, $nameFile, $fullName)
    {
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

            if($matches) {
                // Pretending what we think
                $pause = ($this->fastTranslate) ? random_int(1, 3) : random_int(20, 60);
                sleep($pause);
            }
        }
    }

    private function replaceCommandOutput(array $output)
    {
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
}


(new TranslateFolder($argv));

