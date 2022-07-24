<?php
declare(strict_types=1);

namespace Foks\Console;

use Foks\Import\Import;
use Foks\Log\Logger;
use Foks\Model\Category;
use Foks\Model\Product;
use Foks\Model\ProductVariation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */
class ImportCommand extends Command
{
    private const BATCH_SIZE = 1;

    protected function configure()
    {
        $this->setName('import-products')
            ->setDescription('Import products')
            ->setHelp('Import products from foks');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Start import products');

        $file = get_option('foks_import');

        if ($file) {
            $xml = file_get_contents($file);
            Logger::file($xml, 'foks_import', 'xml');
            $file_path = FOKS_URL . '/logs/foks_import.xml';
            $data = Import::parseFile($file_path);
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
    }
}
