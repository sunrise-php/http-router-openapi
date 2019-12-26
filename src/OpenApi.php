<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2018, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router
 */

namespace Sunrise\Http\Router\OpenApi;

/**
 * Import classes
 */
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Operation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Parameter;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Schema;
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
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oasversion
     */
    protected $openapi = '3.0.2';

    /**
     * @var Info
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oasinfo
     */
    protected $info;

    /**
     * @var Server[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oasservers
     */
    protected $servers;

    /**
     * @var array
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oaspaths
     */
    protected $paths;

    /**
     * @var array
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oascomponents
     */
    protected $components;

    /**
     * @var SecurityRequirement[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oassecurity
     */
    protected $security;

    /**
     * @var Tag[]
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oastags
     */
    protected $tags;

    /**
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
     * @param Info $info
     */
    public function __construct(Info $info)
    {
        $this->info = $info;

        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace('Sunrise\Http\Router\Annotation');
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
            $operation = $this->createOperation($route);

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
     * @param RouteInterface $route
     *
     * @return Operation
     */
    private function createOperation(RouteInterface $route) : Operation
    {
        $source = new ReflectionClass($route->getRequestHandler());

        $operation = $this->annotationReader->getClassAnnotation($source, Operation::class) ?? new Operation();

        $operation->operationId = $operation->operationId ?? $route->getName();

        $this->addComponentObject(...$operation->getReferencedObjects($this->annotationReader));

        $attributes = path_parse($route->getPath());

        foreach ($attributes as $attribute) {
            $parameter = new Parameter();
            $parameter->in = 'path';
            $parameter->name = $attribute['name'];
            $parameter->required = !$attribute['isOptional'];

            if (isset($attribute['pattern'])) {
                $parameter->schema = new Schema();
                $parameter->schema->type = 'string';
                $parameter->schema->pattern = $attribute['pattern'];
            }

            $operation->parameters[] = $parameter;
        }

        return $operation;
    }
}
