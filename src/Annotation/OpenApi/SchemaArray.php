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
final class SchemaArray extends Schema
{

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface
     */
    public $items;

    /**
     * @var string
     */
    public $type = 'array';
}
