<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Console;

use Foks\Export\Export;
use Foks\Model\Resource\LogResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    public const COMMAND_NAME = 'export-products';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Export products')
            ->setHelp('Generate xml for export products');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start export');

        LogResourceModel::set([
            'action' => 'cli',
            'message' => 'Cli execute command: ' . self::COMMAND_NAME,
        ]);

        Export::generateXML();

        $output->writeln('Complete: ' . FOKS_URL . 'logs/foks_export.xml');

        return 1;
    }
}
