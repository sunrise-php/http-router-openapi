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
 * OAS OAuth Flow Object
 *
 * Configuration details for a supported OAuth Flow.
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#oauth-flow-object
 */
final class OAuthFlow extends AbstractObject
{

    /**
     * The authorization URL to be used for this flow
     *
     * This MUST be in the form of a URL.
     *
     * The OAuth2 standard requires the use of TLS.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowauthorizationurl
     */
    protected $authorizationUrl;

    /**
     * The token URL to be used for this flow
     *
     * This MUST be in the form of a URL
     *
     * The OAuth2 standard requires the use of TLS.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowtokenurl
     */
    protected $tokenUrl;

    /**
     * The URL to be used for obtaining refresh tokens
     *
     * This MUST be in the form of a URL.
     *
     * The OAuth2 standard requires the use of TLS.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowrefreshurl
     */
    protected $refreshUrl;

    /**
     * The available scopes for the OAuth2 security scheme
     *
     * A map between the scope name and a short description for it.
     *
     * The map MAY be empty.
     *
     * @var string[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowscopes
     */
    protected $scopes;

    /**
     * @param string $authorizationUrl
     *
     * @return void
     */
    public function setAuthorizationUrl(string $authorizationUrl) : void
    {
        $this->authorizationUrl = $authorizationUrl;
    }

    /**
     * @param string $tokenUrl
     *
     * @return void
     */
    public function setTokenUrl(string $tokenUrl) : void
    {
        $this->tokenUrl = $tokenUrl;
    }

    /**
     * @param string $refreshUrl
     *
     * @return void
     */
    public function setRefreshUrl(string $refreshUrl) : void
    {
        $this->refreshUrl = $refreshUrl;
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return void
     */
    public function addScope(string $name, string $description) : void
    {
        $this->scopes[$name] = $description;
    }
}
