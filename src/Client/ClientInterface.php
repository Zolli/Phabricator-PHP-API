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
     * Returns the current client unique name
     *
     * @return string
     */
    public function getClientName();

    /**
     * Returns the description of the current client. Return an empty string
     * if you not want any detailed description
     *
     * @return string
     */
    public function getClientDescription();

    /**
     * Returns the current client version number in semantic format
     * e.g. (1.1.4)
     *
     * @return string
     */
    public function getClientVersion();

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