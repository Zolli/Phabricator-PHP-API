[![Build Status](http://ci.zolli.hu/buildStatus/icon?job=Phabricator%20PHP%20API)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![Build Stability](http://status.buildr-framework.io/buildstatus/status_modules.php?jobName=Phabricator%20PHP%20API/&type=stability)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![Code Coverage](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Zolli/Phabricator-PHP-API/?branch=master)
[![Test Results](http://status.buildr-framework.io/buildstatus/status_modules.php?jobName=Phabricator%20PHP%20API&type=tests)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![CRAP Report](http://status.buildr-framework.io/buildstatus/status_modules.php?jobName=Phabricator%20PHP%20API&type=crap)](http://ci.zolli.hu/job/Phabricator%20PHP%20API/)
[![Dependency Status](https://www.versioneye.com/user/projects/5694edc7af789b0043000c0c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5694edc7af789b0043000c0c)
[![PHP7 Status](https://img.shields.io/badge/PHP7-tested-8892BF.svg)](https://github.com/BuildrPHP/Test-Tools)


# Phabricator PHP API (Conduit client)

This is a PHP based client for Phabricator API. [Phabricator](http://phabricator.org) is an open source, software engineering platform, built in PHP, and it has a very nice API called **Conduit**.
For all available endpoint and method name, see the [Conduit Application](https://secure.phabricator.com/conduit/query/modern/) in the live Phabricator instance.

**Basic useful feature list:**

 * Fully implemented all current API endpoint
 * Ability to make custom Client implementation
 * Custom handler class for each endpoint


## Installation

### With composer

Run this command inside your project

```
composer require zolli/phabricator-php-api
```

Or past this dependency into your composer.json manually

```json
{
  "require": {
        "zolli/phabricator-php-api": "2.0.*"
    }
}
```

## Documentation

#### Initialization

```php
//Initialization the instance
$api = new \Phabricator\Phabricator('http://phabricator.example.com', 'cli-exmapletoken')
```

The API is now ready to use. This class uses magic method to proxy the calls to the suitable endpoint handler.
Phabricator methods should looks like this: `project.query`. In this package exploded into two parts.

The first is the endpoint (`Project` in this example) and the method (`query`);

With this example the call is looks like this:

```php
$result = $api->Project('query', ['status' => 'status-open']);
```

In this example the `/api/project.query` API is called and the `status` argument is passed.

#### Using custom client

This API of this package is allows you to make custom API clients that run the request for you.
All client should implement the `Phabricator\Client\ClientInterface` interface.

Custom clients should be injected in two different way.

Injecting trough the constructor

```php
$myClient = \Vendor\Package\MyAwesomeApiClient();

$api = new \Phabricator\Phabricator('http://phabricator.example.com', 'cli-exmapletoken', $myClient);
```

Or you can use the `Phabricator::setClient(ClientInterface $client)` method.

```php
$myClient = \Vendor\Package\MyAwesomeApiClient();

$api = new \Phabricator\Phabricator('http://phabricator.example.com', 'cli-exmapletoken');
$api->setClient($myClient);
```

#### Custom endpoint handlers

Handlers are various classes that handle the execution and post-processing of endpoint methods.
By default all API endpoint have handler, but only the default that no do any pre- or post-processing.

By example a custom handler can read and write files when using the `file.upload` or `file.download` method.

To achieve this create a class that implements the `\Phabricator\Endpoints\EndpointInterface` and extends the
`\Phabricator\Endpoints\BaseEndpoint` class and you good to go.

The `BaseEndpoint` provides a `defaultExecutor()` method that executed when an endpoint method
not has any specific executor.

When creating custom executor method this methods will be used when calling an endpoint method.

Look the `BaseEndpoint` and any endpoint handler for more information.

Suppose that you created and endpoint handler with this FQCN: `\Vendor\Package\Hander\FileHander`;
You can push this handler like this:

Tha first argument is the endpoint name for this handler is listen and the second is the FQCN of the handler.

```php
    $api = new \Phabricator\Phabricator('http://phabricator.example.com', 'cli-exmapletoken');

    $api->pushEndpointHandler('File', FileHandler::class);
```

## Responses

The client is returning `\Phabricator\Response\ConduitResponse` as response. Look API documentation for
methods.

## Upgrading

### From 1.0.0

In the 2.0.0 release the API is changed significantly and the underlying API dramatically.
So, this release probably not compatible with components that created for 1.0.0

Main API Differences:

 - The `\Phabricator\Phabricator constructor` only take the baseUrl and the tokens as arguemnt
 - Client registration in constructor is now optional
 - Registering custom endpoint handler only require the handler Fully qualified class name, not instance
 - Instead of `\stdClass` responses now `\Phabricator\Response\ConduitResponse` objects.
 - The `\Phabricator\Client\ClientInterface` interface changed significantly.
 - The arguments of endpoint handler methods (executors) changed.
 - Now not using exceptions from global namespace, instead use `buildr/foundation` package exceptions
 - Clients not responsible for request data formatting.

## API Documentation

The 2.0.0 release API documentation is available here: [API Documentation](https://ci.zolli.hu/job/Phabricator%20PHP%20API/4/artifact/build/output/release/Phabricator%20PHP%20API-doc-2.0.0.git-237.zip)

## Contribution

For contribution guide and coding standard please visit our [Coding Standard Repository](https://github.com/BuildrPHP/Coding-Standard)

## Licensing

This project licensed under [GNU - General Public License, version 3](http://www.gnu.org/licenses/lgpl.txt)

![GPLv3 Logo](http://www.gnu.org/graphics/gplv3-88x31.png)
