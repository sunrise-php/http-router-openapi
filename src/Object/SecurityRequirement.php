<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Object;

/**
 * Import classes
 */
use Sunrise\Http\Router\OpenApi\ObjectInterface;

/**
 * OAS Security Requirement Object
 *
 * Lists the required security schemes to execute this operation.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#security-requirement-object
 */
final class SecurityRequirement implements ObjectInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $scopes = [];

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string ...$scopes
     *
     * @return void
     */
    public function setScopes(string ...$scopes) : void
    {
        $this->scopes = $scopes;
    }

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
