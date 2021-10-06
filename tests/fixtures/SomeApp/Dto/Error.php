<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto;

/**
 * @OpenApi\Schema(
 *   type="object",
 *   properties={
 *     "code": @OpenApi\SchemaReference(".code"),
 *     "message": @OpenApi\SchemaReference(".message"),
 *   },
 * )
 */
final class Error
{

    /**
     * @OpenApi\Schema(
     *   type="integer",
     * )
     */
    public $code;

    /**
     * @OpenApi\Schema(
     *   type="string",
     * )
     */
    public $message;
}
