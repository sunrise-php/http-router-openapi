<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures;

use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\OpenApi;

trait OpenapiAwareTrait
{
    use RouterAwareTrait;

    /**
     * @return OpenApi
     */
    private function getOpenapi() : OpenApi
    {
        $router = $this->getRouter();

        $info = new Info('Some application', '1.0.0');

        $openapi = new OpenApi($info);
        $openapi->addRoute(...$router->getRoutes());

        return $openapi;
    }
}
