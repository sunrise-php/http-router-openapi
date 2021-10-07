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
 * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#link-object
 */
final class Link extends AbstractAnnotation implements LinkInterface, ComponentInterface
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
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-linkoperationref
     */
    public $operationRef;

    /**
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-linkoperationid
     */
    public $operationId;

    /**
     * @var array
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-linkparameters
     */
    public $parameters;

    /**
     * @var mixed
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-linkrequestbody
     */
    public $requestBody;

    /**
     * @var string
     *
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.2.md#user-content-linkdescription
     */
    public $description;

    /**
     * {@inheritdoc}
     */
    public function getComponentName() : string
    {
        return 'links';
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceName() : string
    {
        return $this->refName ?? spl_object_hash($this);
    }
}
