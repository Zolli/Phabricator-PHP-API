<?php namespace Phabricator\Client;

interface ClientInterface {

    public function connect();
    public function setBaseData($data);
    public function getAuthData();
    public function getClientName();
    public function getClientVersion();
    public function isConnected();
    public function request($url, $requestData);

} 