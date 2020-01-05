<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\AbstractObject;
use Sunrise\Http\Router\OpenApi\ObjectInterface;

/**
 * AbstractObjectTest
 */
class AbstractObjectTest extends TestCase
{

    /**
     * @return void
     */
    public function testContracts() : void
    {
        $object = $this->createMock(AbstractObject::class);

        $this->assertInstanceOf(ObjectInterface::class, $object);
    }

    /**
     * @return void
     */
    public function testToArray() : void
    {
        $foo = new class extends AbstractObject
        {
            public $foo = 'foo';
        };

        $bar = new class extends AbstractObject
        {
            public $bar = 'bar';
        };

        $baz = new class extends AbstractObject
        {
        };

        $object = new class ($foo, $bar, $baz) extends AbstractObject
        {
            protected const IGNORE_FIELDS = [
                'p12',
                'p20',
            ];

            protected const FIELD_ALIASES = [
                'p13' => 'p13a',
                'p21' => 'p21a',
            ];

            /** @scrutinizer ignore-unused */ private $p01;
            /** @scrutinizer ignore-unused */ private $p02 = 0;
            /** @scrutinizer ignore-unused */ private $p03 = [];
            /** @scrutinizer ignore-unused */ private $p04 = '';
            /** @scrutinizer ignore-unused */ private $p05 = 'value';
            /** @scrutinizer ignore-unused */ private $p06;

            protected $p07;
            protected $p08 = 0;
            protected $p09 = [];
            protected $p10 = '';
            protected $p11 = 'value';
            protected $p12 = 'value';
            protected $p13 = 'value';
            protected $p14;

            public $p15;
            public $p16 = 0;
            public $p17 = [];
            public $p18 = '';
            public $p19 = 'value';
            public $p20 = 'value';
            public $p21 = 'value';
            public $p22;

            public function __construct($foo, $bar, $baz)
            {
                $this->p06 = $foo;
                $this->p14 = $bar;
                $this->p22 = $baz;
            }
        };

        $this->assertSame([
            'p08' => 0,
            'p09' => [],
            'p10' => '',
            'p11' => 'value',
            'p13a' => 'value',
            'p14' => ['bar' => 'bar'],
            'p16' => 0,
            'p17' => [],
            'p18' => '',
            'p19' => 'value',
            'p21a' => 'value',
            'p22' => [],
        ], $object->toArray());
    }
}
