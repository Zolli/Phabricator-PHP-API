<?php namespace Phabricator;

use Phabricator\Client\ClientInterface;

/**
 * Class ClientAwareTrait
 *
 * @package Phabricator
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @version 1.0.0
 */
trait ClientAwareTrait {

    /**
     * @var \Phabricator\Client\ClientInterface
     */
    protected $client;

    /**
     * set the current client implementation
     *
     * @param \Phabricator\Client\ClientInterface $client
     */
    public function setClient(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * Returns the current client
     *
     * @return \Phabricator\Client\ClientInterface
     */
    public function getClient() {
        return $this->client;
    }

} 