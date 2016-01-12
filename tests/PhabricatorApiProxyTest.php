<?php namespace Phabricator\Tests;

use BuildR\TestTools\BuildR_TestCase;
use Phabricator\Phabricator;
use Phabricator\Endpoints\Defaults\File;
use Phabricator\Endpoints\Defaults\Conduit;
use Phabricator\Client\Curl\CurlClient;
use Phabricator\Tests\Fixtures\Endpoints\UniqueFileHandler;
use \ReflectionClass;
use ReflectionMethod;

class PhabricatorApiProxyTest extends BuildR_TestCase {

    /**
     * @type \Phabricator\Phabricator
     */
    protected $instance;

    protected $baseUrl = 'http://phabricator.example.com';

    protected $dummyToken = 'cli-as8vrszfsevfshvsef789e';

    public function setUp() {
        parent::setUp();

        $this->instance = new Phabricator($this->baseUrl, $this->dummyToken);
    }

    public function tearDown() {
        parent::tearDown();

        unset($this->instance);
    }

    public function testItCreatesTheDefaultClientIfNoOneProvided() {
        $this->assertInstanceOf(CurlClient::class, $this->instance->getClient());
    }

    /**
     * @expectedException \BuildR\Foundation\Exception\RuntimeException
     * @expectedExceptionMessage This handler class (Phabricator\Handler\Conduit\NonExistingHandler) not found!
     */
    public function testItThrowsExceptionWhenTryToPushNonExistingHandler() {
        $this->instance->pushEndpointHandler('conduit', 'Phabricator\\Handler\\Conduit\\NonExistingHandler');
    }

    public function testIsPushesEndpointHandlersCorrectly() {
        //Pushed dummy object to the cache
        $cache = ['File' => (new \stdClass())];
        $this->setProperty($this->instance, 'endpointObjectCache', $cache);

        $this->instance->pushEndpointHandler('FIlE', UniqueFileHandler::class);
        $cache = $this->getPropertyValue(Phabricator::class, 'endpointObjectCache', $this->instance);

        $this->assertTrue(is_array($cache));
        $this->assertArrayNotHasKey('File', $cache);
    }

    public function testItReturnsTheEndpointHandlerCorrectly() {
        $reflector = new ReflectionClass(File::class);
        $options = ['methodParams' => ['File', $reflector]];
        $result = $this->invokeMethod(Phabricator::class, 'getEndpointHandler', $this->instance, $options);

        $this->assertInstanceOf(File::class, $result);
    }

    public function testItReturnsTheEndpointHandlerWhenCacheIsExist() {
        $cache = ['File' => (new UniqueFileHandler($this->instance->getClient()))];
        $this->setProperty($this->instance, 'endpointObjectCache', $cache);

        $reflector = new ReflectionClass(File::class);
        $options = ['methodParams' => ['File', $reflector]];
        $result = $this->invokeMethod(Phabricator::class, 'getEndpointHandler', $this->instance, $options);

        $this->assertInstanceOf(UniqueFileHandler::class, $result);
    }

    public function testItReturnsTheProperExecutorMethod() {
        $reflector = new ReflectionClass(UniqueFileHandler::class);
        $options = ['methodParams' => ['download', $reflector]];
        $result = $this->invokeMethod(Phabricator::class, 'getExecutorMethod', $this->instance, $options);

        $this->assertInstanceOf(ReflectionMethod::class, $result);
        $this->assertEquals('defaultExecutor', $result->getName());

        $options = ['methodParams' => ['upload', $reflector]];
        $result = $this->invokeMethod(Phabricator::class, 'getExecutorMethod', $this->instance, $options);

        $this->assertInstanceOf(ReflectionMethod::class, $result);
        $this->assertEquals('uploadExecutor', $result->getName());
    }

    public function testItReturnsTheHandlerClassNameProperly() {
        $this->instance->pushEndpointHandler('File', UniqueFileHandler::class);

        $options = ['methodParams' => ['File']];
        $result = $this->invokeMethod(Phabricator::class, 'getHandlerClassName', $this->instance, $options);

        $this->assertEquals(UniqueFileHandler::class, $result);

        $options = ['methodParams' => ['Conduit']];
        $result = $this->invokeMethod(Phabricator::class, 'getHandlerClassName', $this->instance, $options);

        $this->assertEquals(Conduit::class, $result);
    }

    /**
     * @expectedException \BuildR\Foundation\Exception\Exception
     * @expectedExceptionMessage The arguments not contains the method name!
     */
    public function testItThrowsExceptionWhenMalformedArguments() {
        $options = ['methodParams' => [[]]];
        $this->invokeMethod(Phabricator::class, 'getDataByArguments', $this->instance, $options);
    }

    public function testIsParseArgumentsCorrectly() {
        $options = ['methodParams' => [['File', ['name' => 'dummy']]]];
        $result = $this->invokeMethod(Phabricator::class, 'getDataByArguments', $this->instance, $options);

        $this->assertArrayHasKey('methodName', $result);
        $this->assertEquals('File', $result['methodName']);
        $this->assertArrayHasKey('methodArgs', $result);
        $this->assertCount(1, $result['methodArgs']);

        $options = ['methodParams' => [['File']]];
        $result = $this->invokeMethod(Phabricator::class, 'getDataByArguments', $this->instance, $options);

        $this->assertCount(0, $result['methodArgs']);
    }

}