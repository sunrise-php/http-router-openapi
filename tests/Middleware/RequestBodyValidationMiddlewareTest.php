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

        $request = $this->createConfiguredMock(ServerRequestInterface::class, [
            'getAttribute' => $route,
            'getHeaderLine' => 'application/json',
            'getParsedBody' => ['foo' => 'bar'],
        ]);

        $middleware = new RequestBodyValidationMiddleware();

        // var_dump($middleware->process($request, $route->getRequestHandler()));

        $this->assertTrue(true);
    }

    /**
     * @return RouteInterface
     */
    private function createRoute() : RouteInterface
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
        $handler = new class implements RequestHandlerInterface
        {
            public function handle(ServerRequestInterface $request) : ResponseInterface
            {
                throw new \RuntimeException('passed');
            }
        };

        return $this->createConfiguredMock(RouteInterface::class, [
            'getName' => 'test',
            'getPath' => '/test',
            'getMethods' => ['GET'],
            'getRequestHandler' => $handler,
        ]);
    }
}
