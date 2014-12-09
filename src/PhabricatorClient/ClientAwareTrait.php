<?php namespace Phabricator;

use Phabricator\Client\ClientInterface;

trait ClientAwareTrait {

    /**
     * @var \Phabricator\Client\ClientInterface
     */
    protected $client;

    /**
     * set the current client implementation
     *
     * @param \Phabricator\Client\ClientInterface $client
     */
    public function setClient(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * Returns the current client
     *
     * @return \Phabricator\Client\ClientInterface
     */
    public function getClient() {
        return $this->client;
    }

} 