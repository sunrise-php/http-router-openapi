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
 * OAS Info Object
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#info-object
 */
class Info extends AbstractObject
{

    /**
     * The title of the application
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-infotitle
     */
    protected $title;

    /**
     * A short description of the application
     *
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-infodescription
     *
     * @link https://spec.commonmark.org/
     */
    protected $description;

    /**
     * A URL to the Terms of Service for the API. MUST be in the format of a URL
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-infotermsofservice
     */
    protected $termsOfService;

    /**
     * The contact information for the exposed API
     *
     * @var Contact
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-infocontact
     */
    protected $contact;

    /**
     * The license information for the exposed API
     *
     * @var License
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-infolicense
     */
    protected $license;

    /**
     * The version of the OpenAPI document
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-infoversion
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#oasVersion
     */
    protected $version;

    /**
     * @param string $title
     * @param string $version
     */
    public function __construct(string $title, string $version)
    {
        $this->title = $title;
        $this->version = $version;
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
     * @param string $termsOfService
     *
     * @return void
     */
    public function setTermsOfService(string $termsOfService) : void
    {
        $this->termsOfService = $termsOfService;
    }

    /**
     * @param Contact $contact
     *
     * @return void
     */
    public function setContact(Contact $contact) : void
    {
        $this->contact = $contact;
    }

    /**
     * @param License $license
     *
     * @return void
     */
    public function setLicense(License $license) : void
    {
        $this->license = $license;
    }
}
