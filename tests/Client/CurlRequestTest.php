<?php namespace Phabricator\Tests\Client;

use BuildR\Foundation\Exception\RuntimeException;
use BuildR\TestTools\BuildR_TestCase;
use Phabricator\Client\Curl\CurlRequest;

class CurlRequestTest extends BuildR_TestCase{

    public function curlOptionDataProvider() {
        return [
            [CURLOPT_AUTOREFERER, TRUE, TRUE],
            [CURLOPT_RETURNTRANSFER, TRUE, TRUE],
            [28584584454, 'invalidParam', 'Failed to set the following option: 28584584454'],
        ];
    }

    /**
     * @dataProvider curlOptionDataProvider
     */
    public function testItSetsOptionsCorrectly($opt, $optValue, $exceptionMessage) {
        $url = 'https://httpbin.org/get';
        $request = new CurlRequest($url);

        try {
            $result = $request->setOption($opt, $optValue);

            if($exceptionMessage === TRUE) {
                $this->assertInstanceOf(CurlRequest::class, $result);
            }
        } catch(RuntimeException $e) {
            $this->assertEquals($exceptionMessage, $e->getMessage());
        }
    }

    public function testItSetsMultipleOptionCorrectly() {
        $url = 'https://httpbin.org/get';
        $request = new CurlRequest($url);

        $instance = $request->setOptionFromArray([CURLOPT_AUTOREFERER => TRUE, CURLOPT_RETURNTRANSFER => TRUE]);

        $this->assertInstanceOf(CurlRequest::class, $instance);
    }

    public function testIsCloseTheSessionCorrectly() {
        $url = 'https://httpbin.org/get';
        $request = new CurlRequest($url);
        $request->close();

        $handle = $this->getPropertyValue(CurlRequest::class, 'handler', $request);

        $this->assertEquals('Unknown', get_resource_type($handle));
    }

    public function testItInitializeProperly() {
        $url = 'https://httpbin.org/get';
        $request = new CurlRequest($url);
        $handle = $this->getPropertyValue(CurlRequest::class, 'handler', $request);

        $handleInfo = curl_getinfo($handle);

        $this->assertArrayHasKey('url', $handleInfo);
        $this->assertEquals($url, $handleInfo['url']);
    }


}