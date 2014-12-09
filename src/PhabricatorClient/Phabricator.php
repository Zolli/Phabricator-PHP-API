<?php namespace Phabricator;

use Phabricator\Client\ClientInterface;
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
     * Call on any undefined method allow to use like this:
     * $instance->Project("query");
     *
     * @param $apiName
     * @param $arguments
     * @throws \Phabricator\Exception\UnimplementedEndpointMethodException
     */
    public function __call($apiName, $arguments) {
        $methodName = $arguments[0];
        $funcArgs = [];

        if(isset($arguments[1]) AND is_array($arguments[1])) {
            $funcArgs = $arguments[1];
        }

        $neededClass = __NAMESPACE__ . "\\" . "Endpoints\\" . $apiName;
        $neededMethod = ucfirst(strtolower($methodName)) . "Executor";
        $endpointReflector = new \ReflectionClass($neededClass);

        if(!$endpointReflector->hasMethod($neededMethod)) {
            throw new UnimplementedEndpointMethodException("The following endpoint method is not implemented in current version: " . ucfirst(strtolower($methodName)) . "!");
        }

        $endpointInstance = $endpointReflector->newInstanceArgs([$this->getClient()]);
        $endpointReflector->getMethod($neededMethod)->invokeArgs($endpointInstance, [$funcArgs]);
    }



} 