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
use RuntimeException;
use Sunrise\Http\Router\OpenApi\OpenApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import functions
 */
use function sprintf;

/**
 * This command generates OpenAPI document
 *
 * If you cannot pass the openapi to the constructor,
 * or your architecture has problems with autowiring,
 * then inherit this class and override the getOpenapi method.
 *
 * @since 2.0.0
 */
class GenerateOpenapiDocumentCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'router:generate-openapi-document';

    /**
     * {@inheritdoc}
     */
    protected static $defaultDescription = 'Generates OpenAPI document';

    /**
     * The openapi instance
     *
     * @var OpenApi|null
     */
    private $openapi;

    /**
     * Constructor of the class
     *
     * @param OpenApi|null $openapi
     */
    public function __construct(?OpenApi $openapi = null)
    {
        $this->openapi = $openapi;

        parent::__construct();

        $this->setName(static::$defaultName);
        $this->setDescription(static::$defaultDescription);

        $this->addOption(
            'output-format',
            null,
            InputOption::VALUE_REQUIRED,
            'Output format ("json", "yaml")',
            'json'
        );
    }

    /**
     * Gets the openapi instance
     *
     * @return OpenApi
     *
     * @throws RuntimeException
     *         If the class doesn't contain the openapi instance.
     */
    protected function getOpenapi() : OpenApi
    {
        if (null === $this->openapi) {
            throw new RuntimeException(sprintf(
                'The %2$s() method MUST return the %1$s class instance. ' .
                'Pass the %1$s class instance to the constructor, or override the %2$s() method.',
                OpenApi::class,
                __METHOD__
            ));
        }

        return $this->openapi;
    }

    /**
     * {@inheritdoc}
     */
    final protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $openapi = $this->getOpenapi();

        switch ($input->getOption('output-format')) {
            case 'json':
                $output->writeln($openapi->toJson());
                return 0;
            case 'yaml':
                $output->writeln($openapi->toYaml());
                return 0;
            default:
                $output->writeln('<error>Unknown output format ("json", "yaml").</error>');
                return 1;
        }
    }
}
