<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Utility;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Exception\UnsupportedMediaTypeException;
use Sunrise\Http\Router\OpenApi\Utility\JsonSchemaBuilder;
use ReflectionClass;

/**
 * JsonSchemaBuilderTest
 *
 * @group json-schema-builder
 *
 * @OpenApi\RequestBody(
 *   refName="ReferencedRequestBody",
 *   content={
 *     "application/json"=@OpenApi\MediaType(
 *       schema=@OpenApi\Schema(
 *         type="object",
 *         properties={
 *           "foo"=@OpenApi\SchemaReference(
 *             class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
 *             property="foo",
 *           ),
 *           "bar"=@OpenApi\SchemaReference(
 *             class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
 *             property="bar",
 *           ),
 *         },
 *       ),
 *     ),
 *   },
 * )
 *
 * @OpenApi\Response(
 *   refName="ReferencedResponse",
 *   description="OK",
 *   content={
 *     "application/json"=@OpenApi\MediaType(
 *       schema=@OpenApi\Schema(
 *         type="object",
 *         properties={
 *           "foo"=@OpenApi\SchemaReference(
 *             class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
 *             property="foo",
 *           ),
 *           "bar"=@OpenApi\SchemaReference(
 *             class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
 *             property="bar",
 *           ),
 *         },
 *       ),
 *     ),
 *   },
 * )
 */
class JsonSchemaBuilderTest extends TestCase
{

    /**
     * @OpenApi\Schema(
     *   refName="ReferencedFooProperty",
     *   type="integer",
     * )
     */
    private $foo;

