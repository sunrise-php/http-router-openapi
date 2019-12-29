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
 * OAS External Documentation Object
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#external-documentation-object
 */
class ExternalDocumentation extends AbstractObject
{

    /**
     * The URL for the target documentation. Value MUST be in the format of a URL
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-externaldocurl
     */
    protected $url;

    /**
     * A short description of the target documentation. CommonMark syntax MAY be used for rich text representation
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-externaldocdescription
     */
    protected $description;

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
}
