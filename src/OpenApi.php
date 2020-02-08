<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi;

/**
 * Import classes
 */
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Operation as OperationAnnotation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Parameter as ParameterAnnotation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Schema as SchemaAnnotation;
use Sunrise\Http\Router\OpenApi\Object\ExternalDocumentation;
use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\Object\SecurityRequirement;
use Sunrise\Http\Router\OpenApi\Object\Server;
use Sunrise\Http\Router\OpenApi\Object\Tag;
use Sunrise\Http\Router\RouteInterface;
use ReflectionClass;

/**
 * Import functions
 */
use function Sunrise\Http\Router\path_parse;
use function Sunrise\Http\Router\path_plain;
use function strtolower;

/**
 * OAS OpenAPI Object
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#openapi-object
 */
class OpenApi extends AbstractObject
{

    /**
     * @var string
     */
    public const ANNOTATIONS_NAMESPACE = 'Sunrise\Http\Router\OpenApi\Annotation';

    /**
     * The OpenAPI Specification version that the OpenAPI document uses
     *
     * The openapi field SHOULD be used by tooling specifications and clients to interpret the OpenAPI document.
     *
     * This is not related to the API info.version string.
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oasversion
     *
     * @link https://semver.org/spec/v2.0.0.html
     */
    protected $openapi = '3.0.2';

    /**
     * Provides metadata about the API
     *
     * The metadata MAY be used by tooling as required.
     *
     * @var Info
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oasinfo
     */
    protected $info;

    /**
     * An array of Server Objects, which provide connectivity information to a target server
     *
     * If the servers property is not provided, or is an empty array,
     * the default value would be a Server Object with a url value of /.
     *
     * @var Server[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oasservers
     */
    protected $servers;

    /**
     * The available paths and operations for the API
     *
     * @var array
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oaspaths
     */
    protected $paths;

    /**
     * An element to hold various schemas for the specification
     *
     * @var array
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oascomponents
     */
    protected $components;

    /**
     * A declaration of which security mechanisms can be used across the API
     *
     * The list of values includes alternative security requirement objects that can be used.
     *
     * Only one of the security requirement objects need to be satisfied to authorize a request.
     *
     * Individual operations can override this definition.
     *
     * @var SecurityRequirement[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oassecurity
     */
    protected $security;

    /**
     * A list of tags used by the specification with additional metadata
     *
     * The order of the tags can be used to reflect on their order by the parsing tools.
     *
     * Not all tags that are used by the Operation Object must be declared.
     *
     * The tags that are not declared MAY be organized randomly or based on the tools' logic.
     *
     * Each tag name in the list MUST be unique.
     *
     * @var Tag[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oastags
     */
    protected $tags;

    /**
     * Additional external documentation
     *
     * @var ExternalDocumentation
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oasexternaldocs
     */
    protected $externalDocs;

    /**
     * @var SimpleAnnotationReader
     */
    private $annotationReader;

    /**
     * @var bool
     */
    private $includeUndescribedOperations = true;

    /**
     * @param Info $info
     */
    public function __construct(Info $info)
    {
        $this->info = $info;

        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace(self::ANNOTATIONS_NAMESPACE);
    }

    /**
     * @param bool $value
     *
     * @return void
     */
    public function includeUndescribedOperations(bool $value) : void
    {
        $this->includeUndescribedOperations = $value;
    }

    /**
     * @param Server ...$servers
     *
     * @return void
     */
    public function addServer(Server ...$servers) : void
    {
        foreach ($servers as $server) {
            $this->servers[] = $server;
        }
    }

    /**
     * @param RouteInterface ...$routes
     *
     * @return void
     */
    public function addRoute(RouteInterface ...$routes) : void
    {
        foreach ($routes as $route) {
            $path = path_plain($route->getPath());
            $operation = $this->fetchOperation($route);

            if (null === $operation) {
                continue;
            }

            $this->addComponentObject(...$operation->getReferencedObjects($this->annotationReader));

            foreach ($route->getMethods() as $method) {
                /** @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#fixed-fields-7 */
                $method = strtolower($method);

                $this->paths[$path][$method] = $operation;
            }
        }
    }

    /**
     * @param ComponentObjectInterface ...$objects
     *
     * @return void
     */
    public function addComponentObject(ComponentObjectInterface ...$objects) : void
    {
        foreach ($objects as $object) {
            $this->components[$object->getComponentName()][$object->getReferenceName()] = $object;
        }
    }

    /**
     * @param SecurityRequirement ...$requirements
     *
     * @return void
     */
    public function addSecurityRequirement(SecurityRequirement ...$requirements) : void
    {
        foreach ($requirements as $requirement) {
            $this->security[] = $requirement;
        }
    }

    /**
     * @param Tag ...$tags
     *
     * @return void
     */
    public function addTag(Tag ...$tags) : void
    {
        foreach ($tags as $tag) {
            $this->tags[] = $tag;
        }
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

    /**
     * Fetches OAS Operation Object from the given route
     *
     * This method always returns an instance of the Operation Object,
     * even if the given route doesn't contain it.
     *
     * If you do not want to include undescribed operations,
     * use the `$openapi->includeUndescribedOperations(false)` method.
     *
     * @param RouteInterface $route
     *
     * @return null|OperationAnnotation
     */
    private function fetchOperation(RouteInterface $route) : ?OperationAnnotation
    {
        $operation = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($route->getRequestHandler()),
            OperationAnnotation::class
        );

        if (null === $operation) {
            if (false === $this->includeUndescribedOperations) {
                return null;
            }

            $operation = new OperationAnnotation();
        }

        if (null === $operation->operationId) {
            $operation->operationId = $route->getName();
        }

        $attributes = path_parse($route->getPath());

        foreach ($attributes as $attribute) {
            $parameter = new ParameterAnnotation();
            $parameter->in = 'path';
            $parameter->name = $attribute['name'];
            $parameter->required = !$attribute['isOptional'];

            if (isset($attribute['pattern'])) {
                $parameter->schema = new SchemaAnnotation();
                $parameter->schema->type = 'string';
                $parameter->schema->pattern = $attribute['pattern'];
            }

            $operation->parameters[] = $parameter;
        }

        return $operation;
    }
}
