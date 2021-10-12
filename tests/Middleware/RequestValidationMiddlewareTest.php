<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Middleware;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Sunrise\Http\Factory\ServerRequestFactory;
use Sunrise\Http\Router\Exception\BadRequestException;
use Sunrise\Http\Router\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\Middleware\RequestValidationMiddleware;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\OpenapiAwareTrait;
use Sunrise\Http\Router\OpenApi\Openapi;
use Sunrise\Http\Router\Route;
use RuntimeException;

/**
 * RequestValidationMiddlewareTest
 */
class RequestValidationMiddlewareTest extends TestCase
{
    use OpenapiAwareTrait;

    /**
     * @return void
     */
    public function testContract() : void
    {
        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * @return void
     */
    public function testRunWithInvalidBody() : void
    {
        $route = $this->getRouter()->getRoute('users.create');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withHeader('Content-Type', 'application/json; charset=UTF-8')
            ->withHeader('X-Key', 'C6DBD0C3-40FF-4BD6-A0BF-247DB27B3314')
            ->withParsedBody(['email' => '@', 'password' => '****'])
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request body is not valid for this resource.');

        $middleware->process($request, $route);
    }

    /**
     * @return void
     */
    public function testRunWithUnsupportedContentType() : void
    {
        $route = $this->getRouter()->getRoute('users.create');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withHeader('Content-Type', 'application/unsupported')
            ->withHeader('X-Key', 'EBD28E0E-BD1F-4978-9CCE-BF9C89CC508F')
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $this->expectException(UnsupportedMediaTypeException::class);
        $this->expectExceptionMessage('Unsupported Media Type');

        $middleware->process($request, $route);
    }

    /**
     * @return void
     */
    public function testRunWithInvalidCookie() : void
    {
        $route = $this->getRouter()->getRoute('users.list');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withCookieParams(['limit' => 'foo'])
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request cookie is not valid for this resource.');

        $middleware->process($request, $route);
    }

    /**
     * @return void
     */
    public function testRunWithInvalidHeader() : void
    {
        $route = $this->getRouter()->getRoute('users.list');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withHeader('X-Limit', 'foo')
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request header is not valid for this resource.');

        $middleware->process($request, $route);
    }

    /**
     * @return void
     */
    public function testRunWithInvalidQuery() : void
    {
        $route = $this->getRouter()->getRoute('users.list');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withQueryParams(['limit' => 'foo'])
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('The request query is not valid for this resource.');

        $middleware->process($request, $route);
    }

    /**
     * @return void
     */
    public function testRunWithValidBody() : void
    {
        $route = $this->getRouter()->getRoute('users.create');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withHeader('Content-Type', 'application/json; charset=UTF-8')
            ->withHeader('X-Key', '3DDCB0E9-54CC-4DBE-9241-43CD9A2B98FE')
            ->withParsedBody(['email' => 'foo@acme.com', 'password' => 'P@$$w0rd'])
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $response = $middleware->process($request, $route);

        $this->assertSame(201, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRunWithValidCookie() : void
    {
        $route = $this->getRouter()->getRoute('users.list');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withCookieParams(['limit' => '100'])
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $response = $middleware->process($request, $route);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRunWithValidHeader() : void
    {
        $route = $this->getRouter()->getRoute('users.list');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withHeader('X-Limit', '100')
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $response = $middleware->process($request, $route);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRunWithValidQuery() : void
    {
        $route = $this->getRouter()->getRoute('users.list');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withQueryParams(['limit' => '100'])
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $response = $middleware->process($request, $route);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRunWithoutRoute() : void
    {
        $route = $this->getRouter()->getRoute('users.create');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/');

        $middleware = new RequestValidationMiddleware($this->getOpenapi());

        $response = $middleware->process($request, $route);

        $this->assertSame(201, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRunWithoutOpenapi() : void
    {
        $route = $this->getRouter()->getRoute('users.create');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $middleware = new RequestValidationMiddleware();

        $this->expectException(RuntimeException::class);

        $middleware->process($request, $route);
    }

    /**
     * @return void
     */
    public function testRunInheritedMiddleware() : void
    {
        // @codingStandardsIgnoreStart
        $middleware = new class($this->getOpenapi()) extends RequestValidationMiddleware {
            private $_openapi;

            public function __construct(Openapi $openapi) {
                $this->_openapi = $openapi;
            }

            protected function getOpenapi() : OpenApi {
                return $this->_openapi;
            }
        };
        // @codingStandardsIgnoreEnd

        $route = $this->getRouter()->getRoute('users.list');

        $request = (new ServerRequestFactory)
            ->createServerRequest('GET', '/')
            ->withCookieParams(['limit' => '100'])
            ->withAttribute(Route::ATTR_NAME_FOR_ROUTE, $route);

        $response = $middleware->process($request, $route);

        $this->assertSame(200, $response->getStatusCode());
    }
}
