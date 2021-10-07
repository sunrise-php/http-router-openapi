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
use Sunrise\Http\Router\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Operation;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ParameterReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\RequestBodyReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ResponseReference;
use Sunrise\Http\Router\OpenApi\Annotation\OpenApi\Schema;

/**
 * Import functions
 */
use function array_keys;
use function array_walk_recursive;
use function is_array;
use function strtolower;
use function str_replace;

/**
 * OperationConverter
 */
final class OperationConverter
{

    /**
     * Blank JSON Schema
     *
     * @var array
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schema-object
     */
    private const JSON_SCHEMA_BLANK = [
        '$schema' => 'http://json-schema.org/draft-00/schema#',
    ];

    /**
     * OAS Operation Object
     *
     * @var Operation
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#operationObject
     */
    private $operation;

    /**
     * Constructor of the class
     *
     * @param Operation $operation
     */
    public function __construct(Operation $operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return array|null
     */
    public function toRequestCookieJsonSchema() : ?array
    {
        return $this->toRequestParamsJsonSchema('cookie');
    }

    /**
     * @return array|null
     */
    public function toRequestHeaderJsonSchema() : ?array
    {
        return $this->toRequestParamsJsonSchema('header');
    }

    /**
     * @return array|null
     */
    public function toRequestQueryJsonSchema() : ?array
    {
        return $this->toRequestParamsJsonSchema('query');
    }

    /**
     * @param string $contentType
     *
     * @return array|null
     *
     * @throws UnsupportedMediaTypeException
     */
    public function toRequestBodyJsonSchema(string $contentType) : ?array
    {
        $requestBodyObject = $this->operation->requestBody;
        if (!isset($requestBodyObject)) {
            return null;
        }

        if ($requestBodyObject instanceof RequestBodyReference) {
            $requestBodyObject = $requestBodyObject->getReferencedObject();
        }

        if (!isset($requestBodyObject->content[$contentType])) {
            throw new UnsupportedMediaTypeException('Unsupported Media Type', [
                'type' => $contentType,
                'supported' => array_keys($requestBodyObject->content),
            ]);
        }

        if (!isset($requestBodyObject->content[$contentType]->schema)) {
            return null;
        }

        $jsonSchema = self::JSON_SCHEMA_BLANK;
        $jsonSchema += $requestBodyObject->content[$contentType]->schema->toArray();

        $referencedObjects = $requestBodyObject->getReferencedObjects();
        foreach ($referencedObjects as $referencedObject) {
            if ($referencedObject instanceof Schema) {
                $refName = $referencedObject->getReferenceName();
                $jsonSchema['definitions'][$refName] = $referencedObject->toArray();
            }
        }

        $jsonSchema = $this->fixJsonSchemaRefs($jsonSchema);
        $jsonSchema = $this->fixJsonSchemaNulls($jsonSchema);

        return $jsonSchema;
    }

    /**
     * @param mixed $statusCode
     * @param string $contentType
     *
     * @return array|null
     */
    public function toResponseBodyJsonSchema($statusCode, string $contentType) : ?array
    {
        $responseObject = $this->operation->responses[$statusCode] ?? null;
        if (!isset($responseObject)) {
            return null;
        }

        if ($responseObject instanceof ResponseReference) {
            $responseObject = $responseObject->getReferencedObject();
        }

        if (!isset($responseObject->content[$contentType]->schema)) {
            return null;
        }

        $jsonSchema = self::JSON_SCHEMA_BLANK;
        $jsonSchema += $responseObject->content[$contentType]->schema->toArray();

        $referencedObjects = $responseObject->getReferencedObjects();
        foreach ($referencedObjects as $referencedObject) {
            if ($referencedObject instanceof Schema) {
                $refName = $referencedObject->getReferenceName();
                $jsonSchema['definitions'][$refName] = $referencedObject->toArray();
            }
        }

        $jsonSchema = $this->fixJsonSchemaRefs($jsonSchema);
        $jsonSchema = $this->fixJsonSchemaNulls($jsonSchema);

        return $jsonSchema;
    }

    /**
     * @param string $parameterLocation
     *
     * @return array|null
     */
    private function toRequestParamsJsonSchema(string $parameterLocation) : ?array
    {
        $parameterObjects = $this->operation->parameters;
        if (empty($parameterObjects)) {
            return null;
        }

        $jsonSchema = [];
        foreach ($parameterObjects as $parameterObject) {
            if ($parameterObject instanceof ParameterReference) {
                $parameterObject = $parameterObject->getReferencedObject();
            }

            if ($parameterLocation <> $parameterObject->in) {
                continue;
            }

            // Note that RFC7230 states header names are case insensitive.
            // https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#parameter-locations
            $parameterName = $parameterObject->name;
            if ('header' === $parameterObject->in) {
                $parameterName = strtolower($parameterName);
            }

            if (true === $parameterObject->required) {
                $jsonSchema['required'][] = $parameterName;
            }

            if (isset($parameterObject->schema)) {
                $jsonSchema['properties'][$parameterName] = $parameterObject->schema->toArray();

                $referencedObjects = $parameterObject->getReferencedObjects();
                foreach ($referencedObjects as $referencedObject) {
                    if ($referencedObject instanceof Schema) {
                        $refName = $referencedObject->getReferenceName();
                        $jsonSchema['definitions'][$refName] = $referencedObject->toArray();
                    }
                }
            }
        }

        if (empty($jsonSchema)) {
            return null;
        }

        // restructuring the built json schema...
        $jsonSchema = self::JSON_SCHEMA_BLANK + ['type' => 'object'] + $jsonSchema;

        $jsonSchema = $this->fixJsonSchemaRefs($jsonSchema);
        $jsonSchema = $this->fixJsonSchemaNulls($jsonSchema);

        return $jsonSchema;
    }

    /**
     * TODO: OAS 3.1 supports the "definitions" path but isn't widely implemented...
     *
     * @param array $jsonSchema
     *
     * @return array
     */
    private function fixJsonSchemaRefs(array $jsonSchema) : array
    {
        array_walk_recursive($jsonSchema, function (&$value, $key) {
            if ('$ref' === $key) {
                $value = str_replace('#/components/schemas/', '#/definitions/', $value);
            }
        });

        return $jsonSchema;
    }

    /**
     * @param array $jsonSchema
     *
     * @return array
     */
    private function fixJsonSchemaNulls(array $jsonSchema) : array
    {
        $fixer = function (array &$array) use (&$fixer) {
            foreach ($array as $key => &$value) {
                if ('nullable' === $key && true === $value) {
                    $array['type'] = (array) ($array['type'] ?? []);
                    $array['type'][] = 'null';
                    unset($array['nullable']);
                } elseif (is_array($value)) {
                    $fixer($value);
                }
            }
        };

        $fixer($jsonSchema);

        return $jsonSchema;
    }
}
