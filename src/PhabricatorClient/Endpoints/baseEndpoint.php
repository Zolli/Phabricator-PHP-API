<?php namespace Phabricator\Endpoints;

use Phabricator\Client\ClientInterface;

class baseEndpoint {

    /**
     * @var \Phabricator\Client\ClientInterface
     */
    protected $client;

    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $methodName The called method name, like project.query
     * @param $arguments The arguments passed to the method as JSON
     * @return \stdClass|null
     */
    public function defaultExecutor($methodName, $arguments) {
        return $this->client->request($methodName, $arguments);
    }

} 