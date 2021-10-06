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
use Sunrise\Http\Router\OpenApi\ComponentInterface;

/**
 * Import functions
 */
use function spl_object_hash;

/**
 * @Annotation
 *
 * @Target({"ALL"})
 *
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#schema-object
 * @link https://json-schema.org/draft/2019-09/json-schema-validation.html
 */
final class Schema extends AbstractAnnotation implements SchemaInterface, ComponentInterface
{

    /**
     * {@inheritdoc}
     */
    protected const IGNORE_FIELDS = ['refName'];

    /**
     * {@inheritdoc}
     */
    protected const FIELD_ALIASES = ['allowAdditionalProperties' => 'additionalProperties'];

    /**
     * @var string
     */
    public $refName;

    /**
     * @var bool
     */
    public $allowAdditionalProperties;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface
     */
    public $additionalProperties;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface>
     */
    public $allOf;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface>
     */
    public $anyOf;

    /**
     * @var mixed
     */
    public $default;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemadeprecated
     */
    public $deprecated;

    /**
     * @var string
     */
    public $description;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\DiscriminatorInterface
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemadiscriminator
     */
    public $discriminator;

    /**
     * @var array
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.1.2
     */
    public $enum;

    /**
     * @var mixed
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemaexample
     */
    public $example;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.2.3
     */
    public $exclusiveMaximum;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.2.5
     */
    public $exclusiveMinimum;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExternalDocumentationInterface
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemaexternaldocs
     */
    public $externalDocs;

    /**
     * @var string
     */
    public $format;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface
     */
    public $items;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.2.2
     */
    public $maximum;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.4.1
     */
    public $maxItems;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.3.1
     */
    public $maxLength;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.5.1
     */
    public $maxProperties;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.2.4
     */
    public $minimum;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.4.2
     */
    public $minItems;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.3.2
     */
    public $minLength;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.5.2
     */
    public $minProperties;

    /**
     * @var int
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.2.1
     */
    public $multipleOf;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface
     */
    public $not;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemanullable
     */
    public $nullable;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface>
     */
    public $oneOf;

    /**
     * @var string
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.3.3
     */
    public $pattern;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface>
     */
    public $properties;

    /**
     * [!] Isn't standard property
     *
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface>
     *
     * @since 1.4.1
     */
    public $patternProperties;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemareadonly
     */
    public $readOnly;

    /**
     * @var array<string>
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.5.3
     */
    public $required;

    /**
     * @var string
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.9.1
     */
    public $title;

    /**
     * @var string
     */
    public $type;

    /**
     * @var bool
     *
     * @link https://json-schema.org/draft/2019-09/json-schema-validation.html#rfc.section.6.4.3
     */
    public $uniqueItems;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemawriteonly
     */
    public $writeOnly;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\XmlInterface
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-schemaxml
     */
    public $xml;

    /**
     * {@inheritdoc}
     */
    public function getComponentName() : string
    {
        return 'schemas';
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceName() : string
    {
        return $this->refName ?? spl_object_hash($this);
    }
}
