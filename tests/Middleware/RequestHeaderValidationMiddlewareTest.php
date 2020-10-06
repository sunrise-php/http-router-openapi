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
use Sunrise\Http\Router\OpenApi\Middleware\RequestHeaderValidationMiddleware;
use Sunrise\Http\Router\Route;
use Sunrise\Http\Router\RouteInterface;

/**
 * RequestHeaderValidationMiddlewareTest
 */
class RequestHeaderValidationMiddlewareTest extends TestCase
{

    /**
     * @return void
     */
    public function testContract() : void
    {
        $middleware = new RequestHeaderValidationMiddleware();

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * @param array $payload
     *
     * @return void
     *
     * @dataProvider validPayloadProvider
     */
    public function testProcessWithValidPayload(array $payload) : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], $payload);

        $middleware = new RequestHeaderValidationMiddleware();
        $response = $middleware->process($request, $route->getRequestHandler());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testProcessWithEmptyRequest() : void
    {
        $route = $this->createRoute();
        $request = $this->createServerRequest();

        $middleware = new RequestHeaderValidationMiddleware();
        $response = $middleware->process($request, $route->getRequestHandler());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @param array $payload
     *
     * @return void
     *
     * @dataProvider invalidPayloadProvider
     */
    public function testProcessWithInvalidPayload(array $payload) : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], $payload);

        $middleware = new RequestHeaderValidationMiddleware();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request header is not valid for this resource.');

        $middleware->process($request, $route->getRequestHandler());
    }

    /**
     * @return array
     */
    public function validPayloadProvider() : array
    {
        return [
            [['x-foo' => ['1'], 'x-bar' => ['a']]],
            [['x-foo' => ['1']]],
        ];
    }

    /**
     * @return array
     */
    public function invalidPayloadProvider() : array
    {
        return [
            [['x-foo' => ['a'], 'x-bar' => ['1']]],
            [['x-foo' => ['a'], 'x-bar' => ['a']]],
            [['x-foo' => ['a']]],
            [['x-bar' => ['a']]],
            [[]],
        ];
    }

    /**
     * @return RouteInterface
     */
    private function createRoute() : RouteInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        /**
         * @OpenApi\Operation(
         *   parameters={
         *     @OpenApi\Parameter(
         *       in="header",
         *       name="X-Foo",
         *       required=true,
         *       schema=@OpenApi\Schema(
         *         type="string",
         *         pattern="^\d+$",
         *       ),
         *     ),
         *     @OpenApi\Parameter(
         *       in="header",
         *       name="X-Bar",
         *       required=false,
         *       schema=@OpenApi\Schema(
         *         type="string",
         *         pattern="^\w+$",
         *       ),
         *     ),
         *   },
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $requestHandler = new class ($response) implements RequestHandlerInterface
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
            'getRequestHandler' => $requestHandler,
        ]);
    }

    /**
     * @param array $attributes
     * @param array $headers
     *
     * @return ServerRequestInterface
     */
    private function createServerRequest($attributes = [], $headers = []) : ServerRequestInterface
    {
        $mock = $this->createMock(ServerRequestInterface::class);

        $mock->method('getAttribute')->will($this->returnCallback(function ($key) use ($attributes) {
            return $attributes[$key] ?? null;
        }));

        $mock->method('getHeaders')->willReturn($headers);

        $mock->method('getHeaderLine')->will($this->returnCallback(function ($key) use ($headers) {
            return isset($headers[$key]) ? implode(', ', $headers[$key]) : '';
        }));

        return $mock;
    }
}