    /**
     * @OpenApi\Schema(
     *   refName="ReferencedBarProperty",
     *   type="string",
     * )
     */
    private $bar;

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBody() : void
    {
        /**
         * @OpenApi\Operation(
         *   requestBody=@OpenApi\RequestBody(
         *     content={
         *       "application/json"=@OpenApi\MediaType(
         *         schema=@OpenApi\Schema(
         *           type="object",
         *           properties={
         *             "foo"=@OpenApi\Schema(
         *               type="string",
         *             ),
         *           },
         *         ),
         *       ),
         *     },
         *   ),
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forRequestBody('application/json');

        $this->assertSame([
            '$schema' => 'http://json-schema.org/draft-00/schema#',
            'properties' => [
                'foo' => [
                    'type' => 'string',
                ],
            ],
            'type' => 'object',
        ], $jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBodyWithReferencesToSchemas() : void
    {
        /**
         * @OpenApi\Operation(
         *   requestBody=@OpenApi\RequestBody(
         *     content={
         *       "application/json"=@OpenApi\MediaType(
         *         schema=@OpenApi\Schema(
         *           type="object",
         *           properties={
         *             "foo"=@OpenApi\SchemaReference(
         *               class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *               property="foo",
         *             ),
         *             "bar"=@OpenApi\SchemaReference(
         *               class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *               property="bar",
         *             ),
         *           },
         *         ),
         *       ),
         *     },
         *   ),
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forRequestBody('application/json');

        $this->assertSame([
            '$schema' => 'http://json-schema.org/draft-00/schema#',
            'definitions' => [
                'ReferencedFooProperty' => [
                    'type' => 'integer',
                ],
                'ReferencedBarProperty' => [
                    'type' => 'string',
                ],
            ],
            'properties' => [
                'foo' => [
                    '$ref' => '#/definitions/ReferencedFooProperty',
                ],
                'bar' => [
                    '$ref' => '#/definitions/ReferencedBarProperty',
                ],
            ],
            'type' => 'object',
        ], $jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBodyWithReferenceToRequestBody() : void
    {
        /**
         * @OpenApi\Operation(
         *   requestBody=@OpenApi\RequestBodyReference(
         *     class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *   ),
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forRequestBody('application/json');

        $this->assertSame([
            '$schema' => 'http://json-schema.org/draft-00/schema#',
            'definitions' => [
                'ReferencedFooProperty' => [
                    'type' => 'integer',
                ],
                'ReferencedBarProperty' => [
                    'type' => 'string',
                ],
            ],
            'properties' => [
                'foo' => [
                    '$ref' => '#/definitions/ReferencedFooProperty',
                ],
                'bar' => [
                    '$ref' => '#/definitions/ReferencedBarProperty',
                ],
            ],
            'type' => 'object',
        ], $jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBodyWhenOperationUnknown() : void
    {
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forRequestBody('application/json');

        $this->assertNull($jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBodyWhenOperationDoesntContainRequestBody() : void
    {
        /**
         * @OpenApi\Operation(
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forRequestBody('application/json');

        $this->assertNull($jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBodyWhenRequestBodyContainsContentWithoutSchema() : void
    {
        /**
         * @OpenApi\Operation(
         *   requestBody=@OpenApi\RequestBody(
         *     content={
         *       "application/json"=@OpenApi\MediaType(
         *       ),
         *     },
         *   ),
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forRequestBody('application/json');

        $this->assertNull($jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBodyWhenRequestBodyContainsEmptyContents() : void
    {
        /**
         * @OpenApi\Operation(
         *   requestBody=@OpenApi\RequestBody(
         *     content={
         *     },
         *   ),
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);

        $this->expectException(UnsupportedMediaTypeException::class);
        $this->expectExceptionMessage('Media type "application/json" is not supported for this operation.');

        try {
            $jsonSchemaBuilder->forRequestBody('application/json');
        } catch (UnsupportedMediaTypeException $e) {
            $this->assertSame(
                'application/json',
                $e->getType()
            );

            $this->assertSame([
            ], $e->getSupportedTypes());

            throw $e;
        }
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForRequestBodyWithUnsupportedMediaType() : void
    {
        /**
         * @OpenApi\Operation(
         *   requestBody=@OpenApi\RequestBody(
         *     content={
         *       "application/json"=@OpenApi\MediaType(
         *       ),
         *       "application/xml"=@OpenApi\MediaType(
         *       ),
         *     },
         *   ),
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);

        $this->expectException(UnsupportedMediaTypeException::class);
        $this->expectExceptionMessage('Media type "application/schema+json" is not supported for this operation.');

        try {
            $jsonSchemaBuilder->forRequestBody('application/schema+json');
        } catch (UnsupportedMediaTypeException $e) {
            $this->assertSame(
                'application/schema+json',
                $e->getType()
            );

            $this->assertSame([
                'application/json',
                'application/xml',
            ], $e->getSupportedTypes());

            throw $e;
        }
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForResponseBody() : void
    {
        /**
         * @OpenApi\Operation(
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *       content={
         *         "application/json"=@OpenApi\MediaType(
         *           schema=@OpenApi\Schema(
         *             type="object",
         *             properties={
         *               "foo"=@OpenApi\Schema(
         *                 type="string",
         *               ),
         *             },
         *           ),
         *         ),
         *       },
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forResponseBody(200, 'application/json');

        $this->assertSame([
            '$schema' => 'http://json-schema.org/draft-00/schema#',
            'properties' => [
                'foo' => [
                    'type' => 'string',
                ],
            ],
            'type' => 'object',
        ], $jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForResponseBodyWithReferencesToSchemas() : void
    {
        /**
         * @OpenApi\Operation(
         *   responses={
         *     200=@OpenApi\Response(
         *       description="OK",
         *       content={
         *         "application/json"=@OpenApi\MediaType(
         *           schema=@OpenApi\Schema(
         *             type="object",
         *             properties={
         *               "foo"=@OpenApi\SchemaReference(
         *                 class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *                 property="foo",
         *               ),
         *               "bar"=@OpenApi\SchemaReference(
         *                 class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *                 property="bar",
         *               ),
         *             },
         *           ),
         *         ),
         *       },
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forResponseBody(200, 'application/json');

        $this->assertSame([
            '$schema' => 'http://json-schema.org/draft-00/schema#',
            'definitions' => [
                'ReferencedFooProperty' => [
                    'type' => 'integer',
                ],
                'ReferencedBarProperty' => [
                    'type' => 'string',
                ],
            ],
            'properties' => [
                'foo' => [
                    '$ref' => '#/definitions/ReferencedFooProperty',
                ],
                'bar' => [
                    '$ref' => '#/definitions/ReferencedBarProperty',
                ],
            ],
            'type' => 'object',
        ], $jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForResponseBodyWithReferenceToResponse() : void
    {
        /**
         * @OpenApi\Operation(
         *   responses={
         *     200=@OpenApi\ResponseReference(
         *       class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forResponseBody(200, 'application/json');

        $this->assertSame([
            '$schema' => 'http://json-schema.org/draft-00/schema#',
            'definitions' => [
                'ReferencedFooProperty' => [
                    'type' => 'integer',
                ],
                'ReferencedBarProperty' => [
                    'type' => 'string',
                ],
            ],
            'properties' => [
                'foo' => [
                    '$ref' => '#/definitions/ReferencedFooProperty',
                ],
                'bar' => [
                    '$ref' => '#/definitions/ReferencedBarProperty',
                ],
            ],
            'type' => 'object',
        ], $jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForResponseBodyWhenOperationUnknown() : void
    {
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forResponseBody(200, 'application/json');

        $this->assertNull($jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForResponseBodyWhenStatusCodeUnsupported() : void
    {
        /**
         * @OpenApi\Operation(
         *   responses={
         *     200=@OpenApi\ResponseReference(
         *       class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forResponseBody(201, 'application/json');

        $this->assertNull($jsonSchema);
    }

    /**
     * @return void
     */
    public function testBuildJsonSchemaForResponseBodyWhenMediaTypeUnsupported() : void
    {
        /**
         * @OpenApi\Operation(
         *   responses={
         *     200=@OpenApi\ResponseReference(
         *       class="Sunrise\Http\Router\OpenApi\Tests\Utility\JsonSchemaBuilderTest",
         *     ),
         *   },
         * )
         */
        $class = new class
        {
        };

        $classReflection = new ReflectionClass($class);
        $jsonSchemaBuilder = new JsonSchemaBuilder($classReflection);
        $jsonSchema = $jsonSchemaBuilder->forResponseBody(200, 'application/schema+json');

        $this->assertNull($jsonSchema);
    }
}
