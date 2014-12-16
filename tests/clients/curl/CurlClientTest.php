<?php

/**
 * Class CurlClientTest
 *
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @version 1.0.0
 */
class CurlClientTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \Phabricator\Client\CurlClient
     */
    private $client;

    public function __construct($name = null, array $data = [], $dataName = '') {
        $this->client = new \Phabricator\Client\CurlClient();

        parent::__construct($name, $data, $dataName);
    }

    public function wrongBaseDataProvider() {
        return [
            ["string"],
            ["stringNull"],
            [null],
            [1],
            [false],
            ['char']
        ];
    }

    /**
     * @dataProvider wrongBaseDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testItThrowsExceptionWithEmptyData($data) {
        $this->client->setBaseData($data);
    }

    public function testItConnectsProperly() {
        $stub = $this->getMockBuilder("\\Phabricator\\Client\\CurlRequest");
        $stub->disableOriginalConstructor()->getMock()->expects($this->any())->method('execute')->will($this->returnValue("asd"));

        die(var_dump($stub->execute()));
    }

} 