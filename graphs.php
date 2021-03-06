<?php

/**
 * This script uses the ezComponents Graph component to convert the raw CSV 
 * data generated by runner.php into SVG graph images.
 *
 * @link http://ezcomponents.org/docs/tutorials/Graph
 */

require 'ezc/Base/ezc_bootstrap.php';

$log = fopen('results/raw.csv', 'r');
fgetcsv($log);

$data = array(
    'memory' => array(),
    'eps' => array()
);

while ($line = fgetcsv($log)) {
    list ($elements, $file, $time, $eps, $memory) = $line;
    list ($component, $test) = explode('-', str_replace('.php', '', $file));
    $data['memory'][$component][$test][$elements] = ceil($memory / 1024); // convert to KB
    $data['eps'][$component][$test][$elements] = $eps;
}

fclose($log);

foreach ($data as $measurement => $components) {
    foreach ($components as $component => $tests) {
        echo $component, ' - ', $measurement, PHP_EOL;

        $graph = new ezcGraphBarChart;
        $graph->driver = new ezcGraphGdDriver;
        $graph->options->font = '/usr/share/fonts/truetype/msttcorefonts/arial.ttf';
        $graph->driver->options->supersampling = 1;
        $graph->driver->options->imageFormat = IMG_PNG;
        $graph->xAxis->label = 'Elements';
        $graph->yAxis->label = ($measurement == 'memory') ? 'Memory (KB)' : 'Executions / Second';
        $graph->title = $component . ' - ' . $measurement;
        foreach ($tests as $test => $elements) {
            $graph->data[$test] = new ezcGraphArrayDataSet($elements);
        }
        $graph->render(400, 225, 'results/' . $component . '_' . $measurement . '.png');
    }
}
