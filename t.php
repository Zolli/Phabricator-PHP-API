<?php

include_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

$client = new \Phabricator\Client\Curl\CurlClient();
$api = new \Phabricator\Phabricator('http://project.zolli.hu', 'api-ubmifaodz3ygtirfwlia657susqx');

$rsp = $api->Project("query", ["status" => "status-open"]);

die(var_dump($rsp));