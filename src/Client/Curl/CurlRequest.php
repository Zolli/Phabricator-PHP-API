<?php namespace Phabricator\Client\Curl;

use BuildR\Foundation\Exception\RuntimeException;
use Phabricator\Phabricator;

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

        $this->initialize();
    }

    /**
     * Initialize the CURL session
     */
    public function initialize() {
        $this->handler = curl_init();

        $this->setOption(CURLOPT_URL, $this->requestUrl)
             ->setOption(CURLOPT_VERBOSE, 0)
             ->setOption(CURLOPT_HEADER, 0);
    }

    /**
     * Set an opt in current curl handler
     *
     * @param int $option
     * @param mixed $value
     *
     * @throws \BuildR\Foundation\Exception\RuntimeException
     *
     * @return \Phabricator\Client\Curl\CurlRequest
     */
    public function setOption($option, $value) {
        //Silence it because errors handled differently
        $res = @curl_setopt($this->handler, $option, $value);

        if($res === TRUE) {
            return $this;
        }

        throw new RuntimeException('Failed to set the following option: ' . $option);
    }

    /**
     * Set multiple options with an associative array
     *
     * @param $options
     *
     * @return \Phabricator\Client\Curl\CurlRequest
     */
    public function setOptionFromArray($options) {
        foreach($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * Set the posted data. And also set the CURLOPT_POST option to TRUE, if is
     * not set already.
     *
     * @param array $postData
     *
     * @codeCoverageIgnore
     */
    public function setPostData(array $postData) {
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
     * Execute the prepared request and optionally returns the response
     *
     * @param bool $returnTransfer Returns the returned response
     *
     * @throws \BuildR\Foundation\Exception\RuntimeException
     *
     * @return array|\stdClass
     *
     * @codeCoverageIgnore
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
