<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures;

use Sunrise\Http\Router\Loader\DescriptorLoader;
use Sunrise\Http\Router\RequestHandler\CallableRequestHandler;
use Sunrise\Http\Router\Router;
use Sunrise\Http\Router\Route;

trait RouterAwareTrait
{
    use CacheAwareTrait;

    /**
     * @return Router
     */
    private function getRouter() : Router
    {
        $cache = $this->getCache(true);

        $loader = new DescriptorLoader();
        $loader->setCache($cache);
        $loader->attach(SomeApp\Controller\HomeController::class);
        $loader->attach(SomeApp\Controller\UserController::class);

        $router = new Router();
        $router->load($loader);

        $router->addRoute(new Route(
            'A52DE488-E4DC-4110-9A85-FC66834EF6D1',
            '/A52DE488-E4DC-4110-9A85-FC66834EF6D1',
            ['GET'],
            new SomeApp\Controller\SomeController()
        ));

        $router->addRoute(new Route(
            'BE6F8C86-9EE1-4FB4-A549-715D62F185E2',
            '/BE6F8C86-9EE1-4FB4-A549-715D62F185E2',
            ['GET'],
            new CallableRequestHandler([
                new SomeApp\Controller\SomeController(),
                'someAction',
            ])
        ));

        $router->addRoute(new Route(
            'C410FF24-D55B-4736-BDE8-1DA2849B7E8F',
            '/C410FF24-D55B-4736-BDE8-1DA2849B7E8F',
            ['GET'],
            new CallableRequestHandler([
                new SomeApp\Controller\SomeController(),
                'anotherAction',
            ])
        ));

        $router->addRoute(new Route(
            'D20527E7-5220-481B-8C1B-6F240F2A0227',
            '/D20527E7-5220-481B-8C1B-6F240F2A0227',
            ['GET'],
            new CallableRequestHandler(function () {
            })
        ));

        return $router;
    }
}
