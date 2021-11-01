<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Annotation\OpenApi;

/**
 * @Annotation
 *
 * @Target({"ALL"})
 */
final class SchemaObject extends Schema
{

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface>
     */
    public $properties;

    /**
     * @var string
     */
    public $type = 'object';
}
