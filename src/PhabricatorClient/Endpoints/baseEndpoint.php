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

} 