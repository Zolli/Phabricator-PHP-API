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

        $params = $this->rawData;
        $params['__conduit__'] = ['token' => $this->token];

        $returnedData['params'] = json_encode($params);
        $returnedData['output'] = $this->output;
        $returnedData['__conduit__'] = TRUE;

        return $returnedData;
    }

}