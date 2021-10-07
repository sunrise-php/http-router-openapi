<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Fixtures\SomeApp\Controller;

final class InvalidController
{
    private $foo;

    private function foo()
    {
    }

    /**
     * @OpenApi\Operation(
     *   responses={
     *     "default": @OpenApi\ResponseReference("UndefinedClass"),
     *   },
     * )
     */
    public function refersToUndefinedClass()
    {
    }

    /**
     * @OpenApi\Operation(
     *   responses={
     *     "default": @OpenApi\ResponseReference("@undefinedMethod"),
     *   },
     * )
     */
    public function refersToUndefinedClassMethod()
    {
    }

    /**
     * @OpenApi\Operation(
     *   responses={
     *     "default": @OpenApi\ResponseReference(".undefinedProperty"),
     *   },
     * )
     */
    public function refersToUndefinedClassProperty()
    {
    }

    /**
     * @OpenApi\Operation(
     *   responses={
     *     "default": @OpenApi\ResponseReference(""),
     *   },
     * )
     */
    public function refersToClassWithoutTarget()
    {
    }

    /**
     * @OpenApi\Operation(
     *   responses={
     *     "default": @OpenApi\ResponseReference("@foo"),
     *   },
     * )
     */
    public function refersToClassMethodWithoutTarget()
    {
    }

    /**
     * @OpenApi\Operation(
     *   responses={
     *     "default": @OpenApi\ResponseReference(".foo"),
     *   },
     * )
     */
    public function refersToClassPropertyWithoutTarget()
    {
    }
}
