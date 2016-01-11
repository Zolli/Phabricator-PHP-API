<?php namespace Phabricator\Endpoints;

use Phabricator\Client\ClientInterface;

/**
 * Base class to handle endpoint methods
 *
 * Phabricator PHP API
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @package Phabricator
 * @subpackage Endpoints
 *
 * @copyright    Copyright 2016, Zoltán Borsos.
 * @license      https://github.com/Zolli/Phabricator-PHP-API/blob/master/LICENSE.md
 * @link         https://github.com/Zolli/Phabricator-PHP-API
 */
class BaseEndpoint {

    /**
     * @type \Phabricator\Client\ClientInterface
     */
    protected $client;

    /**
     * Constructor
     * Set the client in this class
     *
     * @param \Phabricator\Client\ClientInterface $client
     */
    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function defaultExecutor($requestUrl, $requestData) {
        return $this->client->request($requestUrl, $requestData);
    }

} 