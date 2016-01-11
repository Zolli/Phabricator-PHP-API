<?php namespace Phabricator\Client\Curl;

use BuildR\Foundation\Exception\RuntimeException;

/**
 * Class CurlRequest
 *
 * @package Phabricator\Client
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @version 1.0.0
 */
class CurlRequest {

    /**
     * @var string The url to the request be made
     */
    private $requestUrl;

    /**
     * @var Resource CURL resource
     */
    private $handler;

    /**
     * Constructor
     *
     * @param string $requestUrl
     */
    public function __construct($requestUrl) {
        $this->requestUrl = $requestUrl;

        $this->init();
    }

    /**
     * Initialize the CURL session
     */
    public function init() {
        $this->handler = curl_init();

        $this->setOption(CURLOPT_URL, $this->requestUrl)
             ->setOption(CURLOPT_VERBOSE, 0)
             ->setOption(CURLOPT_HEADER, 0);
    }

    /**
     * Set an opt in current curl handler
     *
     * @param $option
     * @param $value
     * @return \Phabricator\Client\CurlRequest
     * @throws \RuntimeException
     */
    public function setOption($option, $value) {
        $res = curl_setopt($this->handler, $option, $value);

        if($res === TRUE) {
            return $this;
        }

        throw new \RuntimeException("Failed to set the following opt: " . $option);
    }

    /**
     * Set multiple options with an associative array
     *
     * @param $options
     */
    public function setOptionFromArray($options) {
        foreach($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    /**
     * Set the posted data
     *
     * @param array $postData
     */
    public function setPostData($postData) {
        $this->setOption(CURLOPT_POST, 1)
             ->setOption(CURLOPT_POSTFIELDS, $postData);
    }

    /**
     * Close the current request
     */
    public function close() {
        curl_close($this->handler);
    }

    /**
     * Execute the request
     *
     * @param bool $processAsConduitResponse Return only the response body from the response
     *
     * @throws \BuildR\Foundation\Exception\RuntimeException
     *
     * @return array|\stdClass
     */
    public function execute($returnTransfer = TRUE) {
        //Need transfer return
        if($returnTransfer === TRUE) {
            $this->setOption(CURLOPT_RETURNTRANSFER, 1);
        }

        $result = curl_exec($this->handler);

        //Error handling
        if(curl_errno($this->handler)) {
            $format = [curl_errno($this->handler), curl_error($this->handler)];
            $this->close();
            throw RuntimeException::createByFormat('Error executing request, error code: %s, Message: %s', $format);
        }

        return $result;
    }

} 
