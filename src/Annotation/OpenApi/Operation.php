<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Annotation\OpenApi;

/**
 * Import classes
 */
use Sunrise\Http\Router\OpenApi\AbstractAnnotation;

/**
 * @Annotation
 *
 * @Target({"CLASS"})
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#operation-object
 */
final class Operation extends AbstractAnnotation implements OperationInterface
{

    /**
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationid
     */
    public $operationId;

    /**
     * @var array<string>
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationtags
     */
    public $tags;

    /**
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationsummary
     */
    public $summary;

    /**
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationdescription
     */
    public $description;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExternalDocumentationInterface
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationexternaldocs
     */
    public $externalDocs;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterInterface>
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationparameters
     */
    public $parameters;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\RequestBodyInterface
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationrequestbody
     */
    public $requestBody;

    /**
     * @Required
     *
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ResponseInterface>
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationresponses
     */
    public $responses;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationdeprecated
     */
    public $deprecated;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SecurityRequirementInterface>
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-operationsecurity
     */
    public $security;
}
