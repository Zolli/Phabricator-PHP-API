<?php namespace Phabricator;

use Phabricator\Client\ClientInterface;

/**
 * Client aware trait
 *
 * Phabricator PHP API
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @package Phabricator
 *
 * @copyright    Copyright 2016, Zoltán Borsos.
 * @license      https://github.com/Zolli/Phabricator-PHP-API/blob/master/LICENSE.md
 * @link         https://github.com/Zolli/Phabricator-PHP-API
 */
trait ClientAwareTrait {

    /**
     * @type \Phabricator\Client\ClientInterface
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
     * returns the currently used client
     *
     * @return \Phabricator\Client\ClientInterface
     */
    public function getClient() {
        return $this->client;
    }

} 