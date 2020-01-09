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
 * Import functions
 */
use function sprintf;

/**
 * UnsupportedMediaTypeException
 */
class UnsupportedMediaTypeException extends Exception
{

    /**
     * @var string
     */
    private $type;

    /**
     * @var string[]
     */
    private $supported;

    /**
     * @param string $type
     * @param string[] $supported
     */
    public function __construct(string $type, array $supported)
    {
        $format = 'Media type "%s" is not supported for this operation.';
        parent::__construct(sprintf($format, $type));

        $this->type = $type;
        $this->supported = $supported;
    }

    /**
     * Gets a type
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Gets supported types
     *
     * @return string[]
     */
    public function getSupportedTypes() : array
    {
        return $this->supported;
    }
}
