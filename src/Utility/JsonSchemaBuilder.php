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
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Operation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\RequestBodyReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ResponseReference;
use Sunrise\Http\Router\OpenApi\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\OpenApi;
use ReflectionClass;

/**
 * Import functions
 */
use function array_keys;
use function array_walk_recursive;
use function str_replace;

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
     * @var SimpleAnnotationReader
     */
    private $annotationReader;

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
