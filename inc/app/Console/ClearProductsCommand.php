<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Console;

use Foks\Model\Woocommerce\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearProductsCommand extends Command
{
    protected function configure()
    {
        $this->setName('clear-products')
            ->setDescription('Clear products')
            ->setHelp('Remove all products from woocommerce');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start');

        Product::deleteProducts();

        $output->writeln('Complete');

        return 1;
    }
}
