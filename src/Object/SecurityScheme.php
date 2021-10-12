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
use Sunrise\Http\Router\OpenApi\ComponentInterface;

/**
 * OAS Security Scheme Object
 *
 * Defines a security scheme that can be used by the operations.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#security-scheme-object
 * @link https://datatracker.ietf.org/doc/html/rfc6749
 * @link https://datatracker.ietf.org/doc/html/draft-ietf-oauth-discovery-06
 * @link https://datatracker.ietf.org/doc/html/draft-ietf-oauth-security-topics
 */
final class SecurityScheme extends AbstractObject implements ComponentInterface
{

    /**
     * @var string
     */
    private $refName;

    /**
     * The type of the security scheme
     *
     * Valid values are "apiKey", "http", "mutualTLS", "oauth2", "openIdConnect".
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemetype
     */
    protected $type;

    /**
     * A description for security scheme
     *
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemedescription
     * @link https://spec.commonmark.org/
     */
    protected $description;

    /**
     * The name of the header, query or cookie parameter to be used
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemename
     */
    protected $name;

    /**
     * The location of the API key
     *
     * Valid values are "query", "header" or "cookie".
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemein
     */
    protected $in;

    /**
     * The name of the HTTP Authorization scheme to be used in the Authorization header as defined in RFC7235
     *
     * The values used SHOULD be registered in the IANA Authentication Scheme registry.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemescheme
     * @link https://tools.ietf.org/html/rfc7235#section-5.1
     * @link https://www.iana.org/assignments/http-authschemes/http-authschemes.xhtml
     */
    protected $scheme;

    /**
     * A hint to the client to identify how the bearer token is formatted
     *
     * Bearer tokens are usually generated by an authorization server,
     * so this information is primarily for documentation purposes.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemebearerformat
     */
    protected $bearerFormat;

    /**
     * An object containing configuration information for the flow types supported
     *
     * @var OAuthFlows
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemeflows
     */
    protected $flows;

    /**
     * OpenId Connect URL to discover OAuth2 configuration values
     *
     * This MUST be in the form of a URL.
     *
     * The OpenID Connect standard requires the use of TLS.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-securityschemeopenidconnecturl
     */
    protected $openIdConnectUrl;

    /**
     * @param string $refName
     * @param string $type
     */
    public function __construct(string $refName, string $type)
    {
        $this->refName = $refName;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName() : string
    {
        return 'securitySchemes';
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceName() : string
    {
        return $this->refName;
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
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @param string $in
     *
     * @return void
     */
    public function setIn(string $in) : void
    {
        $this->in = $in;
    }

    /**
     * @param string $scheme
     *
     * @return void
     */
    public function setScheme(string $scheme) : void
    {
        $this->scheme = $scheme;
    }

    /**
     * @param string $bearerFormat
     *
     * @return void
     */
    public function setBearerFormat(string $bearerFormat) : void
    {
        $this->bearerFormat = $bearerFormat;
    }

    /**
     * @param OAuthFlows $flows
     *
     * @return void
     */
    public function setFlows(OAuthFlows $flows) : void
    {
        $this->flows = $flows;
    }

    /**
     * @param string $openIdConnectUrl
     *
     * @return void
     */
    public function setOpenIdConnectUrl(string $openIdConnectUrl) : void
    {
        $this->openIdConnectUrl = $openIdConnectUrl;
    }
}
