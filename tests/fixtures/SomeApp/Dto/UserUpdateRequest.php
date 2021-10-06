<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto;

/**
 * @OpenApi\RequestBody(
 *   content={
 *     "application/json": @OpenApi\MediaType(
 *       schema=@OpenApi\Schema(
 *         type="object",
 *         required={
 *           "email",
 *           "password",
 *         },
 *         properties={
 *           "email": @OpenApi\SchemaReference("User.email"),
 *           "password": @OpenApi\SchemaReference("User.password"),
 *         },
 *       ),
 *     ),
 *   },
 * )
 */
final class UserUpdateRequest
{
}
