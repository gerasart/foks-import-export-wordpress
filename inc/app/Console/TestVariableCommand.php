<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestVariableCommand extends Command
{
    public const COMMAND_NAME = 'test-variable';

    /**
     * @return void
     */
    protected function configure() : void
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('test variable')
            ->setHelp('test variable from foks');
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
        $this->createSample();
        $output->writeln('Complete');

        return 1;
    }

    private function createSample(): void
    {
        // Creating a variable product
        $product = new \WC_Product_Variable();

// Name and image would be enough
        $product->set_name('Wizard Hat22');
        $product->set_image_id(90);

// one available for variation attribute
        $attribute = new \WC_Product_Attribute();
        $attribute->set_name('Кухниvfdfd fsdfds');
        $attribute->set_options(array('60% полиамид, 30% эластан, 10% полиэстер'));
        $attribute->set_position(0);
        $attribute->set_visible(true);
        $attribute->set_variation(false); // here it is

        $attribute2 = new \WC_Product_Attribute();
        $attribute2->set_name('magical');
        $attribute2->set_options(array('No'));
        $attribute2->set_position(1);
        $attribute2->set_visible(true);
        $attribute2->set_variation(true); // here it is

        $attribute3 = new \WC_Product_Attribute();
        $attribute3->set_name('magical');
        $attribute3->set_options(array('Yes'));
        $attribute3->set_position(2);
        $attribute3->set_visible(true);
        $attribute3->set_variation(true); // here it is

        $product->set_attributes(array($attribute, $attribute2, $attribute3));

        $product->save();

// now we need two variations for Magical and Non-magical Wizard hat
        $variation = new \WC_Product_Variation();
        $variation->set_parent_id($product->get_id());
        $variation->set_attributes(array('magical' => 'Yes'));
        $variation->set_regular_price(1000000); // yep, magic hat is quite expensive
        $variation->save();

        $variation = new \WC_Product_Variation();
        $variation->set_parent_id($product->get_id());
        $variation->set_attributes(array('magical' => 'No'));
        $variation->set_regular_price(500);
        $variation->save();
    }
}
