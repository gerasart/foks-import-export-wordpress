<?php
/**
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

    protected function configure()
    {
        $this->setName('test-variable')
            ->setDescription('test variable')
            ->setHelp('test variable from foks');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start import products');

        // Creating a variable product
        $product = new \WC_Product_Variable();

// Name and image would be enough
        $product->set_name( 'Wizard Hat22' );
        $product->set_image_id( 90 );

// one available for variation attribute
        $attribute = new \WC_Product_Attribute();
        $attribute->set_name( 'Кухниvfdfd fsdfds' );
        $attribute->set_options( array( '60% полиамид, 30% эластан, 10% полиэстер' ) );
        $attribute->set_position( 0 );
        $attribute->set_visible( true );
        $attribute->set_variation( false ); // here it is

        $attribute2 = new \WC_Product_Attribute();
        $attribute2->set_name( 'Кухни23 fdsfd' );
        $attribute2->set_options( array( '60% полиамид, 30% эластан, 10% полиэстер' ) );
        $attribute2->set_position( 1 );
        $attribute2->set_visible( true );
        $attribute2->set_variation( false ); // here it is

        $attribute3 = new \WC_Product_Attribute();
        $attribute3->set_name( 'fdsfdsf' );
        $attribute3->set_options( array( '60% полиамид, 30% эластан, 10% полиэстер' ) );
        $attribute3->set_position( 2 );
        $attribute3->set_visible( true );
        $attribute3->set_variation( false ); // here it is

        $product->set_attributes( array( $attribute, $attribute2, $attribute3 ) );

        $product->save();

// now we need two variations for Magical and Non-magical Wizard hat
//        $variation = new \WC_Product_Variation();
//        $variation->set_parent_id( $product->get_id() );
//        $variation->set_attributes( array( 'magical' => 'Yes' ) );
//        $variation->set_regular_price( 1000000 ); // yep, magic hat is quite expensive
//        $variation->save();
//
//        $variation = new \WC_Product_Variation();
//        $variation->set_parent_id( $product->get_id() );
//        $variation->set_attributes( array( 'magical' => 'No' ) );
//        $variation->set_regular_price( 500 );

        $output->writeln('Complete');

        return 1;
    }
}
