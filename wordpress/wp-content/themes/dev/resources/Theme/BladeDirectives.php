<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 31.10.18
 * Time: 16:18
 */

namespace Theme;

use function App\sage;

class BladeDirectives {

    public function __construct() {
        self::init();
    }

    public static function init() {
        add_action('after_setup_theme', array(__CLASS__, 'register'));
    }

    public static function register() {
        $default = ['init', 'register', '__construct'];

        $methods = get_class_methods(__CLASS__);
        foreach ($methods as $method) {
            if ( !in_array($method, $default) ) {
                self::$method();
            }
        }
    }


    /**
     * Create @hierarchy() Blade directive
     */
    private static function hierarchy() {
        sage('blade')->compiler()->directive('hierarchy', function () {
            return "<?= Theme\Help::hierarchy(); ?>";
        });
    }

    private static function translate() {
        sage('blade')->compiler()->directive('translate', function ($slug) {
            return "<?= Theme\OptionPageAcf::translate($slug); ?>";
        });
    }

    private static function thePost() {
        sage('blade')->compiler()->directive('the_post', function () {
            return "<?php the_post(); ?>";
        });
    }

    private static function reset() {
        sage('blade')->compiler()->directive('reset_postdata', function () {
//            wp_reset_postdata();
//            wp_reset_query();

            return "<?php wp_reset_postdata(); ?>";
        });
    }

    private static function image() {
        sage('blade')->compiler()->directive('image', function ($image) {
            return "<?= Theme\Help::getImageElement({$image}); ?>";
        });
    }

    private static function content() {
        sage('blade')->compiler()->directive('content', function ($post_id) {
            return "<?= Theme\Help::getContent({$post_id}); ?>";
        });
    }

    public static function thumbnail() {
        sage('blade')->compiler()->directive('thumbnail', function ($post_id) {
            return "<img src='<?= Theme\Help::getThumbnail({$post_id}) ?>' alt='<?= get_the_title({$post_id}) ?>' />";
        });
    }

    public static function icon() {
        sage('blade')->compiler()->directive('icon', function ($name) {
            return "<?= Theme\Help::getIcon({$name}); ?>";
        });
    }

}
