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
 * The name used for each property MUST correspond to a security scheme declared in the Security Schemes under the
 * Components Object. Security Requirement Objects that contain multiple schemes require that all schemes MUST be
 * satisfied for a request to be authorized. This enables support for scenarios where multiple query parameters or HTTP
 * headers are required to convey security information. When a list of Security Requirement Objects is defined on the
 * OpenAPI Object or Operation Object, only one of the Security Requirement Objects in the list needs to be satisfied to
 * authorize the request.
 *
 * Each name MUST correspond to a security scheme which is declared in the Security Schemes under the Components Object.
 * If the security scheme is of type "oauth2" or "openIdConnect", then the value is a list of scope names required for
 * the execution, and the list MAY be empty if authorization does not require a specified scope. For other security
 * scheme types, the array MAY contain a list of role names which are required for the execution, but are not otherwise
 * defined or exchanged in-band.
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
