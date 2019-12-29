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
 * OAS OAuth Flows Object
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#oauth-flows-object
 */
class OAuthFlows extends AbstractObject
{

    /**
     * Configuration for the OAuth Implicit flow
     *
     * @var OAuthFlow
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowsimplicit
     */
    protected $implicit;

    /**
     * Configuration for the OAuth Resource Owner Password flow
     *
     * @var OAuthFlow
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowspassword
     */
    protected $password;

    /**
     * Configuration for the OAuth Client Credentials flow
     *
     * Previously called `application` in OpenAPI 2.0.
     *
     * @var OAuthFlow
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowsclientcredentials
     */
    protected $clientCredentials;

    /**
     * Configuration for the OAuth Authorization Code flow
     *
     * Previously called `accessCode` in OpenAPI 2.0.
     *
     * @var OAuthFlow
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oauthflowsauthorizationcode
     */
    protected $authorizationCode;

    /**
     * @param OAuthFlow $implicit
     *
     * @return void
     */
    public function setImplicit(OAuthFlow $implicit) : void
    {
        $this->implicit = $implicit;
    }

    /**
     * @param OAuthFlow $password
     *
     * @return void
     */
    public function setPassword(OAuthFlow $password) : void
    {
        $this->password = $password;
    }

    /**
     * @param OAuthFlow $clientCredentials
     *
     * @return void
     */
    public function setClientCredentials(OAuthFlow $clientCredentials) : void
    {
        $this->clientCredentials = $clientCredentials;
    }

    /**
     * @param OAuthFlow $authorizationCode
     *
     * @return void
     */
    public function setAuthorizationCode(OAuthFlow $authorizationCode) : void
    {
        $this->authorizationCode = $authorizationCode;
    }
}
