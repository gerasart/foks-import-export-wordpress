<?php
/**
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks;

use Foks\Traits\LocalVars;
use Foks\Export\Export;
use Foks\Log\Logger;

ini_set('memory_limit', '1024M');

class Setup
{
    use LocalVars;

    public static $admin_vars = [];

    public function __construct()
    {
        if (FOKS_PAGE === 'page=' . FOKS_NAME) {

            Logger::file(0, 'total', 'json');

            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin'));
            add_action('admin_head', array(__CLASS__, 'localAdminVars'));
            add_action('init', [$this, 'init']);
        }
        add_action('rest_api_init', array(Export::class, 'getGenerateXml'));

    }

    public function init(): void
    {
        $this->viewData();
    }

    public function enqueue_admin(): void
    {
        wp_enqueue_script(FOKS_NAME, FOKS_URL . 'inc/metasync-app/dist/app.js', array('jquery'), time(), true);
        wp_enqueue_style(FOKS_NAME, FOKS_URL . 'inc/metasync-app/dist/index.css');
    }
}
