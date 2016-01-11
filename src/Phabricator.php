<?php namespace Phabricator;

use BuildR\Foundation\Exception\RuntimeException;
use Phabricator\ClientAwareTrait;
use Phabricator\Client\ClientInterface;
use Phabricator\Client\Curl\CurlClient;
use Phabricator\Exception\UnimplementedEndpointException;
use Phabricator\Request\RequestData;
use ReflectionClass;
use ReflectionException;

/**
 * Phabricator PHP API main class that manage API class and result printing
 *
 * Phabricator PHP API
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @package Phabricator
 *
 * @copyright    Copyright 2016, Zoltán Borsos.
 * @license      https://github.com/Zolli/Phabricator-PHP-API/blob/master/LICENSE.md
 * @link         https://github.com/Zolli/Phabricator-PHP-API
 *
 * @method object User(string $methodName, array $methodData = []) Execute the method on User endpoint
 * @method object Token(string $methodName, array $methodData = []) Execute the method on Token endpoint
 * @method object Slowvote(string $methodName, array $methodData = []) Execute the method on Slowvote endpoint
 * @method object Repository(string $methodName, array $methodData = []) Execute the method on Repository endpoint
 * @method object Rremarkup(string $methodName, array $methodData = []) Execute the method on Remarkup endpoint
 * @method object Releephwork(string $methodName, array $methodData = []) Execute the method on Releephwork endpoint
 * @method object Releeph(string $methodName, array $methodData = []) Execute the method on Releeph endpoint
 * @method object Project(string $methodName, array $methodData = []) Execute the method on Project endpoint
 * @method object Phriction(string $methodName, array $methodData = []) Execute the method on Phriction endpoint
 * @method object Phrequest(string $methodName, array $methodData = []) Execute the method on Phrequest endpoint
 * @method object Phragment(string $methodName, array $methodData = []) Execute the method on Phragment endpoint
 * @method object Phid(string $methodName, array $methodData = []) Execute the method on Phid endpoint
 * @method object Phame(string $methodName, array $methodData = []) Execute the method on Phame endpoint
 * @method object paste(string $methodName, array $methodData = []) Execute the method on paste endpoint
 * @method object Passphare(string $methodName, array $methodData = []) Execute the method on Passphare endpoint
 * @method object Nuance(string $methodName, array $methodData = []) Execute the method on Nuance endpoint
 * @method object Maniphest(string $methodName, array $methodData = []) Execute the method on Maniphest endpoint
 * @method object Macro(string $methodName, array $methodData = []) Execute the method on Macro endpoint
 * @method object Harbormaster(string $methodName, array $methodData = []) Execute the method on Harbormaster endpoint
 * @method object Flag(string $methodName, array $methodData = []) Execute the method on Flag endpoint
 * @method object File(string $methodName, array $methodData = []) Execute the method on File endpoint
 * @method object Feed(string $methodName, array $methodData = []) Execute the method on Feed endpoint
 * @method object Diffusion(string $methodName, array $methodData = []) Execute the method on Diffusion endpoint
 * @method object Differential(string $methodName, array $methodData = []) Execute the method on Differential endpoint
 * @method object Conpherence(string $methodName, array $methodData = []) Execute the method on Conpherence endpoint
 * @method object Conduit(string $methodName, array $methodData = []) Execute the method on Conduit endpoint
 * @method object Chatlog(string $methodName, array $methodData = []) Execute the method on Chatlog endpoint
 * @method object Audit(string $methodName, array $methodData = []) Execute the method on Audit endpoint
 * @method object Almanac(string $methodName, array $methodData = []) Execute the method on Almanac endpoint
 */
class Phabricator {

    use ClientAwareTrait;

    /**
     * @type string Where is phabricator located
     */
    protected $phabricatorUrl;

    /**
     * @type string Contains the authenticated user token
     */
    protected $conduitToken;

    /**
     * @type array Cache the constructed endpoint ebjects
     */
    private $endpointObjectCache;

    /**
     * @type array Contains all unique endpoint handler
     */
    private $uniqueEndpointHandlers;

