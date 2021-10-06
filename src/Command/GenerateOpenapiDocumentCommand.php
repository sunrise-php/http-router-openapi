<?php declare(strict_types=1);

/**
 * It's free open-source software released under the MIT License.
 *
 * @author Anatoly Fenric <anatoly@fenric.ru>
 * @copyright Copyright (c) 2019, Anatoly Fenric
 * @license https://github.com/sunrise-php/http-router-openapi/blob/master/LICENSE
 * @link https://github.com/sunrise-php/http-router-openapi
 */

namespace Sunrise\Http\Router\OpenApi\Command;

/**
 * Import classes
 */
use Sunrise\Http\Router\OpenApi\OpenApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateOpenapiDocumentCommand
 */
final class GenerateOpenapiDocumentCommand extends Command
{

    /**
     * Openapi instance
     *
     * @var OpenApi
     */
    private $openapi;

    /**
     * {@inheritdoc}
     *
     * @param OpenApi $openapi
     * @param string|null $name
     */
    public function __construct(OpenApi $openapi, ?string $name = null)
    {
        $this->openapi = $openapi;

        parent::__construct($name ?? 'router:generate-openapi-document');

        $this->addOption(
            'output-format',
            null,
            InputOption::VALUE_REQUIRED,
            'Output format ("json", "yaml")',
            'json'
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int Exit code
     */
    public function execute(InputInterface $input, OutputInterface $output) : int
    {
        switch ($input->getOption('output-format')) {
            case 'json':
                $output->writeln($this->openapi->toJson());
                return 0;
            case 'yaml':
                $output->writeln($this->openapi->toYaml());
                return 0;
            default:
                $output->writeln('<error>Unknown output format ("json", "yaml").</error>');
                return 1;
        }
    }
}
