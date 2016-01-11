<?php namespace Phabricator\Endpoints;

use Phabricator\Client\ClientInterface;

/**
 * Class baseEndpoint
 *
 * @package Phabricator\Endpoints
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @version 1.0.0
 */
class BaseEndpoint {

    /**
     * @var \Phabricator\Client\ClientInterface
     */
    protected $client;

    /**
     * Constructor
     * Set the client in this class
     *
     * @param \Phabricator\Client\ClientInterface $client
     */
    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * fallback method if the method executor is not defined in the child class
     *
     * @param string $methodName The called method name, like project.query
     * @param $arguments The arguments passed to the method as JSON
     * @return \stdClass|null
     */
    public function defaultExecutor($methodName, $arguments) {
        return $this->client->request($methodName, $arguments);
    }

} 