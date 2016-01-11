<?php namespace Phabricator\Endpoints;

/**
 * Common interface for various endpoint handler
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
interface EndpointInterface {

    /**
     * Default command executor method.
     * This is a fallback, if no unique executor default in the handler this method will be invoked
     *
     * @param string $requestUrl The request full URL
     * @param array $requestData The request data as array
     *
     * @return \stdClass|NULL
     */
    public function defaultExecutor($requestUrl, $requestData);

} 