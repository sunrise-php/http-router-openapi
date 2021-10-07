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
 * OAS Server Object
 *
 * An object representing a Server.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#server-object
 */
final class Server extends AbstractObject
{

    /**
     * A URL to the target host
     *
     * This URL supports Server Variables and MAY be relative, to indicate that the host location is relative to the
     * location where the OpenAPI document is being served.
     *
     * Variable substitutions will be made when a variable is named in {brackets}.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-serverurl
     */
    protected $url;

    /**
     * An optional string describing the host designated by the URL
     *
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-serverdescription
     * @link https://spec.commonmark.org/
     */
    protected $description;

    /**
     * A map between a variable name and its value
     *
     * The value is used for substitution in the server's URL template.
     *
     * @var ServerVariable[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-servervariables
     */
    protected $variables;

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
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

    /**
     * @param ServerVariable ...$variables
     *
     * @return void
     */
    public function addVariable(ServerVariable ...$variables) : void
    {
        foreach ($variables as $variable) {
            $this->variables[$variable->getName()] = $variable;
        }
    }
}
