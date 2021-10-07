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
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#parameter-object
 *
 * @final Don't use the object outside of the package.
 */
class Parameter extends AbstractAnnotation implements ParameterInterface, ComponentInterface
{

    /**
     * {@inheritdoc}
     */
    protected const IGNORE_FIELDS = ['refName'];

    /**
     * @var string
     */
    public $refName;

    /**
     * @Required
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parametername
     */
    public $name;

    /**
     * @Required
     *
     * @Enum({"cookie", "header", "query"})
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterin
     */
    public $in;

    /**
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterdescription
     */
    public $description;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterrequired
     */
    public $required;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterdeprecated
     */
    public $deprecated;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterallowemptyvalue
     */
    public $allowEmptyValue;

    /**
     * @Enum({"matrix", "label", "form", "simple", "spaceDelimited", "pipeDelimited", "deepObject"})
     *
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterstyle
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#style-values
     */
    public $style;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterexplode
     */
    public $explode;

    /**
     * @var bool
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterallowreserved
     */
    public $allowReserved;

    /**
     * @var \Sunrise\Http\Router\OpenApi\Annotation\OpenApi\SchemaInterface
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterschema
     */
    public $schema;

    /**
     * @var mixed
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterexample
     */
    public $example;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\ExampleInterface>
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parameterexamples
     */
    public $examples;

    /**
     * @var array<\Sunrise\Http\Router\OpenApi\Annotation\OpenApi\MediaTypeInterface>
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-parametercontent
     */
    public $content;

    /**
     * {@inheritdoc}
     */
    public function getComponentName() : string
    {
        return 'parameters';
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceName() : string
    {
        return $this->refName ?? spl_object_hash($this);
    }
}
