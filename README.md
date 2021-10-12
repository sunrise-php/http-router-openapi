## OpenAPI (Swagger) Specification Support for Sunrise Router

[![Build Status](https://circleci.com/gh/sunrise-php/http-router-openapi.svg?style=shield)](https://circleci.com/gh/sunrise-php/http-router-openapi)
[![Code Coverage](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sunrise-php/http-router-openapi/?branch=master)
[![Total Downloads](https://poser.pugx.org/sunrise/http-router-openapi/downloads?format=flat)](https://packagist.org/packages/sunrise/http-router-openapi)
[![Latest Stable Version](https://poser.pugx.org/sunrise/http-router-openapi/v/stable?format=flat)](https://packagist.org/packages/sunrise/http-router-openapi)
[![License](https://poser.pugx.org/sunrise/http-router-openapi/license?format=flat)](https://packagist.org/packages/sunrise/http-router-openapi)

---

## Important to understanding

* [OpenAPI Specification](https://github.com/OAI/OpenAPI-Specification/blob/6ba1577240b79c9f613c2ea8d745c6ef6c832e50/versions/3.0.2.md)

## Installation

```bash
composer require 'sunrise/http-router-openapi:^2.1'
```

## QuickStart

```php
use Psr\SimpleCache\CacheInterface;
use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\OpenApi\RouteInterface;

$openapi = new OpenApi(new Info('Acme', '1.0.0'));

// Passing PSR-16 cache to the openapi object:
/** @var CacheInterface $cache */
$openapi->setCache($cache);

// Passing all routes to the openapi object:
/** @var RouteInterface[] $routes */
$openapi->addRoute(...$routes);

// When using Sunrise Router:
/** @var \Sunrise\Http\Router\Router $router */
$openapi->addRoute(...$router->getRoutes());
```

#### Building OpenAPI Document

```php
// Converting the openapi object to JSON document:
$openapi->toJson();
// Converting the openapi object to YAML document:
$openapi->toYaml();
// Converting the openapi object to an array:
$openapi->toArray();
```

#### Building JSON Schemas

> Converts an operation part to [JSON Schema](https://json-schema.org).

```php
$openapi->getRequestCookieJsonSchema();
$openapi->getRequestHeaderJsonSchema();
$openapi->getRequestQueryJsonSchema();
$openapi->getRequestBodyJsonSchema();
$openapi->getResponseBodyJsonSchema();
```

## PSR-15 Middlewares

#### RequestValidationMiddleware

> Validates a request using a route description.

```php
use Sunrise\Http\Router\OpenApi\Middleware\RequestValidationMiddleware;
use Sunrise\Http\Router\OpenApi\OpenApi;

/** @var OpenApi $openapi */
$middleware = new RequestValidationMiddleware($openapi);
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

> Generates an operation part to [JSON Schema](https://json-schema.org).

```php
use Sunrise\Http\Router\OpenApi\Command\GenerateJsonSchemaCommand;
use Sunrise\Http\Router\OpenApi\OpenApi;

/** @var OpenApi $openapi */
$command = new GenerateJsonSchemaCommand($openapi);
```

```bash
php bin/app router:generate-json-schema --help
```

## Test Kit

#### assertResponseBodyMatchesDescription

> The assertion fails if the given response body doesn't match a description of the operation identified by the given ID.

```php
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Router\OpenApi\Test\OpenapiTestKit;

class SomeTest extends TestCase
{
    use OpenapiTestKit;

    public function testResponseBodyMatchesDescription() : void
    {
        // some logic to run a route...

        /** @var ResponseInterface $response */
        $this->assertResponseBodyMatchesDescription('route.name', $response);
    }
}
```

## Simple Route Description

```php
class SomeController
{

    /**
     * @OpenApi\Operation(
     *   requestBody=@OpenApi\RequestBody(
     *     content={
     *       "application/json": @OpenApi\MediaType(
     *         schema=@OpenApi\Schema(
     *           type="object",
     *           properties={
     *             "foo": @OpenApi\Schema(
     *               type="string",
     *             ),
     *           },
     *         ),
     *       ),
     *     },
     *   ),
     *   responses={
     *     200: @OpenApi\Response(
     *       description="Ok",
     *     ),
     *   },
     * )
     */
    public function someAction()
    {
    }
}
```

---

Look for more examples here: [Some App](https://github.com/sunrise-php/http-router-openapi/tree/be27acedfc1f100f8efdcdd9da9430714890baa3/tests/fixtures/SomeApp)
