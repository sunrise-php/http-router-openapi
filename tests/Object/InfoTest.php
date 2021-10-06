<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Object;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractObject;
use Sunrise\Http\Router\OpenApi\Object\Contact;
use Sunrise\Http\Router\OpenApi\Object\Info;
use Sunrise\Http\Router\OpenApi\Object\License;

/**
 * InfoTest
 */
class InfoTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = new Info('foo', 'bar');

        $this->assertInstanceOf(AbstractObject::class, $object);
    }

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $object = new Info('foo', 'bar');

        $this->assertSame([
            'title' => 'foo',
            'version' => 'bar',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetSummary() : void
    {
        $object = new Info('foo', 'bar');
        $object->setSummary('baz');

        $this->assertSame([
            'title' => 'foo',
            'summary' => 'baz',
            'version' => 'bar',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetDescription() : void
    {
        $object = new Info('foo', 'bar');
        $object->setDescription('baz');

        $this->assertSame([
            'title' => 'foo',
            'description' => 'baz',
            'version' => 'bar',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetTermsOfService() : void
    {
        $object = new Info('foo', 'bar');
        $object->setTermsOfService('baz');

        $this->assertSame([
            'title' => 'foo',
            'termsOfService' => 'baz',
            'version' => 'bar',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetContact() : void
    {
        $object = new Info('foo', 'bar');
        $object->setContact(new Contact('baz'));

        $this->assertSame([
            'title' => 'foo',
            'contact' => [
                'name' => 'baz',
            ],
            'version' => 'bar',
        ], $object->toArray());
    }

    /**
     * @return void
     */
    public function testSetLicense() : void
    {
        $object = new Info('foo', 'bar');
        $object->setLicense(new License('baz'));

        $this->assertSame([
            'title' => 'foo',
            'license' => [
                'name' => 'baz',
            ],
            'version' => 'bar',
        ], $object->toArray());
    }
}
