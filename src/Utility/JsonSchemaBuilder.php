<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Utility;

/**
 * Import classes
 */
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\Common\Cache\ApcuCache;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Operation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\RequestBodyReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ResponseReference;
use Sunrise\Http\Router\OpenApi\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\OpenApi;
use ReflectionClass;
use RuntimeException;

/**
 * Import functions
 */
use function array_keys;
use function array_walk;
use function array_walk_recursive;
use function extension_loaded;
use function str_replace;
use function strtolower;

/**
 * JsonSchemaBuilder
 */
class JsonSchemaBuilder
{

    /**
     * @var array
     */
    private $jsonSchemaBlank = [
        '$schema' => 'http://json-schema.org/draft-00/schema#',
    ];

    /**
     * @var ReflectionClass
     */
    private $operationSource;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var bool
     */
    private $useCache = false;

    /**
     * @var string
     */
    private const REQUEST_PARAMETER_LOCATION_PATH = 'path';

    /**
     * @var string
     */
    private const REQUEST_PARAMETER_LOCATION_HEADER = 'header';

    /**
     * @var string
     */
    private const REQUEST_PARAMETER_LOCATION_QUERY = 'query';

    /**
     * @var string
     */
    private const REQUEST_PARAMETER_LOCATION_COOKIE = 'cookie';

    /**
     * Constructor of the class
     *
     * @param ReflectionClass $operationSource
     */
    public function __construct(ReflectionClass $operationSource)
    {
        $this->operationSource = $operationSource;

        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace(OpenApi::ANNOTATIONS_NAMESPACE);
    }

    /**
     * @return ReflectionClass
     */
    public function getOperationSource() : ReflectionClass
    {
        return $this->operationSource;
    }

    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader() : AnnotationReader
    {
        return $this->annotationReader;
    }

    /**
     * @return void
     *
     * @throws RuntimeException
     */
    public function useCache() : void
    {
        if ($this->useCache) {
            throw new RuntimeException('Cache already used.');
        }

        if (!extension_loaded('apcu')) {
            throw new RuntimeException('APCu extension required.');
        }

        $this->useCache = true;

        $this->annotationReader = new CachedReader($this->annotationReader, new ApcuCache(__CLASS__), false);
    }

    /**
     * Builds a JSON schema for a request path
     *
     * @return null|array
     */
    public function forRequestPath() : ?array
    {
        return $this->forRequestParams(self::REQUEST_PARAMETER_LOCATION_PATH);
    }

    /**
     * Builds a JSON schema for a request query parameters
     *
     * @return null|array
     */
    public function forRequestQueryParams() : ?array
    {
        return $this->forRequestParams(self::REQUEST_PARAMETER_LOCATION_QUERY);
    }

    /**
     * Builds a JSON schema for a request header
     *
     * @return null|array
     */
    public function forRequestHeader() : ?array
    {
        return $this->forRequestParams(self::REQUEST_PARAMETER_LOCATION_HEADER);
    }

    /**
     * Builds a JSON schema for a request cookie
     *
     * @return null|array
     */
    public function forRequestCookie() : ?array
    {
        return $this->forRequestParams(self::REQUEST_PARAMETER_LOCATION_COOKIE);
    }

    /**
     * Builds a JSON schema for a request body
     *
     * @param string $mediaType
     *
     * @return null|array
     *
     * @throws UnsupportedMediaTypeException
     */
    public function forRequestBody(string $mediaType) : ?array
    {
        $operation = $this->annotationReader->getClassAnnotation($this->operationSource, Operation::class);
        if (empty($operation->requestBody)) {
            return null;
        }

        $requestBody = $operation->requestBody;
        if ($requestBody instanceof RequestBodyReference) {
            $requestBody = $requestBody->getAnnotation($this->annotationReader);
        }

        if (empty($requestBody->content[$mediaType])) {
            throw new UnsupportedMediaTypeException($mediaType, array_keys($requestBody->content));
        }

        if (empty($requestBody->content[$mediaType]->schema)) {
            return null;
        }

        $jsonSchema = $this->jsonSchemaBlank;

        $referencedObjects = $operation->getReferencedObjects($this->annotationReader);
        foreach ($referencedObjects as $referencedObject) {
            if ('schemas' === $referencedObject->getComponentName()) {
                $jsonSchema['definitions'][$referencedObject->getReferenceName()] = $referencedObject->toArray();
            }
        }

        $jsonSchema += $requestBody->content[$mediaType]->schema->toArray();

        return $this->fixReferences($jsonSchema);
    }

