<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/test-credentials.php';

$iTopClient = new \iTopApi\iTopClient($endpoint,$user,$password);

$apaches =    $iTopClient->coreGet('Daemon','Apache');

foreach($apaches['objects'] as $apache) {
    echo $apache['fields']['name']."\t".$apache['fields']['conf_file'].PHP_EOL;
}

