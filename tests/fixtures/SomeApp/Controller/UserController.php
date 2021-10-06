<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Factory\ResponseFactory;

/**
 * @Route(
 *   name="0FEEFC72-6BDD-4D11-B449-F35559303D6F",
 *   path="/0FEEFC72-6BDD-4D11-B449-F35559303D6F",
 *   method="GET",
 * )
 */
final class UserController implements RequestHandlerInterface
{

    /**
     * @OpenApi\Schema(
     *   type="string",
     *   format="uuid",
     * )
     */
    private $key = null;

    /**
     * @OpenApi\Schema(
     *   type="integer",
     *   nullable=true,
     * )
     */
    private $limit = null;

    /**
     * @OpenApi\ParameterHeader(
     *   name="x-key",
     *   schema=@OpenApi\SchemaReference(".key"),
     *   required=true,
     * )
     */
    private function getKey()
    {
        return $this->key;
    }

    /**
     * @Route(
     *   name="users.list",
     *   path="/users",
     *   method="GET",
     *   summary="A list of users",
     *   description="Returns a list of user models",
     *   tags={"foo", "bar"},
     * )
     *
     * @codingStandardsIgnoreStart
     *
     * @OpenApi\Operation(
     *   parameters={
     *     @OpenApi\ParameterCookie(
     *       name="limit",
     *       schema=@OpenApi\SchemaReference(".limit"),
     *     ),
     *     @OpenApi\ParameterHeader(
     *       name="x-limit",
     *       schema=@OpenApi\SchemaReference(".limit"),
     *     ),
     *     @OpenApi\ParameterQuery(
     *       name="limit",
     *       schema=@OpenApi\SchemaReference(".limit"),
     *     ),
     *   },
     *   responses={
     *     200: @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\UserListResponse"),
     *     "default": @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\ErrorResponse"),
     *   },
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function list()
    {
        return (new ResponseFactory)->createResponse(200);
    }

    /**
     * @Route("users.read", path="/users/{id<\d+>}", method="GET")
     *
     * @codingStandardsIgnoreStart
     *
     * @OpenApi\Operation(
     *   responses={
     *     200: @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\UserResponse"),
     *     "default": @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\ErrorResponse"),
     *   },
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function read()
    {
        return (new ResponseFactory)->createResponse(200);
    }

    /**
     * @Route("users.create", path="/users", method="POST")
     *
     * @codingStandardsIgnoreStart
     *
     * @OpenApi\Operation(
     *   parameters={
     *     @OpenApi\ParameterReference("@getKey"),
     *   },
     *   requestBody=@OpenApi\RequestBodyReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\UserCreateRequest"),
     *   responses={
     *     201: @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\EmptyResponse"),
     *     "default": @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\ErrorResponse"),
     *   },
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function create()
    {
        return (new ResponseFactory)->createResponse(201);
    }

    /**
     * @Route("users.update", path="/users/{id<\d+>}", method="PATCH")
     *
     * @codingStandardsIgnoreStart
     *
     * @OpenApi\Operation(
     *   parameters={
     *     @OpenApi\ParameterReference("@getKey"),
     *   },
     *   requestBody=@OpenApi\RequestBodyReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\UserUpdateRequest"),
     *   responses={
     *     200: @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\EmptyResponse"),
     *     "default": @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\ErrorResponse"),
     *   },
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function update()
    {
        return (new ResponseFactory)->createResponse(200);
    }

    /**
     * @Route("users.delete", path="/users/{id<\d+>}", method="DELETE")
     *
     * @codingStandardsIgnoreStart
     *
     * @OpenApi\Operation(
     *   parameters={
     *     @OpenApi\ParameterReference("@getKey"),
     *   },
     *   responses={
     *     200: @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\EmptyResponse"),
     *     "default": @OpenApi\ResponseReference("Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Dto\ErrorResponse"),
     *   },
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function delete()
    {
        return (new ResponseFactory)->createResponse(200);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return (new ResponseFactory)->createResponse(400);
    }
}
