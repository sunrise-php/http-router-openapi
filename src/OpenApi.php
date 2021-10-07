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
use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Psr\SimpleCache\CacheInterface;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Operation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Parameter;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Schema;
use Sunrise\Http\Router\OpenApi\Object\ExternalDocumentation;
use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\Object\SecurityRequirement;
use Sunrise\Http\Router\OpenApi\Object\Server;
use Sunrise\Http\Router\OpenApi\Object\Tag;
use Sunrise\Http\Router\OpenApi\Utility\OperationConverter;
use Sunrise\Http\Router\RequestHandler\CallableRequestHandler;
use Sunrise\Http\Router\RouteInterface;
use ReflectionClass;
use ReflectionMethod;
use Reflector;
use RuntimeException;

/**
 * Import functions
 */
use function Sunrise\Http\Router\path_parse;
use function Sunrise\Http\Router\path_plain;
use function extension_loaded;
use function hash;
use function is_array;
use function json_encode;
use function strtolower;
use function yaml_emit;

/**
 * Import constants
 */
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const YAML_ANY_BREAK;
use const YAML_ANY_ENCODING;

/**
 * OAS OpenAPI Object
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#openapi-object
 */
final class OpenApi extends AbstractObject
{

    /**
     * The package annotations namespace
     *
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
     * @var array<string, array<string, Operation>>
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-oaspaths
     */
    protected $paths;

    /**
     * An element to hold various schemas for the specification
     *
     * @var array<string, array<string, ComponentInterface>>
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
     * @var CacheInterface|null
     */
    private $cache = null;

    /**
     * @var array<string, RouteInterface>
     */
    private $routes = [];

    /**
     * Constructor of the class
     *
     * @param Info $info
     */
    public function __construct(Info $info)
    {
        $this->info = $info;
    }

    /**
     * @param string $operationId
     *
     * @return array|null
     */
    public function getRequestCookieJsonSchema(string $operationId) : ?array
    {
        $operations = $this->getCachedOperations();
        if (isset($operations[$operationId])) {
            return (new OperationConverter($operations[$operationId]))
                ->toRequestCookieJsonSchema();
        }

        return null;
    }

    /**
     * @param string $operationId
     *
     * @return array|null
     */
    public function getRequestHeaderJsonSchema(string $operationId) : ?array
    {
        $operations = $this->getCachedOperations();
        if (isset($operations[$operationId])) {
            return (new OperationConverter($operations[$operationId]))
                ->toRequestHeaderJsonSchema();
        }

        return null;
    }

    /**
     * @param string $operationId
     *
     * @return array|null
     */
    public function getRequestQueryJsonSchema(string $operationId) : ?array
    {
        $operations = $this->getCachedOperations();
        if (isset($operations[$operationId])) {
            return (new OperationConverter($operations[$operationId]))
                ->toRequestQueryJsonSchema();
        }

        return null;
    }

    /**
     * @param string $operationId
     * @param string $contentType
     *
     * @return array|null
     */
    public function getRequestBodyJsonSchema(string $operationId, ?string $contentType = null) : ?array
    {
        $operations = $this->getCachedOperations();
        if (isset($operations[$operationId])) {
            return (new OperationConverter($operations[$operationId]))
                ->toRequestBodyJsonSchema($contentType ?? 'application/json');
        }

        return null;
    }

    /**
     * @param string $operationId
     * @param mixed $statusCode
     * @param string $contentType
     *
     * @return array|null
     */
    public function getResponseBodyJsonSchema(string $operationId, $statusCode, ?string $contentType = null) : ?array
    {
        $operations = $this->getCachedOperations();
        if (isset($operations[$operationId])) {
            return (new OperationConverter($operations[$operationId]))
                ->toResponseBodyJsonSchema($statusCode, $contentType ?? 'application/json');
        }

        return null;
    }

    /**
     * Adds the given Server Object(s) to the OA object
     *
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
     * Adds the given Component Object(s) to the OA object
     *
     * @param ComponentInterface ...$components
     *
     * @return void
     */
    public function addComponent(ComponentInterface ...$components) : void
    {
        foreach ($components as $component) {
            $this->components[$component->getComponentName()][$component->getReferenceName()] = $component;
        }
    }

    /**
     * Adds the given Security Requirement Object(s) to the OA object
     *
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
     * Adds the given Tag Object(s) to the OA object
     *
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
     * Sets the given External Documentation Object to the OA object
     *
     * @param ExternalDocumentation $externalDocs
     *
     * @return void
     */
    public function setExternalDocs(ExternalDocumentation $externalDocs) : void
    {
        $this->externalDocs = $externalDocs;
    }

    /**
     * @param CacheInterface|null $cache
     *
     * @return void
     */
    public function setCache(?CacheInterface $cache) : void
    {
        $this->cache = $cache;
    }

