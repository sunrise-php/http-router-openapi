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
use Sunrise\Http\Router\OpenApi\Middleware\RequestQueryValidationMiddleware;
use Sunrise\Http\Router\Route;
use Sunrise\Http\Router\RouteInterface;

/**
 * RequestQueryValidationMiddlewareTest
 */
class RequestQueryValidationMiddlewareTest extends TestCase
{

    /**
     * @return void
     */
    public function testContract() : void
    {
        $middleware = new RequestQueryValidationMiddleware();

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
            'foo' => '1',
            'bar' => 'a',
        ]);

        $middleware = new RequestQueryValidationMiddleware();
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

        $middleware = new RequestQueryValidationMiddleware();
        $response = $middleware->process($request, $route->getRequestHandler());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testProcessWithInvalidPayload() : void
    {
        $route = $this->createRoute();

        $request = $this->createServerRequest([
            Route::ATTR_NAME_FOR_ROUTE => $route,
        ], [
            'foo' => 'a',
            'bar' => '1',
        ]);

        $middleware = new RequestQueryValidationMiddleware();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request query parameters is not valid for this resource.');

        $middleware->process($request, $route->getRequestHandler());
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
         *       in="query",
         *       name="foo",
         *       required=true,
         *       schema=@OpenApi\Schema(
         *         type="string",
         *         pattern="^\d+$",
         *       ),
         *     ),
         *     @OpenApi\Parameter(
         *       in="query",
         *       name="bar",
         *       required=true,
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
     * @param array $queryParams
     *
     * @return ServerRequestInterface
     */
    private function createServerRequest($attributes = [], $queryParams = []) : ServerRequestInterface
    {
        $mock = $this->createMock(ServerRequestInterface::class);

        $mock->method('getAttribute')->will($this->returnCallback(function ($key) use ($attributes) {
            return $attributes[$key] ?? null;
        }));

        $mock->method('getQueryParams')->willReturn($queryParams);

        return $mock;
    }
}
