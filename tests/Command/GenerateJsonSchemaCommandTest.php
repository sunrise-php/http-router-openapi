<?php declare(strict_types=1);

namespace Sunrise\Http\Router\OpenApi\Tests\Command;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Sunrise\Http\Router\OpenApi\Command\GenerateJsonSchemaCommand;
use Sunrise\Http\Router\OpenApi\Tests\Fixtures\OpenapiAwareTrait;
use Symfony\Component\Console\Tester\CommandTester;

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
    }
}
