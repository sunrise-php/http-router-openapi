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
use Sunrise\Http\Router\OpenApi\AbstractObject;

/**
 * OAS Server Variable Object
 *
 * An object representing a Server Variable for server URL template substitution.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#server-variable-object
 */
final class ServerVariable extends AbstractObject
{

    /**
     * The name of the variable
     *
     * @var string
     */
    private $name;

    /**
     * An enumeration of string values to be used if the substitution options are from a limited set
     *
     * The array MUST NOT be empty.
     *
     * @var string[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-servervariableenum
     */
    protected $enum;

    /**
     * The default value to use for substitution, which SHALL be sent if an alternate value is not supplied
     *
     * Note this behavior is different than the Schema Object's treatment of default values, because in those cases
     * parameter values are optional. If the enum is defined, the value MUST exist in the enum's values.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-servervariabledefault
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#schema-object
     */
    protected $default;

    /**
     * An optional description for the server variable
     *
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-servervariabledescription
     *
     * @link https://spec.commonmark.org/
     */
    protected $description;

    /**
     * @param string $name
     * @param string $default
     */
    public function __construct(string $name, string $default)
    {
        $this->name = $name;
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string ...$enum
     *
     * @return void
     */
    public function setEnum(string ...$enum) : void
    {
        $this->enum = $enum;
    }

    /**
     * @param string $description
     *
     * @return void
     */
    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }
}
