<?php namespace Phabricator\Tests\Client;

use BuildR\TestTools\BuildR_TestCase;
use Phabricator\Client\Curl\CurlClient;

class CurlClientTest extends BuildR_TestCase {

    /**
     * @type \Phabricator\Client\Curl\CurlClient
     */
    protected $client;

    public function setUp() {
        $this->client = new CurlClient();

        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();

        unset($this->client);
    }

    public function testItStoreOptionsCorrectly() {
        $this->client->setOption(CURLOPT_AUTOREFERER, TRUE)
            ->setOption(CURLOPT_PORT, 65535);

        $result = $this->getPropertyValue(CurlClient::class, 'options', $this->client);

        $this->assertCount(2, $result);
    }

    public function testItStoreOptionsProperlyFromArray() {
        $options = [
            CURLOPT_AUTOREFERER => TRUE,
            CURLOPT_PORT => 8080,
        ];

        $this->client->setOptionArray($options);
        $result = $this->getPropertyValue(CurlClient::class, 'options', $this->client);

        $this->assertCount(2, $result);
    }



}
