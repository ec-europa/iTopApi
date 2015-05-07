<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/test-credentials.php';

$iTopClient = new \iTopApi\iTopClient($endpoint,$user,$password);


var_dump(
    $iTopClient->operation('list_operations'),
    $iTopClient->operation('core/get',array(
        'class' => 'Person',
        'key' => 'SELECT Person'
    ))
);