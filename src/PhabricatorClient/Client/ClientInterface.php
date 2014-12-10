<?php namespace Phabricator\Client;

/**
 * Interface ClientInterface
 *
 * @package Phabricator\Client
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @version 1.0.0
 */
interface ClientInterface {

    public function connect();
    public function setBaseData($data);
    public function getAuthData();
    public function getClientName();
    public function getClientVersion();
    public function isConnected();
    public function request($url, $requestData);

} 