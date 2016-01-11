<?php namespace Phabricator\Client\Curl;

use Phabricator\Client\ClientInterface;

/**
 * Simple CURL based client
 *
 * Phabricator PHP API
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @package Phabricator
 * @subpackage Client\Curl
 *
 * @copyright    Copyright 2016, Zoltán Borsos.
 * @license      https://github.com/Zolli/Phabricator-PHP-API/blob/master/LICENSE.md
 * @link         https://github.com/Zolli/Phabricator-PHP-API
 */
class CurlClient implements ClientInterface {

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function request($url, $requestData) {
        $request = new CurlRequest($url);
        $request->setPostData($requestData);

        return $request->execute();
    }

}