[![Build Status](http://ci.zolli.hu/buildStatus/icon?job=Phabricator%20PHP%20API)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![Build Stability](http://status.buildr-framework.io/buildstatus/status_modules.php?jobName=Phabricator%20PHP%20API/&type=stability)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![Code Coverage](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/?branch=master)
[![Test Results](http://status.buildr-framework.io/buildstatus/status_modules.php?jobName=Phabricator%20PHP%20API&type=tests)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![CRAP Report](http://status.buildr-framework.io/buildstatus/status_modules.php?jobName=Phabricator%20PHP%20API&type=crap)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![Dependency Status](https://www.versioneye.com/user/projects/5694edc7af789b0043000c0c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5694edc7af789b0043000c0c)
[![PHP7 Status](https://img.shields.io/badge/PHP7-tested-8892BF.svg)](https://github.com/BuildrPHP/Test-Tools)


# Phabricator PHP API (Conduit client)

This is a PHP based client for Phabricator API. [Phabricator](http://phabricator.org) is an open source, software engineering platform, built in PHP, and it has a very nice API calld **Conduit**.

For all available endpoint and method name, see the [Conduit Application](https://secure.phabricator.com/conduit/query/modern/) in the live Phabricator instance.

**Basic useful feature list:**

 * Fully implemented all current API endpoint
 * Ability to make custom clients (Currently a simple CURL based client is implemented)
 * Custom processor classes for various endpoints


## Examples

#### This is a simple initialization

```php
//Initialization, this will initialize the connection with conduit.connect method
$client = new \Phabricator\Client\CurlClient();
$phabricator = new \Phabricator\Phabricator($client, "http://phabricator.example.com", "MyUserName", "myAuthToken");

//Uses magic methods and reflection to do this, simple call project.query method with parameters
$response = $phabricator->Project("query", ["status" => "status-open"]);
```

#### Using custom clients

Simply create a new class wich implements the `\Phabricator\Client\ClientInterface`, implement all methods and you ready to go.

```php
$client = new \MyProject\AwesomClient();
$phabricator = new \Phabricator\Phabricator($client, "http://phabricator.example.com", "MyUserName", "myAuthToken");
```

#### Using a cutom endpoint handler

All handler must implements th `\Phabricator\Endpoint\EndpointInterface`. When its complete, you push the new handler to the `Phabricator` class. All handler needs the client in the constructor, you may get it from the main class.

```php
...

$handler = new \MyProject\MyProjectHandler($phabricator->getClient());
$phabricator->pushEndpointHandler("project", $handler);
```

in this example we replaced the handler for the `project` endpoint.

You now specify `executor` methods in your handler, for all method. If you call `$phabricator->Project("query", [])` method call the queryExecutor method in your `MyProjectHandler` class.

If the specified executor method not found the class call the defaultExecutor for fallback.

## Installing

Just for now, simply clone the repository, but i try to put project to packagist, and made available through Jenkins with all test results.

## Informations and Issues

 * The main issue managmenet on my phabricator instance, not in github. - [Link](http://project.zolli.hu)
 * Builds are available through Jenkins and Packagist (Soon...)


## Licensing

This project licesend under [GNU - General Public License, version 3](http://www.gnu.org/licenses/lgpl.txt)

![GPLv3 Logo](http://www.gnu.org/graphics/gplv3-88x31.png)
