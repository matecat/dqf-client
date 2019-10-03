<?php
namespace Matecat\Dqf\Console;

use Matecat\Dqf\Client;
use Matecat\Dqf\Commands\CommandHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientHelperCommand extends Command
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
            ->setName('dqf:client:helper')
            ->setDescription('Get the client\'s list of available commands.')
            ->setHelp('This command displays the complete list of all client\'s available commands.')
            ->addArgument('method', InputArgument::OPTIONAL, 'method')
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
        $this->renderTable($output, $this->getMethods($input));
    }

    /**
     * @param InputInterface $input
     *
     * @return array
     */
    private function getMethods(InputInterface $input)
    {
        $methods = [];

        // get single method
        if (false === empty($input->getArgument('method'))) {
            $method = ucfirst($input->getArgument('method'));
            $commandHandler = 'Matecat\\Dqf\\Commands\\Handlers\\' . $method;

            if (false === class_exists($commandHandler)) {
                throw new \InvalidArgumentException($commandHandler . ' is not a valid command name. Launch the command without specifying method.');
            }

            $methods[] = lcfirst($input->getArgument('method'));

            return $methods;
        }

        // get all methods
        $dir = scandir(__DIR__.'/../../src/Commands/Handlers/');
        foreach ($dir as $file) {
            if ($file !== '.' and $file !== '..') {
                $methods[] = str_replace('.php', '', lcfirst($file));
            }
        }

        return $methods;
    }

    /**
     * @param OutputInterface $output
     * @param array           $methods
     *
     * @throws \ReflectionException
     */
    private function renderTable(OutputInterface $output, array $methods)
    {
        $table = new Table($output);
        $table->setHeaders(['method', 'parameter(s)', 'type', 'required']);

        foreach ($methods as $method) {
            $commandHandler = $this->getCommandHandler($method);

            $keys  = [];
            $reqs  = [];
            $types = [];

            $rules = $commandHandler->getRules();
            ksort($rules);

            foreach ($rules as $key => $rule) {
                $keys[] = $key ;
                $reqs[] = ($rule['required']) ? 'YES' : 'NO';
                $types[] = $rule['type'] ;
            }

            $keys = implode(PHP_EOL, $keys);
            $reqs = implode(PHP_EOL, $reqs);
            $types = implode(PHP_EOL, $types);

            $table->addRow([$method, $keys, $types, $reqs]);

            if ($method !== end($methods)) {
                $table->addRow(new TableSeparator());
            }
        }

        $table->render();
    }

    /**
     * @param $method
     *
     * @return CommandHandler
     * @throws \ReflectionException
     */
    private function getCommandHandler($method)
    {
        $commandHandler = 'Matecat\\Dqf\\Commands\\Handlers\\' . ucfirst($method);

        return new $commandHandler($this->dqfClient->getHttpClient(), $this->dqfClient->getClientParams());
    }
}
