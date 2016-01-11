<?php namespace Phabricator\Response;

use BuildR\Foundation\Exception\RuntimeException;

/**
 * Object representation of a conduit API response
 *
 * Phabricator PHP API
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @package Phabricator
 * @subpackage Response
 *
 * @copyright    Copyright 2016, Zoltán Borsos.
 * @license      https://github.com/Zolli/Phabricator-PHP-API/blob/master/LICENSE.md
 * @link         https://github.com/Zolli/Phabricator-PHP-API
 */
class ConduitResponse {

    /**
     * The raw result data
     *
     * @type array
     */
    protected $result;

    /**
     * ConduitResponse constructor.
     *
     * @param string $result Raw JSON response
     */
    public function __construct($result) {
        $this->result = $this->processRawResponse($result);
    }

    /**
     * Process the raw JSON response, given from conduit.
     * Throw an exception when given JSON is malformed
     *
     * @param $rawResult
     *
     * @throws \BuildR\Foundation\Exception\RuntimeException
     *
     * @return mixed
     */
    protected function processRawResponse($rawResult) {
        $result = json_decode($rawResult, TRUE);

        if(json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Malformed JSON response! Message: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Determines that the current response is successful or not
     *
     * @return bool
     */
    public function isSuccessful() {
        return ($this->result['error_code'] === NULL && $this->result['error_info'] === NULL) ? TRUE : FALSE;
    }

    /**
     * Determines that the current response is failed or not
     *
     * @return bool
     */
    public function isFailed() {
        return !$this->isSuccessful();
    }

    /**
     * Returns the error information from the response. Returns an empty string when
     * the API is not provide textual information
     *
     * @return string
     */
    public function getErrorInfo() {
        return (!empty($this->result['error_info'])) ? $this->result['error_info'] : '';
    }

    /**
     * Returns the error code from the API response, if tha request is not successes
     * This is not valid "error code". The conduit API returns short (constant like)
     * text, that represents the error, like: ERR_CERT_INVALID
     *
     * @return string
     */
    public function getErrorCode() {
        return (!empty($this->result['error_code'])) ? $this->result['error_code'] : '';
    }

    /**
     * Returns the API response
     *
     * @return array
     */
    public function getResult() {
        return $this->result['result'];
    }

}