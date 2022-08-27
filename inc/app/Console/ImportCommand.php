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
use Foks\Log\Logger;
use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Settings;
use Foks\Model\Woocommerce\Category;
use Foks\Model\Woocommerce\Product;
use Foks\Model\Woocommerce\ProductVariation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    private const BATCH_SIZE = 1;
    public const COMMAND_NAME = 'import-products';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Import products')
            ->setHelp('Import products from foks');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start import products');

        LogResourceModel::set([
            'action' => 'cli',
            'message' => 'Cli execute command: ' . self::COMMAND_NAME,
        ]);

        $file = get_option(Settings::IMPORT_FIELD);

        if ($file) {
            $xml = file_get_contents($file);
            Logger::file($xml, Import::IMPORT_FILE, 'xml');
            $data = Import::parseFile(Import::IMPORT_PATH);
            $categories = Category::addCategories($data['categories']);
            $products = ProductVariation::prepareVariationProducts($data['products']);
            $progressBar = new ProgressBar($output, count($products));
            $progressBar->start();
            $dataBatch = array_chunk($products, self::BATCH_SIZE);

            foreach ($dataBatch as $index => $products) {
                Product::addProducts($products, $categories);
                $progressBar->setProgress($index);
            }

            $progressBar->finish();
        }

        $output->writeln('Complete.');

        return 1;
    }
}
