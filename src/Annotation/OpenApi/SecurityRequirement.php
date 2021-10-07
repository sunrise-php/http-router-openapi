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
 * @Target({"ANNOTATION"})
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#security-requirement-object
 */
final class SecurityRequirement implements SecurityRequirementInterface
{

    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @var array<string>
     */
    public $scopes = [];

    /**
     * {@inheritdoc}
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#security-requirement-object-examples
     */
    public function toArray() : array
    {
        return [$this->name => $this->scopes];
    }
}
