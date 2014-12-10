<?php namespace Phabricator\Endpoints;

interface EndpointInterface {

    public function defaultExecutor($methodName, $arguments);

} 