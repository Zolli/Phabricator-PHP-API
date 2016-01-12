<?php namespace Phabricator\Tests;

use BuildR\TestTools\BuildR_TestCase;
use Phabricator\Client\Curl\CurlClient;
use Phabricator\Tests\Fixtures\Traits\ClientAwareTraitImpl;

class ClientAwareTraitTest extends BuildR_TestCase {

    /**
     * @type \Phabricator\Tests\Fixtures\Traits\ClientAwareTraitImpl
     */
    protected $impl;

    public function setUp(){
        parent::setUp();

        $this->impl = new ClientAwareTraitImpl();
    }

    public function tearDown() {
        parent::tearDown();

        unset($this->impl);
    }

    public function testItStoreClientsCorrectly() {
        $this->assertNull($this->impl->getClient());

        $this->impl->setClient(new CurlClient());

        $this->assertInstanceOf(CurlClient::class, $this->impl->getClient());
    }


}