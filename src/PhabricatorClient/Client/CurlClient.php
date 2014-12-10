<?php namespace Phabricator\Client;

class CurlClient implements ClientInterface {

    /**
     * @var string Client name shown on phabricator internal log
     */
    private $clientName = "Phabricator PHP API - CURL Client";

    /**
     * @var string Client version, show on phabricator internal log
     */
    private $clientVersion = "0.0.1";

    private $phabricatorUrl;
    private $authUser;
    private $certificateToken;

    /**
     * Indicates this client is authenticated successfully or not
     *
     * @var bool
     */
    private $isConnected = FALSE;

    /**
     * @var array Response to conduit.connect method. Store some connection info, such as session key and connectionID
     */
    private $connectionData;

    /**
     * Set object properties by given data (baseUrl, user, and token)
     *
     * @param $data
     */
    public function setBaseData($data) {
        if(!is_array($data)) {
            throw new \InvalidArgumentException("The data must be some sort off array");
        }

        $this->phabricatorUrl = $data['baseUrl'];
        $this->authUser = $data['authUser'];
        $this->certificateToken = $data['token'];
    }

    /**
     * Initialize the connection, and made the handshaking
     */
    public function connect() {
        $connectParams = $this->buildAuthParams();

        //Assemble post data
        $postData = [
            "params" => json_encode($connectParams),
            "output" => "json",
            "__conduit__" => true,
        ];

        //Assamble request
        $requestEndpoint = $this->phabricatorUrl . "/api/conduit.connect";
        $request = new CurlRequest($requestEndpoint);
        $request->setPostData($postData);
        $response = $request->execute();

        //Save data, and mark as connected
        $this->saveSessionData($response);
    }

    /**
     * Returns the data by initial connection
     *
     * @return array
     */
    public function getAuthData() {
        return $this->connectionData;
    }

    /**
     * Get the client name
     *
     * @return string
     */
    public function getClientName() {
        return $this->clientName;
    }

    /**
     * Get the client current version
     *
     * @return string
     */
    public function getClientVersion() {
        return $this->clientVersion;
    }

    /**
     * Return the initial connection (authentication) state
     *
     * @return bool
     */
    public function isConnected() {
        return $this->isConnected;
    }

    /**
     * Make a request to conduit
     *
     * @param string $url the endpoint name (Like, project.query)
     * @param $requestData The data posted to server
     * @return array|\stdObj
     */
    public function request($url, $requestData) {
        //Make sure we connected properly
        if(!$this->isConnected()) {
            $this->connect();
        }

        $requestData["__conduit__"] = $this->getAuthData();

        $postData = [
            "params" => json_encode($requestData),
            "output" => "json",
        ];

        $request = new CurlRequest($this->phabricatorUrl . "/api/" . $url);
        $request->setPostData($postData);

        $response = $request->execute();

        return $response;
    }

    /**
     * Save connection data to ebject, and mark session (this client) is connected
     *
     * @param $response
     */
    private function saveSessionData($response) {
        $this->isConnected = TRUE;

        $data = [
            "connectionId" => $response->connectionID,
            "sessionKey" => $response->sessionKey,
        ];

        $this->connectionData = $data;
    }

    /**
     * Create the params field for POST array, used for authentication only
     *
     * @return array
     */
    private function buildAuthParams() {
        $returnData = [];
        $time = time();

        $returnData['client'] = $this->getClientName();
        $returnData['clientVersion'] = $this->getClientVersion();

        $returnData['user'] = $this->authUser;
        $returnData['host'] = $this->phabricatorUrl;
        $returnData['authToken'] = $time;
        $returnData['authSignature'] = sha1($time . $this->certificateToken);

        return $returnData;
    }

}