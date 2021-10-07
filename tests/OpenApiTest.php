<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Operation;
use Sunrise\Http\Router\OpenApi\ComponentInterface;
use Sunrise\Http\Router\OpenApi\Exception\InvalidReferenceException;
use Sunrise\Http\Router\OpenApi\Object\ExternalDocumentation;
use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\Object\SecurityRequirement;
use Sunrise\Http\Router\OpenApi\Object\Server;
use Sunrise\Http\Router\OpenApi\Object\Tag;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\OpenapiAwareTrait;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Controller\InvalidController;
use Sunrise\Http\Router\RequestHandler\CallableRequestHandler;
use Sunrise\Http\Router\Route;

/**
 * Import functions
 */
use function json_encode;

/**
 * Import constants
 */
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * OpenApiTest
 */
class OpenApiTest extends TestCase
{
    use OpenapiAwareTrait;

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $object = new OpenApi(new Info('foo', 'bar'));

        $this->assertSame([
            'openapi' => '3.0.2',
            'info' => [
                'title' => 'foo',
                'version' => 'bar',
            ],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testAddServer() : void
    {
        $object = new OpenApi(new Info('foo', 'bar'));

        $object->addServer(
            new Server('baz'),
            new Server('qux')
        );

        $this->assertSame([
            'openapi' => '3.0.2',
            'info' => [
                'title' => 'foo',
                'version' => 'bar',
            ],
            'servers' => [
                [
                    'url' => 'baz',
                ],
                [
                    'url' => 'qux',
                ],
            ],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testAddComponent() : void
    {
        $object = new OpenApi(new Info('foo', 'bar'));

        $com1 = $this->createMock(ComponentInterface::class);
        $com1->method('getComponentName')->willReturn('foo');
        $com1->method('getReferenceName')->willReturn('bar');
        $com1->method('toArray')->willReturn(['baz']);

        $com2 = $this->createMock(ComponentInterface::class);
        $com2->method('getComponentName')->willReturn('qux');
        $com2->method('getReferenceName')->willReturn('quux');
        $com2->method('toArray')->willReturn(['quuux']);

        $object->addComponent($com1, $com2);

        $this->assertSame([
            'openapi' => '3.0.2',
            'info' => [
                'title' => 'foo',
                'version' => 'bar',
            ],
            'components' => [
                'foo' => [
                    'bar' => [
                        'baz',
                    ],
                ],
                'qux' => [
                    'quux' => [
                        'quuux',
                    ],
                ],
            ],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testAddSecurityRequirement() : void
    {
        $object = new OpenApi(new Info('foo', 'bar'));

        $object->addSecurityRequirement(
            new SecurityRequirement('baz'),
            new SecurityRequirement('qux')
        );

        $this->assertSame([
            'openapi' => '3.0.2',
            'info' => [
                'title' => 'foo',
                'version' => 'bar',
            ],
            'security' => [
                [
                    'baz' => [],
                ],
                [
                    'qux' => [],
                ],
            ],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testAddTag() : void
    {
        $object = new OpenApi(new Info('foo', 'bar'));

        $object->addTag(
            new Tag('baz'),
            new Tag('qux')
        );

        $this->assertSame([
            'openapi' => '3.0.2',
            'info' => [
                'title' => 'foo',
                'version' => 'bar',
            ],
            'tags' => [
                [
                    'name' => 'baz',
                ],
                [
                    'name' => 'qux',
                ],
            ],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetExternalDocs() : void
    {
        $object = new OpenApi(new Info('foo', 'bar'));

        $object->setExternalDocs(new ExternalDocumentation('baz'));

        $this->assertSame([
            'openapi' => '3.0.2',
            'info' => [
                'title' => 'foo',
                'version' => 'bar',
            ],
            'externalDocs' => [
                'url' => 'baz',
            ],
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testGetRequestCookieJsonSchema() : void
    {
        $file = __DIR__ . '/fixtures/SomeApp/json-schemas/users.list.cookie.request.json';
        $jsonSchema = $this->getOpenapi()->getRequestCookieJsonSchema('users.list');
        $jsonSchemaString = json_encode($jsonSchema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $this->assertJsonStringEqualsJsonFile($file, $jsonSchemaString);
        $this->assertNull($this->getOpenapi()->getRequestCookieJsonSchema('home'));
        $this->assertNull($this->getOpenapi()->getRequestCookieJsonSchema('unknown'));
    }

    /**
     * @return void
     */
    public function testGetRequestHeaderJsonSchema() : void
    {
        $file = __DIR__ . '/fixtures/SomeApp/json-schemas/users.auth.header.request.json';
        $jsonSchema = $this->getOpenapi()->getRequestHeaderJsonSchema('users.create');
        $jsonSchemaString = json_encode($jsonSchema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $this->assertJsonStringEqualsJsonFile($file, $jsonSchemaString);
        $this->assertNull($this->getOpenapi()->getRequestHeaderJsonSchema('home'));
        $this->assertNull($this->getOpenapi()->getRequestHeaderJsonSchema('unknown'));
    }

    /**
     * @return void
     */
    public function testGetRequestQueryJsonSchema() : void
    {
        $file = __DIR__ . '/fixtures/SomeApp/json-schemas/users.list.query.request.json';
        $jsonSchema = $this->getOpenapi()->getRequestQueryJsonSchema('users.list');
        $jsonSchemaString = json_encode($jsonSchema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $this->assertJsonStringEqualsJsonFile($file, $jsonSchemaString);
        $this->assertNull($this->getOpenapi()->getRequestQueryJsonSchema('home'));
        $this->assertNull($this->getOpenapi()->getRequestQueryJsonSchema('unknown'));
    }

    /**
     * @return void
     */
    public function testGetRequestBodyJsonSchema() : void
    {
        $file = __DIR__ . '/fixtures/SomeApp/json-schemas/users.create.body.request.json';
        $jsonSchema = $this->getOpenapi()->getRequestBodyJsonSchema('users.create');
        $jsonSchemaString = json_encode($jsonSchema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $this->assertJsonStringEqualsJsonFile($file, $jsonSchemaString);
        $this->assertNull($this->getOpenapi()->getRequestBodyJsonSchema('users.create', 'application/xml'));
        $this->assertNull($this->getOpenapi()->getRequestBodyJsonSchema('unknown'));

        $this->expectException(UnsupportedMediaTypeException::class);
        $this->expectExceptionMessage('Unsupported Media Type');

        $this->getOpenapi()->getRequestBodyJsonSchema('users.create', 'application/graphql');
    }

    /**
     * @return void
     */
    public function testGetResponseBodyJsonSchema() : void
    {
        $file = __DIR__ . '/fixtures/SomeApp/json-schemas/users.read.body.response.json';
        $jsonSchema = $this->getOpenapi()->getResponseBodyJsonSchema('users.read', 200);
        $jsonSchemaString = json_encode($jsonSchema, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $this->assertJsonStringEqualsJsonFile($file, $jsonSchemaString);
        $this->assertNull($this->getOpenapi()->getResponseBodyJsonSchema('users.read', 400));
        $this->assertNull($this->getOpenapi()->getResponseBodyJsonSchema('unknown', 200));
    }

    /**
     * @return void
     */
    public function testJson() : void
    {
        $file = __DIR__ . '/fixtures/SomeApp/openapi-documents/openapi.json';
        $document = $this->getOpenapi()->toJson();

        $this->assertJsonStringEqualsJsonFile($file, $document);
    }

    /**
     * @return void
     */
    public function testYaml() : void
    {
        $file = __DIR__ . '/fixtures/SomeApp/openapi-documents/openapi.yml';
        $document = $this->getOpenapi()->toYaml();

        $this->assertStringEqualsFile($file, $document);
    }

    /**
     * @return void
     */
    public function testBuildCache() : void
    {
        $cache = $this->getCache();

        $openapi = $this->getOpenapi();
        $openapi->setCache($cache);

        $document = $openapi->toArray();

        $this->assertArrayHasKey($openapi->getBuildCacheKey(), $cache->storage);

        $this->assertSame($document, $cache->storage[$openapi->getBuildCacheKey()]);

        $cache->storage[$openapi->getBuildCacheKey()] = ['foo' => 'bar'];

        $this->assertSame($cache->storage[$openapi->getBuildCacheKey()], $openapi->toArray());
    }

    /**
     * @return void
     */
    public function testOperationsCache() : void
    {
        $cache = $this->getCache();

        $openapi = $this->getOpenapi();
        $openapi->setCache($cache);

        // background caching of operations...
        $openapi->toArray();

        $this->assertArrayHasKey($openapi->getOperationsCacheKey(), $cache->storage);

        $this->assertArrayHasKey('home', $cache->storage[$openapi->getOperationsCacheKey()]);

        $testOperation = new Operation();
        $testOperation->operationId = 'home';
        $testOperation->summary = '7AC99FFC-6AB0-4EF1-A75E-49E0B85E7849';

        $cache->storage[$openapi->getBuildCacheKey()] = null;
        $cache->storage[$openapi->getOperationsCacheKey()] = [];
        $cache->storage[$openapi->getOperationsCacheKey()][$testOperation->operationId] = $testOperation;

        $document = $openapi->toArray();

        $this->assertSame($testOperation->summary, $document['paths']['/']['get']['summary'] ?? null);
    }

    /**
     * @return void
     */
    public function testReferenceToUndefinedClass() : void
    {
        $route = new Route(
            '73F7225A-DCB9-453C-824F-D8DB3F0AED86',
            '/73F7225A-DCB9-453C-824F-D8DB3F0AED86',
            ['GET'],
            new CallableRequestHandler([
                new InvalidController(),
                'refersToUndefinedClass',
            ])
        );

        $openapi = $this->getOpenapi();
        $openapi->addRoute($route);

        $this->expectException(InvalidReferenceException::class);

        $openapi->toArray();
    }

    /**
     * @return void
     */
    public function testReferenceToUndefinedClassMethod() : void
    {
        $route = new Route(
            '060CA710-EFB0-487C-9BBE-E15588C2E6E5',
            '/060CA710-EFB0-487C-9BBE-E15588C2E6E5',
            ['GET'],
            new CallableRequestHandler([
                new InvalidController(),
                'refersToUndefinedClassMethod',
            ])
        );

        $openapi = $this->getOpenapi();
        $openapi->addRoute($route);

        $this->expectException(InvalidReferenceException::class);

        $openapi->toArray();
    }

    /**
     * @return void
     */
    public function testReferenceToUndefinedClassProperty() : void
    {
        $route = new Route(
            'C7B45F3F-734C-4B08-978D-92AC1072AF3E',
            '/C7B45F3F-734C-4B08-978D-92AC1072AF3E',
            ['GET'],
            new CallableRequestHandler([
                new InvalidController(),
                'refersToUndefinedClassProperty',
            ])
        );

        $openapi = $this->getOpenapi();
        $openapi->addRoute($route);

        $this->expectException(InvalidReferenceException::class);

        $openapi->toArray();
    }

    /**
     * @return void
     */
    public function testReferenceToClassWithoutTarget() : void
    {
        $route = new Route(
            '59402135-6692-4E29-BE22-4D002E4D5577',
            '/59402135-6692-4E29-BE22-4D002E4D5577',
            ['GET'],
            new CallableRequestHandler([
                new InvalidController(),
                'refersToClassWithoutTarget',
            ])
        );

        $openapi = $this->getOpenapi();
        $openapi->addRoute($route);

        $this->expectException(InvalidReferenceException::class);

        $openapi->toArray();
    }

    /**
     * @return void
     */
    public function testReferenceToClassMethodWithoutTarget() : void
    {
        $route = new Route(
            'F3593B39-8ACA-4A7E-A33B-748E418F84C1',
            '/F3593B39-8ACA-4A7E-A33B-748E418F84C1',
            ['GET'],
            new CallableRequestHandler([
                new InvalidController(),
                'refersToClassMethodWithoutTarget',
            ])
        );

        $openapi = $this->getOpenapi();
        $openapi->addRoute($route);

        $this->expectException(InvalidReferenceException::class);

        $openapi->toArray();
    }

    /**
     * @return void
     */
    public function testReferenceToClassPropertyWithoutTarget() : void
    {
        $route = new Route(
            '9F8570EE-D194-4EC4-A977-19EA2D6B3CEC',
            '/9F8570EE-D194-4EC4-A977-19EA2D6B3CEC',
            ['GET'],
            new CallableRequestHandler([
                new InvalidController(),
                'refersToClassPropertyWithoutTarget',
            ])
        );

        $openapi = $this->getOpenapi();
        $openapi->addRoute($route);

        $this->expectException(InvalidReferenceException::class);

        $openapi->toArray();
    }
}
