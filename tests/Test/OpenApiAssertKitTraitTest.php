<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Test;

/**
 * Import classes
 */
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Factory\ResponseFactory;
use Sunrise\Http\Router\OpenApi\Test\OpenApiAssertKitTrait;
use Sunrise\Http\Router\Route;
use RuntimeException;

/**
 * OpenApiAssertKitTraitTest
 */
class OpenApiAssertKitTraitTest extends TestCase
{
    use OpenApiAssertKitTrait;

    /**
     * @return void
     */
    public function testAssertResponseBodyMatchesDescription() : void
    {
        /**
         * @OpenApi\Operation(
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *       content={
         *         "application/json"=@OpenApi\MediaType(
         *           schema=@OpenApi\Schema(
         *             type="object",
         *             properties={
         *               "foo"=@OpenApi\Schema(
         *                 type="string",
         *               ),
         *               "bar"=@OpenApi\Schema(
         *                 type="string",
         *                 nullable=true,
         *               ),
         *             },
         *           ),
         *         ),
         *       },
         *     ),
         *   },
         * )
         */
        $rh1 = new class implements RequestHandlerInterface
        {
            public function handle(ServerRequestInterface $request) : ResponseInterface
            {
                throw new RuntimeException();
            }
        };

        $rh2 = new class implements RequestHandlerInterface
        {
            public function handle(ServerRequestInterface $request) : ResponseInterface
            {
                throw new RuntimeException();
            }
        };

        $route = new Route('foo', '/foo', ['GET'], $rh1);
        $response = (new ResponseFactory)->createResponse(200);
        $response->getBody()->write('');
        try {
            $this->assertResponseBodyMatchesDescription($route, $response);
        } catch (AssertionFailedError $e) {
            $this->assertTrue(true);
            $this->assertSame('Response body MUST be non-empty.', $e->getMessage());
        }

        $route = new Route('foo', '/foo', ['GET'], $rh1);
        $response = (new ResponseFactory)->createResponse(200);
        $response->getBody()->write('!');
        try {
            $this->assertResponseBodyMatchesDescription($route, $response);
        } catch (AssertionFailedError $e) {
            $this->assertTrue(true);
            $this->assertSame('Response body MUST contain valid JSON: Syntax error', $e->getMessage());
        }

        $route = new Route('foo', '/foo', ['GET'], $rh2);
        $response = (new ResponseFactory)->createResponse(200);
        $response->getBody()->write(json_encode([]));
        try {
            $this->assertResponseBodyMatchesDescription($route, $response);
        } catch (AssertionFailedError $e) {
            $this->assertTrue(true);
            $this->assertSame('No JSON schema found.', $e->getMessage());
        }

        $route = new Route('foo', '/foo', ['GET'], $rh1);
        $response = (new ResponseFactory)->createResponse(200);
        $response->getBody()->write(json_encode(['foo', 'foo', 'bar' => 1]));
        try {
            $this->assertResponseBodyMatchesDescription($route, $response);
        } catch (AssertionFailedError $e) {
            $this->assertTrue(true);
            $this->assertSame('Invalid body: [
    {
        "property": "bar",
        "pointer": "/bar",
        "message": "Integer value found, but a string or a null is required",
        "constraint": "type",
        "context": 1
    }
]', $e->getMessage());
        }

        $route = new Route('foo', '/foo', ['GET'], $rh1);
        $response = (new ResponseFactory)->createResponse(200);
        $response->getBody()->write(json_encode(['foo', 'foo', 'bar' => 'bar']));
        $this->assertResponseBodyMatchesDescription($route, $response);

        $route = new Route('foo', '/foo', ['GET'], $rh1);
        $response = (new ResponseFactory)->createResponse(200);
        $response->getBody()->write(json_encode(['foo', 'foo', 'bar' => null]));
        $this->assertResponseBodyMatchesDescription($route, $response);
    }
}
