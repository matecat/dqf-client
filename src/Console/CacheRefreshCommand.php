<?php
namespace Matecat\Dqf\Console;

use Matecat\Dqf\Cache\BasicAttributes;
use Matecat\Dqf\Client;
use Matecat\Dqf\Commands\CommandHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheRefreshCommand extends Command
{
    /**
     * @var Client
     */
    private $dqfClient;

    /**
     * CacheStatsCommand constructor.
     *
     * @param Client $dqfClient
     * @param null   $name
     */
    public function __construct(Client $dqfClient, $name = null)
    {
        parent::__construct($name);
        $this->dqfClient = $dqfClient;
    }

    protected function configure()
    {
        $this
            ->setName('dqf:cache:refresh')
            ->setDescription('Refresh the local cache for basic attributes.')
            ->setHelp('This command allows you to refresh the local cache for basic attributes.')
            ->addArgument('data-path', InputArgument::OPTIONAL, 'Override the default data path')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if (false === empty($input->getArgument('data-path'))) {
                BasicAttributes::setDataFile($input->getArgument('data-path'));
            }

            BasicAttributes::refresh($this->dqfClient);
            $io->success('Basic attributes were successfully refreshed');
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }
    }
}
