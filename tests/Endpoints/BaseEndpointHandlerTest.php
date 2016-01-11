<?php namespace Phabricator\Tests\Endpoints;

use BuildR\TestTools\BuildR_TestCase;
use Phabricator\Client\Curl\CurlClient;
use Phabricator\Endpoints\BaseEndpoint;

class BaseEndpointHandlerTest extends BuildR_TestCase {

    public function testItStoreClientCorrectly() {
        $client = new CurlClient();
        $baseHandler = new BaseEndpoint($client);
        $storedInstance = $this->getPropertyValue(BaseEndpoint::class, 'client', $baseHandler);

        $this->assertInstanceOf(CurlClient::class, $storedInstance);
        $this->assertSame($client, $storedInstance);
    }

}