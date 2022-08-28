<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Console;

use Foks\Import\Import;
use Foks\Import\ImportAttributes;
use Foks\Log\Logger;
use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AttributesCommand extends Command
{
    public const COMMAND_NAME = 'import-attributes';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Import attributes')
            ->setHelp('Import attributes for variation products');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start');

        LogResourceModel::set([
            'action' => 'cli',
            'message' => 'Cli execute command: ' . self::COMMAND_NAME,
        ]);

        $isFile = file_exists(Import::IMPORT_PATH);

        if (!$isFile) {
            $file = get_option(Settings::IMG_FIELD);
            $xml = file_get_contents($file);
            Logger::file($xml, Import::IMPORT_FILE, 'xml');
        }

        ImportAttributes::execute(Import::IMPORT_PATH);

        $output->writeln('Complete');

        return 1;
    }
}
