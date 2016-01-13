<?php namespace Phabricator;

use BuildR\Foundation\Exception\RuntimeException;
use Phabricator\ClientAwareTrait;
use Phabricator\Client\ClientInterface;
use Phabricator\Client\Curl\CurlClient;
use Phabricator\Exception\UnimplementedEndpointException;
use Phabricator\Request\RequestData;
use Phabricator\Response\ConduitResponse;
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
 * @method \Phabricator\Response\ConduitResponse User(string $methodName, array $methodData = []) Execute the method on User endpoint
 * @method \Phabricator\Response\ConduitResponse Token(string $methodName, array $methodData = []) Execute the method on Token endpoint
 * @method \Phabricator\Response\ConduitResponse Slowvote(string $methodName, array $methodData = []) Execute the method on Slowvote endpoint
 * @method \Phabricator\Response\ConduitResponse Repository(string $methodName, array $methodData = []) Execute the method on Repository endpoint
 * @method \Phabricator\Response\ConduitResponse Rremarkup(string $methodName, array $methodData = []) Execute the method on Remarkup endpoint
 * @method \Phabricator\Response\ConduitResponse Project(string $methodName, array $methodData = []) Execute the method on Project endpoint
 * @method \Phabricator\Response\ConduitResponse Phriction(string $methodName, array $methodData = []) Execute the method on Phriction endpoint
 * @method \Phabricator\Response\ConduitResponse Phrequest(string $methodName, array $methodData = []) Execute the method on Phrequest endpoint
 * @method \Phabricator\Response\ConduitResponse Phid(string $methodName, array $methodData = []) Execute the method on Phid endpoint
 * @method \Phabricator\Response\ConduitResponse Phame(string $methodName, array $methodData = []) Execute the method on Phame endpoint
 * @method \Phabricator\Response\ConduitResponse paste(string $methodName, array $methodData = []) Execute the method on paste endpoint
 * @method \Phabricator\Response\ConduitResponse Passphare(string $methodName, array $methodData = []) Execute the method on Passphare endpoint
 * @method \Phabricator\Response\ConduitResponse Owners(string $methodName, array $methodData = []) Execute the method on Owners endpoint
 * @method \Phabricator\Response\ConduitResponse Nuance(string $methodName, array $methodData = []) Execute the method on Nuance endpoint
 * @method \Phabricator\Response\ConduitResponse Maniphest(string $methodName, array $methodData = []) Execute the method on Maniphest endpoint
 * @method \Phabricator\Response\ConduitResponse Macro(string $methodName, array $methodData = []) Execute the method on Macro endpoint
 * @method \Phabricator\Response\ConduitResponse Harbormaster(string $methodName, array $methodData = []) Execute the method on Harbormaster endpoint
 * @method \Phabricator\Response\ConduitResponse Flag(string $methodName, array $methodData = []) Execute the method on Flag endpoint
 * @method \Phabricator\Response\ConduitResponse File(string $methodName, array $methodData = []) Execute the method on File endpoint
 * @method \Phabricator\Response\ConduitResponse Feed(string $methodName, array $methodData = []) Execute the method on Feed endpoint
 * @method \Phabricator\Response\ConduitResponse Diffusion(string $methodName, array $methodData = []) Execute the method on Diffusion endpoint
 * @method \Phabricator\Response\ConduitResponse Differential(string $methodName, array $methodData = []) Execute the method on Differential endpoint
 * @method \Phabricator\Response\ConduitResponse Conpherence(string $methodName, array $methodData = []) Execute the method on Conpherence endpoint
 * @method \Phabricator\Response\ConduitResponse Conduit(string $methodName, array $methodData = []) Execute the method on Conduit endpoint
 * @method \Phabricator\Response\ConduitResponse Chatlog(string $methodName, array $methodData = []) Execute the method on Chatlog endpoint
 * @method \Phabricator\Response\ConduitResponse Auth(string $methodName, array $methodData = []) Execute the method on Auth endpoint
 * @method \Phabricator\Response\ConduitResponse Audit(string $methodName, array $methodData = []) Execute the method on Audit endpoint
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
     *
     * @throws \BuildR\Foundation\Exception\RuntimeException
     */
    public function pushEndpointHandler($apiName, $handlerClassName) {
        if(!class_exists($handlerClassName)) {
            throw new RuntimeException('This handler class (' . $handlerClassName . ') not found!');
        }

        $apiName = ucfirst(strtolower($apiName));

        $this->uniqueEndpointHandlers[$apiName] = $handlerClassName;

        //Invalidate the cache, if exist
        if(isset($this->endpointObjectCache[$apiName])) {
            unset($this->endpointObjectCache[$apiName]);
        }
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
     *
     * @codeCoverageIgnore
     */
    public function __call($apiName, $arguments) {
        $apiName = ucfirst(strtolower($apiName));
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
        $result = $methodReflector->invokeArgs($endpointInstance, [$requestUrl, $requestData]);
        return new ConduitResponse($result);
    }

    /**
     * Returns a new instance from the given endpoint handler. In the instance creation the client is
     * passed to tha handler as parameter.
     *
     * This method also do runtime caching. All endpoint handler cached by name
     *
     * @param string $apiName Used as cache key name
     * @param \ReflectionClass $endpointReflector
     *
     * @return \Phabricator\Endpoints\EndpointInterface
     */
    protected function getEndpointHandler($apiName, ReflectionClass $endpointReflector) {
        if(isset($this->endpointObjectCache[$apiName])) {
            return $this->endpointObjectCache[$apiName];
        }

        //Create a new instance and store it
        $endpointInstance = $endpointReflector->newInstanceArgs([$this->getClient()]);
        $this->endpointObjectCache[$apiName] = $endpointInstance;

        return $endpointInstance;
    }

    /**
     * Return the reflector of the method that can execute the query on the
     * endpoint.
     *
     * @param string $methodName Like "query"
     * @param \ReflectionClass $endpointReflector
     *
     * @return \ReflectionMethod
     */
    protected function getExecutorMethod($methodName, ReflectionClass $endpointReflector) {
        $neededMethod = strtolower($methodName) . "Executor";

        if(!$endpointReflector->hasMethod($neededMethod)) {
            $neededMethod = "defaultExecutor";
        }

        return $endpointReflector->getMethod($neededMethod);
    }

    /**
     * Returns the FQCN of the handler class. Returns the default handler if no
     * unique handler available for the given endpoint.
     *
     * @param string $apiName Like "Project"
     *
     * @return string
     */
    protected function getHandlerClassName($apiName) {
        $apiName = ucfirst(strtolower($apiName));
        $neededClass = __NAMESPACE__ . '\\' . 'Endpoints\\Defaults\\' . $apiName;

        if(isset($this->uniqueEndpointHandlers[$apiName])) {
            $neededClass = $this->uniqueEndpointHandlers[$apiName];
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
