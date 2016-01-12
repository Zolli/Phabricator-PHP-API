<?php namespace Phabricator\Tests\Request;

use BuildR\TestTools\BuildR_TestCase;
use Phabricator\Request\RequestData;

class RequestDataTest extends BuildR_TestCase {

    protected $dummyToken = 'api-5sdf48esa1fe8a6fcea';

    public function testItCreatesTheRequestDataProperly() {
        $data = ['status' => ['status-open']];

        $requestData = new RequestData($data, $this->dummyToken);
        $result = $requestData->getResult();

        $this->assertArrayHasKey('params', $result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('__conduit__', $result);

        $paramsResult = json_decode($result['params'], TRUE);

        $this->assertArrayHasKey('status', $paramsResult);
        $this->assertArrayHasKey('__conduit__', $paramsResult);
    }

    public function testItMergesConduitMetaDataCorrectly() {
        $data = ['status' => ['status-open'], '__conduit__' =>
            [
                'token' => 'cli-asdsf67sdgfs678tc',
                'auth.signature' => 'hfe8f7he97fe7gceftg7qaewf',
            ]
        ];

        $dataNoUniqueToken = ['status' => ['status-open'], '__conduit__' =>
            [
                'auth.signature' => 'hfe8f7he97fe7gceftg7qaewf',
            ]
        ];

        $requestData = new RequestData($data, $this->dummyToken);
        $options = ['methodParams' => [$data, $this->dummyToken]];
        $mergeResult = $this->invokeMethod(RequestData::class, 'mergeConduitMetaData', $requestData, $options);

        $this->assertArrayHasKey('status', $mergeResult);
        $this->assertArrayHasKey('__conduit__', $mergeResult);
        $this->assertArrayHasKey('token', $mergeResult['__conduit__']);
        $this->assertArrayHasKey('auth.signature', $mergeResult['__conduit__']);
        $this->assertEquals('cli-asdsf67sdgfs678tc', $mergeResult['__conduit__']['token']);

        $options = ['methodParams' => [$dataNoUniqueToken, $this->dummyToken]];
        $mergeResult = $this->invokeMethod(RequestData::class, 'mergeConduitMetaData', $requestData, $options);

        //Holds the default token if no one specified
        $this->assertArrayHasKey('auth.signature', $mergeResult['__conduit__']);
        $this->assertEquals($this->dummyToken, $mergeResult['__conduit__']['token']);
    }

}