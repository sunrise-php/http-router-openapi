<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi;

/**
 * Import classes
 */
use ReflectionClass;
use ReflectionMethod;
use Reflector;

/**
 * RouteInterface
 */
interface RouteInterface
{

    /**
     * Gets the route name (aka ID)
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Gets the route methods
     *
     * @return string[]
     */
    public function getMethods() : array;

    /**
     * Gets the route plain path
     *
     * @return string
     */
    public function getPlainPath() : string;

    /**
     * Gets the route path attributes
     *
     * ```php
     * [
     *   'name' => 'foo',
     *   'pattern' => '\w+',
     *   'isOptional' => false,
     * ]
     * ```
     *
     * @return array[]
     */
    public function getPathAttributes() : array;

    /**
     * Gets the route summary
     *
     * @return string
     */
    public function getSummary() : string;

    /**
     * Gets the route description
     *
     * @return string
     */
    public function getDescription() : string;

    /**
     * Gets the route tags
     *
     * @return string[]
     */
    public function getTags() : array;

    /**
     * Gets the route holder
     *
     * @return ReflectionClass|ReflectionMethod|null
     */
    public function getHolder() : ?Reflector;
}
