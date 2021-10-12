<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Command;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Sunrise\Http\Router\OpenApi\Command\GenerateOpenapiDocumentCommand;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\OpenapiAwareTrait;
use Symfony\Component\Console\Tester\CommandTester;
use RuntimeException;

/**
 * GenerateOpenapiDocumentCommandTest
 */
class GenerateOpenapiDocumentCommandTest extends TestCase
{
    use OpenapiAwareTrait;

    /**
     * @return void
     */
    public function testRun() : void
    {
        $command = new GenerateOpenapiDocumentCommand($this->getOpenapi());
        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([
            '--output-format' => 'json',
        ]));

        $this->assertSame(0, $commandTester->execute([
            '--output-format' => 'yaml',
        ]));

        $this->assertSame(1, $commandTester->execute([
            '--output-format' => 'unknown',
        ]));
    }

    /**
     * @return void
     */
    public function testRunInheritedCommand() : void
    {
        // @codingStandardsIgnoreStart
        $command = new class($this->getOpenapi()) extends GenerateOpenapiDocumentCommand {
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

        $this->assertSame('router:generate-openapi-document', $command->getName());

        $commandTester = new CommandTester($command);

        $this->assertSame(0, $commandTester->execute([]));
    }

    /**
     * @return void
     */
    public function testRunRenamedCommand() : void
    {
        // @codingStandardsIgnoreStart
        $command = new class ($this->getOpenapi()) extends GenerateOpenapiDocumentCommand {
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

        $this->assertSame(0, $commandTester->execute([]));
    }

    /**
     * @return void
     */
    public function testRunWithoutOpenapi() : void
    {
        $command = new GenerateOpenapiDocumentCommand();
        $commandTester = new CommandTester($command);

        $this->expectException(RuntimeException::class);

        $this->assertSame(0, $commandTester->execute([]));
    }
}
