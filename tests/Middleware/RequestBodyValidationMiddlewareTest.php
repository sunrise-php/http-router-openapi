<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Middleware;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Router\Exception\BadRequestException;
use Sunrise\Http\Router\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\Middleware\RequestBodyValidationMiddleware;
use Sunrise\Http\Router\Route;
use Sunrise\Http\Router\RouteInterface;

/**
 * RequestBodyValidationMiddlewareTest
 */
class RequestBodyValidationMiddlewareTest extends TestCase
{

    /**
     * @return void
     */
    public function testContract() : void
    {
        $middleware = new RequestBodyValidationMiddleware();

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * @return void
     */
    public function testProcess() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], [
            'Content-Type' => 'application/json',
        ], [
            'foo' => 'bar',
        ]);

        $middleware = new RequestBodyValidationMiddleware();

        $response = $middleware->process($request, $route->getRequestHandler());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testProcessWithEmptyRequest() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([], [], null);

        $middleware = new RequestBodyValidationMiddleware();

        $response = $middleware->process($request, $route->getRequestHandler());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testProcessWithoutRoute() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            // empty...
        ], [
            'Content-Type' => 'application/json',
        ], [
            'foo' => 'bar',
        ]);

        $middleware = new RequestBodyValidationMiddleware();

        $response = $middleware->process($request, $route->getRequestHandler());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testProcessWithContainedSemicolonHeader() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], [
            'Content-Type' => 'application/json; charset=UTF-8',
        ], [
            'foo' => 'bar',
        ]);

        $middleware = new RequestBodyValidationMiddleware();

        $response = $middleware->process($request, $route->getRequestHandler());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testProcessWithEmptyContentTypeHeader() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], [
            'Content-Type' => '',
        ], [
            'foo' => 'bar',
        ]);

        $middleware = new RequestBodyValidationMiddleware();

        $this->expectException(UnsupportedMediaTypeException::class);
        $this->expectExceptionMessage('Media type "" is not supported for this operation.');

        $middleware->process($request, $route->getRequestHandler());
    }

    /**
     * @return void
     */
    public function testProcessWithoutContentTypeHeader() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], [
            // empty
        ], [
            'foo' => 'bar',
        ]);

        $middleware = new RequestBodyValidationMiddleware();

        $this->expectException(UnsupportedMediaTypeException::class);
        $this->expectExceptionMessage('Media type "" is not supported for this operation.');

        $middleware->process($request, $route->getRequestHandler());
    }

    /**
     * @return void
     */
    public function testProcessWithoutPayload() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], [
            'Content-Type' => 'application/json',
        ], [
            // empty
        ]);

        $middleware = new RequestBodyValidationMiddleware();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request body is not valid for this resource.');

        $middleware->process($request, $route->getRequestHandler());
    }

    /**
     * @param mixed $payload
     *
     * @return void
     *
     * @dataProvider invalidPayloadProvider
     */
    public function testProcessWithInvalidPayload($payload) : void
    {
        $route = $this->createRoute();
        $attributes = [Route::ATTR_NAME_FOR_ROUTE => $route];
        $headers = ['Content-Type' => 'application/json'];

        $request = $this->createServerRequest($attributes, $headers, $payload);
        $middleware = new RequestBodyValidationMiddleware();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request body is not valid for this resource.');

        $middleware->process($request, $route->getRequestHandler());
    }

    /**
     * @return array
     */
    public function invalidPayloadProvider() : array
    {
        return [
            [null],
            [false],
            [''],
            [[]],
            [new \stdClass()],
            [['foo' => null]],
            [['foo' => '']],
            [['foo' => '1']],
            [['foo' => '12']],
            [['foo' => '1234567890']],
        ];
    }

    /**
     * @param array $attrs
     * @param array $headers
     * @param mixed $parsedBody
     *
     * @return ServerRequestInterface
     */
    private function createServerRequest($attrs = [], $headers = [], $parsedBody = null) : ServerRequestInterface
    {
        $mock = $this->createMock(ServerRequestInterface::class);

        $mock->method('getAttribute')->will($this->returnCallback(function ($key) use ($attrs) {
            return $attrs[$key] ?? null;
        }));

        $mock->method('getHeaderLine')->will($this->returnCallback(function ($key) use ($headers) {
            return $headers[$key] ?? '';
        }));

        $mock->method('getParsedBody')->willReturn($parsedBody);

        return $mock;
    }

    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     *
     * @return ResponseInterface
     */
    private function createResponse($statusCode = 200, $reasonPhrase = '') : ResponseInterface
    {
        $mock = $this->createMock(ResponseInterface::class);

        $mock->method('getStatusCode')->willReturn($statusCode);

        $mock->method('getReasonPhrase')->willReturn($reasonPhrase);

        return $mock;
    }

    /**
     * @param null|ResponseInterface $expectedResponse
     *
     * @return RouteInterface
     */
    private function createRoute(ResponseInterface $expectedResponse = null) : RouteInterface
    {
        /**
         * @OpenApi\Operation(
         *   requestBody=@OpenApi\RequestBody(
         *     content={
         *       "application/json"=@OpenApi\MediaType(
         *         schema=@OpenApi\Schema(
         *           type="object",
         *           properties={
         *             "foo"=@OpenApi\Schema(
         *               type="string",
         *               minLength=3,
         *               maxLength=9,
         *             ),
         *           },
         *           required={
         *             "foo",
         *           },
         *         ),
         *       ),
         *     },
         *   ),
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $handler = new class ($expectedResponse ?? $this->createResponse(200)) implements RequestHandlerInterface
        {
            private $response;

            public function __construct($response)
            {
                $this->response = $response;
            }

            public function handle(ServerRequestInterface $request) : ResponseInterface
            {
                return $this->response;
            }
        };

        return $this->createConfiguredMock(RouteInterface::class, [
            'getRequestHandler' => $handler,
        ]);
    }
}
