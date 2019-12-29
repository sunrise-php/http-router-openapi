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
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#license-object
 */
class License extends AbstractObject
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
     * A URL to the license used for the API. MUST be in the format of a URL
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
     * @param string $url
     *
     * @return void
     */
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }
}
