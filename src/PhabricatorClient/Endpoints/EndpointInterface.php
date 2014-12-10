<?php namespace Phabricator\Endpoints;

/**
 * Interface EndpointInterface
 *
 * @package Phabricator\Endpoints
 * @author Zoltán Borsos <zolli07@gmail.com>
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @version 1.0.0
 */
interface EndpointInterface {

    public function defaultExecutor($methodName, $arguments);

} 