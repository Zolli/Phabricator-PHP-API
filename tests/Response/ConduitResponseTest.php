<?php namespace Phabricator\Tests\Response;

use BuildR\TestTools\BuildR_TestCase;
use Phabricator\Response\ConduitResponse;

class ConduitResponseTest extends BuildR_TestCase {

    protected $validResponse = '{"result":"zolli-hu-03","error_code":null,"error_info":null}';
    protected $invalidResponse = '{"result":null,"error_code":"ERR-CONDUIT-CALL","error_info":"API Method \"conduit.query\" does not define these parameters: \'status\'."}';

    /**
     * @expectedException \BuildR\Foundation\Exception\Exception
     */
    public function testItThrowsExceptionWhenGivenJsonIsMalformed() {
        $options = ['methodParams' => ['{malformed json}']];
        $this->invokeMethod(ConduitResponse::class, 'processRawResponse', NULL, $options);
    }

    public function testIsProcessValidMessages() {
        $options = ['methodParams' => [$this->validResponse]];
        $result = $this->invokeMethod(ConduitResponse::class, 'processRawResponse', NULL, $options);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('error_code', $result);
        $this->assertArrayHasKey('error_info', $result);
    }

    public function testIsProcessValidResponses() {
        $rsp = new ConduitResponse($this->validResponse);

        $this->assertTrue($rsp->isSuccessful());
        $this->assertFalse($rsp->isFailed());
        $this->assertEquals('zolli-hu-03', $rsp->getResult());
        $this->assertEquals('', $rsp->getErrorCode());
        $this->assertEquals('', $rsp->getErrorInfo());
    }

    public function testIsProcessInvalidResponses() {
        $rsp = new ConduitResponse($this->invalidResponse);

        $this->assertFalse($rsp->isSuccessful());
        $this->assertTrue($rsp->isFailed());
        $this->assertNull($rsp->getResult());
        $this->assertEquals('ERR-CONDUIT-CALL', $rsp->getErrorCode());
        $this->assertEquals('API Method "conduit.query" does not define these parameters: \'status\'.', $rsp->getErrorInfo());
    }

}
