<?php namespace Phabricator;

use Phabricator\Client\ClientInterface;
use Phabricator\Endpoints\EndpointInterface;
use Phabricator\Exception\UnimplementedEndpointException;
use Phabricator\Exception\UnimplementedEndpointMethodException;

/**
 * Class Phabricator
 *
 * @package Phabricator
 *
 * @method object Project(string $methodName, array $methodData = []) Execute the method on Project endpoint
 */
class Phabricator {

    use ClientAwareTrait;

    /**
     * @var string Where is phabricator located
     */
    private $phabricatorUrl;

    /**
     * @var string Contains the authenticated user name
     */
    private $authUser;

    /**
     * @var string Contains the authenticated user token
     */
    private $conduitCertificateToken;

    /**
     * @var array Cache the constructed endpoint ebjects
     */
    private $endpointObjectCache;

    /**
     * @var array Contains all unique endpoint handler
     */
    private $uniqueEndpointHandlers;

    public function __construct(ClientInterface $client, $baseUrl, $authUser, $token) {
        $this->setClient($client);

        $this->authUser = $authUser;
        $this->conduitCertificateToken = $token;
        $this->phabricatorUrl = $baseUrl;

        $clientBaseData = [
            'baseUrl' => $this->phabricatorUrl,
            'authUser' => $this->authUser,
            'token' => $this->conduitCertificateToken,
        ];

        $client->setBaseData($clientBaseData);

        if($client->isConnected() === FALSE) {
            $client->connect();
        }
    }

    /**
     * Pushes a unique handler to the stack
     *
     * @param string $apiName
     * @param EndpointInterface $handler
     */
    public function pushEndpointHandler($apiName, EndpointInterface $handler) {
        $apiName = ucfirst(strtolower($apiName));

        $this->uniqueEndpointHandlers[$apiName] = $handler;
    }

    /**
     * Call on any undefined method allow to use like this:
     * $instance->Project("query");
     *
     * @param $apiName
     * @param $arguments
     * @throws \Phabricator\Exception\UnimplementedEndpointException
     * @return \stdClass|null
     */
    public function __call($apiName, $arguments) {
        $methodName = $arguments[0];
        $validMethodName = strtolower($apiName) . "." . strtolower($methodName);
        $funcArgs = [];

        if(isset($arguments[1]) AND is_array($arguments[1])) {
            $funcArgs = $arguments[1];
        }

        //Check for unique handler
        if(isset($this->uniqueEndpointHandlers[$apiName])) {
            $neededClass = get_class($this->uniqueEndpointHandlers[$apiName]);
        } else {
            $neededClass = __NAMESPACE__ . "\\" . "Endpoints\\" . $apiName;
        }

        //Figure out method name to execute the method
        $neededMethod = ucfirst(strtolower($methodName)) . "Executor";

        try {
            $endpointReflector = new \ReflectionClass($neededClass);
        } catch(\ReflectionException $e) {
            throw new UnimplementedEndpointException("This API endpoint: {$apiName} is not implemented yet!");
        }

        //Fallback method for not special, or unimplemented endpoint methods
        if(!$endpointReflector->hasMethod($neededMethod)) {
            $neededMethod = "defaultExecutor";
        }

        //Endpoint object caching
        if(!isset($this->endpointObjectCache[$apiName])) {
            $endpointInstance = $endpointReflector->newInstanceArgs([$this->getClient()]);
            $this->endpointObjectCache[$apiName] = $endpointInstance;
        } else {
            $endpointInstance = $this->endpointObjectCache[$apiName];
        }

        //Returning the response from request
        return $endpointReflector->getMethod($neededMethod)->invokeArgs($endpointInstance, [$validMethodName, $funcArgs]);
    }

} 