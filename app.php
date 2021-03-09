<?php




//$di = new RecursiveDirectoryIterator(__DIR__ . '/files',RecursiveDirectoryIterator::SKIP_DOTS);
//$it = new RecursiveIteratorIterator($di);
//
//foreach($it as $file) {
//    if (pathinfo($file, PATHINFO_EXTENSION) === "php") {
//        echo $file, PHP_EOL;
//    }
//}


//die();

//-------------------------------------


//$start = microtime(true);
//
//function replaceCommandOutput(array $output) {
//    static $oldLines = 0;
//    $numNewLines = count($output) - 1;
//
//    if ($oldLines == 0) {
//        $oldLines = $numNewLines;
//    }
//
//    echo implode(PHP_EOL, $output);
//    echo chr(27) . "[0G";
//    echo chr(27) . "[" . $oldLines . "A";
//
//    $numNewLines = $oldLines;
//}
//
//// Generate dots
//$hashs = '';
//for ($j=0; $j<=100; $j++) {
//    $hashs .= '.';
//}
//
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
//
//// посчитать всего файлов
//// показывать % сделаного
//// В конце время выполнения
//
//echo PHP_EOL . 'Full Time: ' . round(microtime(true) - $start, 2).' s.' . PHP_EOL;











// -------------------------------------------------------------------





















