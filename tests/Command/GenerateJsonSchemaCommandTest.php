<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Command;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\OpenApi\Command\GenerateJsonSchemaCommand;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\OpenapiAwareTrait;
use Symfony\Component\Console\Tester\CommandTester;
use RuntimeException;

/**
 * GenerateJsonSchemaCommandTest
 */
class GenerateJsonSchemaCommandTest extends TestCase
{
    use OpenapiAwareTrait;

    /**
     * @return void
     */
    public function testRun() : void
    {
        $command = new GenerateJsonSchemaCommand($this->getOpenapi());
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            'operation-id' => 'users.create',
            'operation-section' => 'body',
            '--content-type' => 'application/json',
        ]));

        $this->assertSame(0, $commandTester->execute([
            'operation-id' => 'users.list',
            'operation-section' => 'cookie',
        ]));

        $this->assertSame(0, $commandTester->execute([
            'operation-id' => 'users.list',
            'operation-section' => 'header',
        ]));

        $this->assertSame(0, $commandTester->execute([
            'operation-id' => 'users.list',
            'operation-section' => 'query',
        ]));

        $this->assertSame(1, $commandTester->execute([
            'operation-id' => 'users.list',
            'operation-section' => 'unknown',
        ]));

        $this->assertSame(1, $commandTester->execute([
            'operation-id' => 'unknown',
            'operation-section' => 'body',
        ]));
    }

    /**
     * @return void
     */
    public function testRunInheritedCommand() : void
    {
        // @codingStandardsIgnoreStart
        $command = new class($this->getOpenapi()) extends GenerateJsonSchemaCommand {
            private $_openapi;

            public function __construct(OpenApi $openapi) {
                $this->_openapi = $openapi;
                parent::__construct(null);
            }

            protected function getOpenapi() : OpenApi {
                return $this->_openapi;
            }
        };
        // @codingStandardsIgnoreEnd

        $this->assertSame('router:generate-json-schema', $command->getName());

        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            'operation-id' => 'users.create',
            'operation-section' => 'body',
        ]));
    }

    /**
     * @return void
     */
    public function testRunRenamedCommand() : void
    {
        // @codingStandardsIgnoreStart
        $command = new class ($this->getOpenapi()) extends GenerateJsonSchemaCommand {
            protected static $defaultName = 'foo';
            protected static $defaultDescription = 'bar';

            public function __construct(OpenApi $openapi) {
                parent::__construct($openapi);
            }
        };
        // @codingStandardsIgnoreEnd

        $this->assertSame('foo', $command->getName());
        $this->assertSame('bar', $command->getDescription());

        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            'operation-id' => 'users.create',
            'operation-section' => 'body',
        ]));
    }

    /**
     * @return void
     */
    public function testRunWithoutOpenapi() : void
    {
        $command = new GenerateJsonSchemaCommand();
        $commandTester = new CommandTester($command);

        $this->expectException(RuntimeException::class);

        $commandTester->execute([
            'operation-id' => 'users.create',
            'operation-section' => 'body',
        ]);
    }
}
