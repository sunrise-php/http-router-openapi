<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Exception;

/**
 * UnsupportedMediaTypeException
 */
class UnsupportedMediaTypeException extends Exception
{

    /**
     * @var string
     */
    private $unsupportedMediaType;

    /**
     * @var string[]
     */
    private $supportedMediaTypes;

    /**
     * @param string $unsupportedMediaType
     * @param string[] $supportedMediaTypes
     */
    public function __construct(string $unsupportedMediaType, array $supportedMediaTypes)
    {
        $message = 'Media type "%s" is not supported for this operation.';

        parent::__construct(sprintf($message, $unsupportedMediaType));

        $this->unsupportedMediaType = $unsupportedMediaType;
        $this->supportedMediaTypes = $supportedMediaTypes;
    }

    /**
     * @return string
     */
    public function getUnsupportedMediaType() : string
    {
        return $this->unsupportedMediaType;
    }

    /**
     * @return string[]
     */
    public function getSupportedMediaTypes() : array
    {
        return $this->supportedMediaTypes;
    }
}
