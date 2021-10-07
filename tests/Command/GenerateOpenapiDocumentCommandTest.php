<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Command;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Command\GenerateOpenapiDocumentCommand;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\OpenapiAwareTrait;
use Symfony\Component\Console\Tester\CommandTester;

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
}
