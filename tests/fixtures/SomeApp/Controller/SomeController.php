<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Factory\ResponseFactory;

final class SomeController implements RequestHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return (new ResponseFactory)->createResponse(400);
    }

    /**
     * Some action
     */
    public function someAction(ServerRequestInterface $request) : ResponseInterface
    {
        return (new ResponseFactory)->createResponse(400);
    }

    /**
     * Another action
     */
    public function anotherAction(ServerRequestInterface $request) : ResponseInterface
    {
        return (new ResponseFactory)->createResponse(400);
    }
}