    /**
     * @param RouteInterface ...$routes
     *
     * @return void
     */
    public function addRoute(RouteInterface ...$routes) : void
    {
        foreach ($routes as $route) {
            $this->routes[$route->getName()] = $route;
        }
    }

    /**
     * Converts the object to JSON string
     *
     * @return string
     */
    public function toJson() : string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Converts the object to YAML string
     *
     * @return string
     *
     * @throws RuntimeException
     *         If the yaml extension isn't installed.
     */
    public function toYaml() : string
    {
        if (!extension_loaded('yaml')) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('The yaml extension is required.');
            // @codeCoverageIgnoreEnd
        }

        return yaml_emit($this->toArray(), YAML_ANY_ENCODING, YAML_ANY_BREAK);
    }

    /**
     * Builds and converts the object to an array using caching mechanism
     *
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        $key = $this->getBuildCacheKey();

        if ($this->cache && $this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $result = $this->build();

        if ($this->cache) {
            $this->cache->set($key, $result);
        }

        return $result;
    }

    /**
     * Gets build cache key
     *
     * @return string
     */
    public function getBuildCacheKey() : string
    {
        return hash('md5', 'router:openapi:build');
    }

    /**
     * Gets operations cache key
     *
     * @return string
     */
    public function getOperationsCacheKey() : string
    {
        return hash('md5', 'router:openapi:operations');
    }

    /**
     * Builds the object and returns the result as an array
     *
     * @return array
     */
    private function build() : array
    {
        $operations = $this->getCachedOperations();
        foreach ($operations as $operation) {
            if (isset($this->routes[$operation->operationId])) {
                $this->addComponent(...$operation->getReferencedObjects());

                $path = path_plain($this->routes[$operation->operationId]->getPath());
                foreach ($this->routes[$operation->operationId]->getMethods() as $method) {
                    // https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#fixed-fields-7
                    $lcmethod = strtolower($method);

                    $this->paths[$path][$lcmethod] = $operation;
                }
            }
        }

        return parent::toArray();
    }

    /**
     * Gets cached operations from routes
     *
     * @return array<string, Operation>
     */
    private function getCachedOperations() : array
    {
        $key = $this->getOperationsCacheKey();

        if ($this->cache && $this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $operations = $this->getOperations();

        if ($this->cache) {
            $this->cache->set($key, $operations);
        }

        return $operations;
    }

    /**
     * Gets operations from routes
     *
     * @return array<string, Operation>
     */
    private function getOperations() : array
    {
        $annotationReader = /** @scrutinizer ignore-deprecated */ new SimpleAnnotationReader();
        $annotationReader->addNamespace(self::ANNOTATIONS_NAMESPACE);

        $operations = [];
        foreach ($this->routes as $route) {
            $operation = $this->getRouteOperation($route, $annotationReader);
            if (isset($operation)) {
                $operations[$operation->operationId] = $operation;
            }
        }

        return $operations;
    }

    /**
     * Gets the given route operation
     *
     * TODO: Can be moved to a new abstract layer,
     *       which would make support for any router...
     *
     * @param RouteInterface $route
     * @param AnnotationReader $annotationReader
     *
     * @return Operation|null
     */
    private function getRouteOperation(RouteInterface $route, AnnotationReader $annotationReader) : ?Operation
    {
        $holder = $this->getRouteHolder($route);
        if (null === $holder) {
            return null;
        }

        $operation = ($holder instanceof ReflectionClass) ?
            $annotationReader->getClassAnnotation($holder, Operation::class) :
            $annotationReader->getMethodAnnotation($holder, Operation::class);

        if (null === $operation) {
            return null;
        }

        // override the operation ID...
        $operation->operationId = $route->getName();

        if (empty($operation->summary) && !empty($summary = $route->getSummary())) {
            $operation->summary = $summary;
        }

        if (empty($operation->description) && !empty($description = $route->getDescription())) {
            $operation->description = $description;
        }

        if (empty($operation->tags) && !empty($tags = $route->getTags())) {
            $operation->tags = $tags;
        }

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

        $operation->setHolder($holder);
        $operation->collectReferencedObjects($annotationReader);

        return $operation;
    }

    /**
     * Gets the given route holder
     *
     * @param RouteInterface $route
     *
     * @return ReflectionClass|ReflectionMethod|null
     */
    private function getRouteHolder(RouteInterface $route) : ?Reflector
    {
        $holder = $route->getHolder();
        if (isset($holder)) {
            return $holder;
        }

        $handler = $route->getRequestHandler();
        if (!($handler instanceof CallableRequestHandler)) {
            return new ReflectionClass($handler);
        }

        $callback = $handler->getCallback();
        if (is_array($callback)) {
            return new ReflectionMethod(...$callback);
        }

        return null;
    }
}
