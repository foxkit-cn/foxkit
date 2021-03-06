<?php

namespace Foxkit\Console\Commands;

use Foxkit\Application\Console\Command;
use Foxkit\Installer\Package\PackageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'uninstall';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Uninstalls a Foxkit package';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('packages', InputArgument::IS_ARRAY | InputArgument::REQUIRED, '[Package name]');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new PackageManager($output);
        $updater->uninstall((array) $this->argument('packages'));
    }
}
