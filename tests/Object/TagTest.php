<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Object;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Object\ExternalDocumentation;
use Sunrise\Http\Router\OpenApi\Object\Tag;
use Sunrise\Http\Router\OpenApi\AbstractObject;

/**
 * TagTest
 */
class TagTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Tag('foo');

        $this->assertInstanceOf(AbstractObject::class, $object);
    }

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $object = new Tag('foo');

        $this->assertSame([
            'name' => 'foo',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetDescription() : void
    {
        $object = new Tag('foo');
        $object->setDescription('bar');

        $this->assertSame([
            'name' => 'foo',
            'description' => 'bar',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetExternalDocs() : void
    {
        $object = new Tag('foo');
        $object->setExternalDocs(new ExternalDocumentation('bar'));

        $this->assertSame([
            'name' => 'foo',
            'externalDocs' => [
                'url' => 'bar',
            ],
        ], $object->toArray());
    }
}
