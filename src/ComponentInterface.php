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
 * ComponentInterface
 */
interface ComponentInterface extends ObjectInterface
{

    /**
     * Gets a component name
     *
     * @return string
     */
    public function getComponentName() : string;

    /**
     * Gets a reference name
     *
     * @return string
     */
    public function getReferenceName() : string;
}
