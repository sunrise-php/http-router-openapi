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
 * Import classes
 */
use Sunrise\Http\Router\OpenApi\AbstractAnnotationReference;

/**
 * @Annotation
 *
 * @Target({"ANNOTATION"})
 */
final class LinkReference extends AbstractAnnotationReference implements LinkInterface
{

    /**
     * {@inheritdoc}
     */
    public function getAnnotationName() : string
    {
        return Link::class;
    }
}
