<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Bridge\Sunrise;

/**
 * Import classes
 */
use Sunrise\Http\Router\OpenApi\RouteInterface as OpenapiRouteInterface;
use Sunrise\Http\Router\RequestHandler\CallableRequestHandler as SunriseCallableRequestHandler;
use Sunrise\Http\Router\RouteInterface as SunriseRouteInterface;
use ReflectionClass;
use ReflectionMethod;
use Reflector;

/**
 * Import functions
 */
use function is_array;
use function Sunrise\Http\Router\path_parse;
use function Sunrise\Http\Router\path_plain;

/**
 * Sunrise Route Proxy
 */
final class SunriseRouteProxy implements OpenapiRouteInterface
{

    /**
     * Proxied Sunrise Route
     *
     * @var SunriseRouteInterface
     */
    private $route;

    /**
     * Constructor of the class
     *
     * @param SunriseRouteInterface $route
     */
    public function __construct(SunriseRouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->route->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods() : array
    {
        return $this->route->getMethods();
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPath() : string
    {
        return path_plain($this->route->getPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getPathAttributes() : array
    {
        return path_parse($this->route->getPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary() : string
    {
        return $this->route->getSummary();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription() : string
    {
        return $this->route->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getTags() : array
    {
        return $this->route->getTags();
    }

    /**
     * {@inheritdoc}
     */
    public function getHolder() : ?Reflector
    {
        $handler = $this->route->getRequestHandler();
        if (!($handler instanceof SunriseCallableRequestHandler)) {
            return new ReflectionClass($handler);
        }

        $callback = $handler->getCallback();
        if (is_array($callback)) {
            return new ReflectionMethod(...$callback);
        }

        return null;
    }
}
