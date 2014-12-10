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
