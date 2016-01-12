<?php namespace Phabricator\Request;

class RequestData {

    /**
     * Raw data passed to API
     *
     * @type array
     */
    protected $rawData;

    /**
     * API token
     *
     * @type string
     */
    protected $token;

    /**
     * The wanted output format
     *
     * @type string
     */
    protected $output;

    public function __construct(array $rawParams, $authToken, $output = 'json') {
        $this->rawData = $rawParams;
        $this->token = $authToken;
        $this->output = $output;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult() {
        return $this->process();
    }

    protected function process() {
        $returnedData = [];

        $params = $this->mergeConduitMetaData($this->rawData, $this->token);

        $returnedData['params'] = json_encode($params);
        $returnedData['output'] = $this->output;

        //This indicates the API to the client expects conduit response.
        //This sometimes provide more detailed error messages.
        $returnedData['__conduit__'] = TRUE;

        return $returnedData;
    }

    protected function mergeConduitMetaData($rawData, $token) {
        $params = $rawData;
        $tokenMeta = ['token' => $token];

        if(!isset($params['__conduit__'])) {
            $params['__conduit__'] = $tokenMeta;

            return $params;
        }

        $existingMeta = $params['__conduit__'];
        $params['__conduit__'] = array_merge($tokenMeta, $existingMeta);

        return $params;
    }

}