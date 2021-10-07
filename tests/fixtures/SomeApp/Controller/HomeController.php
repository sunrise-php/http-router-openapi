<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Factory\ResponseFactory;

/**
 * @Route("home", path="/", method="GET")
 *
 * @codingStandardsIgnoreStart
 *
 * @OpenApi\Operation(
 *   responses={
 *     200: @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\EmptyResponse"),
 *     "default": @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\ErrorResponse"),
 *   },
 * )
 *
 * @codingStandardsIgnoreEnd
 */
final class HomeController implements RequestHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return (new ResponseFactory)->createResponse(400);
    }
}
