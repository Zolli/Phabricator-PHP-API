<?php namespace Phabricator\Client;

/**
 * Common interface for various clients
 *
 * Phabricator PHP API
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @package Phabricator
 * @subpackage Client
 *
 * @copyright    Copyright 2016, Zoltán Borsos.
 * @license      https://github.com/Zolli/Phabricator-PHP-API/blob/master/LICENSE.md
 * @link         https://github.com/Zolli/Phabricator-PHP-API
 */
interface ClientInterface {

    /**
     * Make the request to the given URI
     *
     * @param string $url
     * @param array $requestData
     *
     * @return array
     */
    public function request($url, $requestData);

} 