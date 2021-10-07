<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Object;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractObject;
use Sunrise\Http\Router\OpenApi\Object\Contact;

/**
 * ContactTest
 */
class ContactTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Contact();

        $this->assertInstanceOf(AbstractObject::class, $object);
    }

    /**
     * @return void
     */
    public function testSetName() : void
    {
        $object = new Contact();
        $object->setName('foo');

        $this->assertSame([
            'name' => 'foo',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetUrl() : void
    {
        $object = new Contact();
        $object->setUrl('foo');

        $this->assertSame([
            'url' => 'foo',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetEmail() : void
    {
        $object = new Contact();
        $object->setEmail('foo');

        $this->assertSame([
            'email' => 'foo',
        ], $object->toArray());
    }
}
