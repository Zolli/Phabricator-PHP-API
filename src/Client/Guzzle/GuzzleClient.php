<?php namespace Phabricator\Client\Guzzle;

use BuildR\Foundation\Exception\RuntimeException;
use GuzzleHttp\Client;
use Phabricator\Client\ClientInterface;

/**
 * Simple Guzzle based client
 *
 * Phabricator PHP API
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @package Phabricator
 * @subpackage Client\Guzzle
 *
 * @copyright    Copyright 2016, Zoltán Borsos.
 * @license      https://github.com/Zolli/Phabricator-PHP-API/blob/master/LICENSE.md
 * @link         https://github.com/Zolli/Phabricator-PHP-API
 */
class GuzzleClient implements ClientInterface {

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function request($url, $requestData) {
        if(!class_exists('GuzzleHttp\Client')) {
            throw new RuntimeException('The guzzle client is not installed. Please install uzzlehttp/guzzle');
        }

        $client = new Client();

        //We do not care exceptions, this will handled by the user
        $response = $client->request('POST', $url, ['form_params' => $requestData]);

        return $response->getBody()->getContents();
    }


}