    /**
     * Phabricator constructor
     *
     * @param string $baseUrl
     * @param string $token
     * @param \Phabricator\Client\ClientInterface $client
     */
    public function __construct($baseUrl, $token, ClientInterface $client = NULL) {
        if($client === NULL) {
            $client = new CurlClient();
        }

        $this->setClient($client);

        $this->conduitToken = $token;
        $this->phabricatorUrl = $baseUrl;
    }

    /**
     * Pushes a unique handler to the stack. Unique handlers are preferred, over default handlers.
     * One endpoint only have on unique handler, and if you push another it will overwrite the previous
     *
     * @param string $apiName
     * @param string $handlerClassName The handler FQCN
     */
    public function pushEndpointHandler($apiName, $handlerClassName) {
        $apiName = ucfirst(strtolower($apiName));

        $this->uniqueEndpointHandlers[$apiName] = $handlerClassName;
    }

    /**
     * Proxy for undefined methods
     *
     * @param string $apiName The endpoint name
     * @param array $arguments arguments
     *
     * @throws \Phabricator\Exception\UnimplementedEndpointException
     *
     * @return \stdClass|NULL
     */
    public function __call($apiName, $arguments) {
        $argData = $this->getDataByArguments($arguments);

        $methodName = $argData['methodName'];
        $requestData = (new RequestData($argData['methodArgs'], $this->conduitToken))->getResult();
        $requestUrl = $this->phabricatorUrl . '/api/' . strtolower($apiName) . "." . strtolower($methodName);
        $neededClass = $this->getHandlerClassName($apiName);

        try {
            $endpointReflector = new ReflectionClass($neededClass);
        } catch(ReflectionException $e) {
            throw new UnimplementedEndpointException("This API endpoint: {$apiName} is not implemented yet!");
        }

        $methodReflector = $this->getExecutorMethod($methodName, $endpointReflector);
        $endpointInstance = $this->getEndpointHandler($apiName, $endpointReflector);

        //Returning the response from request
        return $methodReflector->invokeArgs($endpointInstance, [$requestUrl, $requestData]);
    }

    protected function getEndpointHandler($apiName, $endpointReflector) {
        if(isset($this->endpointObjectCache[$apiName])) {
            return $this->endpointObjectCache[$apiName];
        }

        //Create a new instance and store it
        $endpointInstance = $endpointReflector->newInstanceArgs([$this->getClient()]);
        $this->endpointObjectCache[$apiName] = $endpointInstance;

        return $endpointInstance;
    }

    protected function getExecutorMethod($methodName, ReflectionClass $endpointReflector) {
        $neededMethod = strtolower($methodName) . "Executor";

        if(!$endpointReflector->hasMethod($neededMethod)) {
            $neededMethod = "defaultExecutor";
        }

        return $endpointReflector->getMethod($neededMethod);
    }

    protected function getHandlerClassName($apiName) {
        $apiName = ucfirst(strtolower($apiName));
        $neededClass = __NAMESPACE__ . '\\' . 'Endpoints\\Defaults\\' . $apiName;

        if(isset($this->uniqueEndpointHandlers[$apiName])) {
            $neededClass = get_class($this->uniqueEndpointHandlers[$apiName]);
        }

        return $neededClass;
    }

    /**
     * Get the base date by the passed array. The returned array contains the method name (on endpoint)
     * and the arguments that the called method can give.
     *
     * Returned array keys:
     * - (string) methodName
     * - (array) methodArgs
     *
     * @param array $arguments The magic method argument array
     *
     * @throws \BuildR\Foundation\Exception\RuntimeException
     *
     * @return array
     */
    protected function getDataByArguments(array $arguments) {
        if(!isset($arguments[0])) {
            throw new RuntimeException('The arguments not contains the method name!');
        }

        $methodName = (string) $arguments[0];
        $methodArgs = [];

        if(isset($arguments[1]) && is_array($arguments[1])) {
            $methodArgs = $arguments[1];
        }

        return [
            'methodName' => $methodName,
            'methodArgs' => $methodArgs,
        ];
    }

} 