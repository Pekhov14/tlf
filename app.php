<?php

//print_r(scandir('./for_scan'));


//$di = new RecursiveDirectoryIterator(__DIR__ . '/for_scan',RecursiveDirectoryIterator::SKIP_DOTS);
//$it = new RecursiveIteratorIterator($di);
//
//foreach($it as $file) {
////    if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
//        echo $file, PHP_EOL;
////    }
//}


//sleep(1);
//system('clear');


//-------------------------------------


$start = microtime(true);

function replaceCommandOutput(array $output) {
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

// Generate dots
$hashs = '';
for ($j=0; $j<=100; $j++) {
    $hashs .= '.';
}

$i = 0;

while ($i <= 100) {
    $output = [];

    for ($k=0; $k <= $i; $k++) {
        $hashs[$k] = '#';
    }
    $output[] = 'Working ' . $i . '% ' . $hashs;
    $output[] = 'Time: ' . round(microtime(true) - $start, 2) . 's';
    replaceCommandOutput($output);

//    work
    usleep(100000);

    $i++;
}

// посчитать всего файлов
// показывать % сделаного
// В конце время выполнения

echo PHP_EOL . 'Full Time: ' . round(microtime(true) - $start, 2).' s.' . PHP_EOL;



//class TranslateFolder {
//    public function __construct($params)
//    {
//        if(!isset($params[1]) || !isset($params[2])) {
//            trigger_error('My creator wants you to pass two arguments');
//            die();
//        }
//
//        $this->run($params[1], $params[2]);
//    }
//
//    public function run($dirName, $newDir) {
//        echo $dirName . ' ' . $newDir .  PHP_EOL;
//        $di = new RecursiveDirectoryIterator(__DIR__ . '/'.$dirName,RecursiveDirectoryIterator::SKIP_DOTS);
//        $it = new RecursiveIteratorIterator($di);
//
////        var_dump($it);
//
//foreach($it as $file) {
//////    if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
//        echo $file, PHP_EOL;
//////    }
//}
//    }
//};
//
//
//(new TranslateFolder($argv));