    /**
     * Builds a JSON schema for a response body
     *
     * @param mixed $statusCode
     * @param string $mediaType
     *
     * @return null|array
     */
    public function forResponseBody($statusCode, string $mediaType) : ?array
    {
        $operation = $this->annotationReader->getClassAnnotation($this->operationSource, Operation::class);
        if (empty($operation->responses[$statusCode])) {
            return null;
        }

        $response = $operation->responses[$statusCode];
        if ($response instanceof ResponseReference) {
            $response = $response->getAnnotation($this->annotationReader);
        }

        if (empty($response->content[$mediaType]->schema)) {
            return null;
        }

        $jsonSchema = $this->jsonSchemaBlank;

        $referencedObjects = $operation->getReferencedObjects($this->annotationReader);
        foreach ($referencedObjects as $referencedObject) {
            if ('schemas' === $referencedObject->getComponentName()) {
                $jsonSchema['definitions'][$referencedObject->getReferenceName()] = $referencedObject->toArray();
            }
        }

        $jsonSchema += $response->content[$mediaType]->schema->toArray();

        return $this->fixReferences($jsonSchema);
    }

    /**
     * Builds a JSON schema for a request parameters
     *
     * @param string $name Location name of the parameters specified by the "in" field
     *    (possible name are "query", "header", "path" or "cookie")
     *
     * @return null|array
     */
    private function forRequestParams(string $name) : ?array
    {
        $operation = $this->annotationReader->getClassAnnotation($this->operationSource, Operation::class);

        if (empty($operation->parameters)) {
            return null;
        }

        $jsonSchema = $this->jsonSchemaBlank;
        $jsonSchema['type'] = 'object';
        $jsonSchema['required'] = [];
        $jsonSchema['properties'] = [];
        $jsonSchema['definitions'] = [];

        foreach ($operation->parameters as $parameter) {
            if ($parameter instanceof ParameterReference) {
                $parameter = $parameter->getAnnotation($this->annotationReader);
            }

            if (!($name === $parameter->in)) {
                continue;
            }

            if ('header' === $parameter->in) {
                $parameter->name = strtolower($parameter->name);
            }

            if ($parameter->required) {
                $jsonSchema['required'][] = $parameter->name;
            }

            if ($parameter->schema) {
                $jsonSchema['properties'][$parameter->name] = $parameter->schema;
            }
        }

        if (empty($jsonSchema['required']) && empty($jsonSchema['properties'])) {
            return null;
        }

        $referencedObjects = $operation->getReferencedObjects($this->annotationReader);
        foreach ($referencedObjects as $referencedObject) {
            if ('schemas' === $referencedObject->getComponentName()) {
                $jsonSchema['definitions'][$referencedObject->getReferenceName()] = $referencedObject->toArray();
            }
        }

        array_walk($jsonSchema['properties'], function (&$schema) {
            $schema = $schema->toArray();
        });

        return $this->fixReferences($jsonSchema);
    }

    /**
     * @param array $jsonSchema
     *
     * @return array
     */
    private function fixReferences(array $jsonSchema) : array
    {
        array_walk_recursive($jsonSchema, function (&$value, $key) {
            if ('$ref' === $key) {
                $value = str_replace('#/components/schemas/', '#/definitions/', $value);
            }
        });

        return $jsonSchema;
    }
}
