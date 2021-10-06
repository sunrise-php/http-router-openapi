<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto;

/**
 * @OpenApi\Schema(
 *   type="object",
 *   properties={
 *     "email": @OpenApi\SchemaReference(".email"),
 *     "password": @OpenApi\SchemaReference(".password"),
 *   },
 * )
 */
final class User
{

    /**
     * @OpenApi\Schema(
     *   type="string",
     *   format="email",
     * )
     */
    public $email;

    /**
     * @OpenApi\Schema(
     *   type="string",
     *   format="password",
     * )
     */
    public $password;
}
