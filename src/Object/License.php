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
 * OAS License Object
 *
 * License information for the exposed API.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#license-object
 */
final class License extends AbstractObject
{

    /**
     * The license name used for the API
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-licensename
     */
    protected $name;

    /**
     * An SPDX license expression for the API
     *
     * The identifier field is mutually exclusive of the url field.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#user-content-licenseidentifier
     * @link https://spdx.dev/spdx-specification-21-web-version/#h.jxpfx0ykyb60
     */
    protected $identifier;

    /**
     * A URL to the license used for the API
     *
     * This MUST be in the form of a URL. The url field is mutually exclusive of the identifier field.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-licenseurl
     */
    protected $url;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $identifier
     *
     * @return void
     */
    public function setIdentifier(string $identifier) : void
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }
}
