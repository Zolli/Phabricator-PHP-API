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
     * Hold options that set in request when creating it
     *
     * @type array
     */
    protected $options = [];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function request($url, $requestData) {
        $request = new CurlRequest($url);
        $request->setPostData($requestData);

        $this->setOptionsOnRequest($request, $this->options);

        return $request->execute();
    }

    /**
     * Set option for CURL request
     *
     * @param int $option CURLOPT_* constants
     * @param mixed $value The option value
     *
     * @return \Phabricator\Client\Curl\CurlClient
     */
    public function setOption($option, $value) {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Set CIRL request options from array. The array key is the option
     * and the value is used to option value.
     *
     * @param array $options
     *
     * @return \Phabricator\Client\Curl\CurlClient
     */
    public function setOptionArray(array $options) {
        $this->options = $options;

        return $this;
    }

    /**
     * Set the defined options on the given CurlRequest instance
     *
     * @param \Phabricator\Client\Curl\CurlRequest $request
     * @param $options
     *
     * @throws \BuildR\Foundation\Exception\RuntimeException
     *
     * @codeCoverageIgnore
     */
    protected function setOptionsOnRequest(CurlRequest $request, $options) {
        foreach($options as $option => $value) {
            $request->setOption($options, $value);
        }
    }

}
