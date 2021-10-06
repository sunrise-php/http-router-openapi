## OpenAPI (Swagger) Specification Support for Sunrise Router

[![Build Status](https://circleci.com/gh/sunrise-php/http-router-openapi.svg?style=shield)](https://circleci.com/gh/sunrise-php/http-router-openapi)
[![Code Coverage](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/?branch=master)
[![Total Downloads](https://poser.pugx.org/sunrise/http-router-openapi/downloads?format=flat)](https://packagist.org/packages/sunrise/http-router-openapi)
[![Latest Stable Version](https://poser.pugx.org/sunrise/http-router-openapi/v/stable?format=flat)](https://packagist.org/packages/sunrise/http-router-openapi)
[![License](https://poser.pugx.org/sunrise/http-router-openapi/license?format=flat)](https://packagist.org/packages/sunrise/http-router-openapi)

---

## Installation

```bash
composer require 'sunrise/http-router-openapi:^2.0'
```

## QuickStart

```php
use Psr\SimpleCache\CacheInterface;
use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\Router;

$openapi = new OpenApi(new Info('Acme', '1.0.0'));

// PSR-16 simple cache implementation...
/** @var CacheInterface $cache */
$openapi->setCache($cache);

// Passing all routes to the openapi object:
/** @var Router $router */
$openapi->addRoute(...$router->getRoutes());

// Convert the openapi object to JSON document:
$openapi->toJson();
// Convert the openapi object to YAML document:
$openapi->toYaml();
// Convert the openapi object to an array
$openapi->toArray();

// Convert an operation part to JSON schema (an array):
// a request cookies:
$openapi->getRequestCookieJsonSchema();
// a request headers:
$openapi->getRequestHeaderJsonSchema();
// a request query:
$openapi->getRequestQueryJsonSchema();
// a request body:
$openapi->getRequestBodyJsonSchema();
// a response body:
$openapi->getResponseBodyJsonSchema();
```

Look for more examples here: [Some App](https://github.com/sunrise-php/http-router-openapi/tree/be27acedfc1f100f8efdcdd9da9430714890baa3/tests/fixtures/SomeApp)

## PSR-15 Middlewares

#### RequestValidationMiddleware

> Validates a request using a route description.

```php
use Sunrise\Http\Router\OpenApi\Middleware\RequestValidationMiddleware;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\Route;

/** @var OpenApi $openapi */
$middleware = new RequestValidationMiddleware($openapi);

/** @var Route $route */
$route->addMiddleware($middleware);
```

## Symfony Commands

#### GenerateOpenapiDocumentCommand

> Generates OpenAPI document.

```php
use Sunrise\Http\Router\OpenApi\Command\GenerateOpenapiDocumentCommand;
use Sunrise\Http\Router\OpenApi\OpenApi;

/** @var OpenApi $openapi */
$command = new GenerateOpenapiDocumentCommand($openapi);
```

```bash
php bin/app router:generate-openapi-document --help
```

#### GenerateJsonSchemaCommand

> Converts an operation part to [JSON schema](https://json-schema.org).

```php
use Sunrise\Http\Router\OpenApi\Command\GenerateJsonSchemaCommand;
use Sunrise\Http\Router\OpenApi\OpenApi;

/** @var OpenApi $openapi */
$command = new GenerateJsonSchemaCommand($openapi);
```

```bash
php bin/app router:generate-json-schema --help
```
