<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Console;

use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Woocommerce\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearProductsCommand extends Command
{
    public const COMMAND_NAME = 'clear-products';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Clear products')
            ->setHelp('Remove all products from woocommerce');
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

        Product::deleteProducts();

        $output->writeln('Complete');

        return 1;
    }
}
