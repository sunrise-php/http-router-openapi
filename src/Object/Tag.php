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
 * OAS Tag Object
 *
 * Adds metadata to a single tag that is used by the Operation Object.
 *
 * It is not mandatory to have a Tag Object per tag defined in the Operation Object instances.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#tag-object
 */
final class Tag extends AbstractObject
{

    /**
     * The name of the tag
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-tagname
     */
    protected $name;

    /**
     * A description for the tag
     *
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-tagdescription
     * @link https://spec.commonmark.org/
     */
    protected $description;

    /**
     * Additional external documentation for this tag
     *
     * @var ExternalDocumentation
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-tagexternaldocs
     */
    protected $externalDocs;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
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
     * @param ExternalDocumentation $externalDocs
     *
     * @return void
     */
    public function setExternalDocs(ExternalDocumentation $externalDocs) : void
    {
        $this->externalDocs = $externalDocs;
    }
}
