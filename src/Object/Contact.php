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
 * OAS Contact Object
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#contact-object
 */
class Contact extends AbstractObject
{

    /**
     * The identifying name of the contact person/organization
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-contactname
     */
    protected $name;

    /**
     * The URL pointing to the contact information. MUST be in the format of a URL
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-contacturl
     */
    protected $url;

    /**
     * The email address of the contact person/organization. MUST be in the format of an email address
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-contactemail
     */
    protected $email;

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

    /**
     * @param string $email
     *
     * @return void
     */
    public function setEmail(string $email) : void
    {
        $this->email = $email;
    }
}
