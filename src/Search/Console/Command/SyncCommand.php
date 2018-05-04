<?php

namespace Mulwi\Search\Console\Command;

use Mulwi\Search\Repository\IndexRepository;
use Mulwi\Search\Model\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State as AppState;

class SyncCommand extends Command
{
    /**
     * @var IndexRepository
     */
    private $indexRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AppState
     */
    private $appState;

    public function __construct(
        IndexRepository $indexRepository,
        Config $config,
        AppState $appState
    )
    {
        $this->indexRepository = $indexRepository;
        $this->config = $config;
        $this->appState = $appState;

        parent::__construct();
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mulwi:sync')
            ->setDescription('Synchronize searchable content')
            ->setDefinition([]);

        $this->addArgument('index');

        $this->addOption('list');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('global');
        } catch (\Exception $e) {

        }

        if (!$this->isValid($output)) {
            return;
        }

        $indexes = $this->indexRepository->getIndexes();

        if ($input->getOption('list')) {
            foreach ($indexes as $index) {
                $output->writeln($index->getIdentifier());
            }
            return;
        }


        $bar = new ProgressBar($output, count($indexes));
        $bar->start();

        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        $t = microtime(true);
        foreach ($indexes as $index) {
            if ($input->getArgument('index') && $input->getArgument('index') != $index->getIdentifier()) {
                continue;
            }

            $bar->setMessage($index->getIdentifier() . '...');
            $bar->display();

            $index->reindexAll();

            $bar->advance();
        }
        $time = round(microtime(true) - $t);

        $bar->finish();
        $bar->clear();

        $output->writeln("Took $time sec");
        $output->writeln("<info>Synchronization was completed</info>");
    }

    private function isValid(OutputInterface $output)
    {
        $valid = true;
        if (!$this->config->getApplicationID()) {
            $output->writeln("<error>Application ID is not set</error>");
            $valid = false;
        }

        if (!$this->config->getApplicationKey()) {
            $output->writeln("<error>Application Key is not set</error>");
            $valid = false;
        }

        return $valid;
    }
}
