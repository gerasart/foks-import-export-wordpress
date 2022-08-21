<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks;

use Foks\Model\ViewModel\SettingsView;
use Foks\Export\Export;
use Foks\Log\Logger;

ini_set('memory_limit', '1024M');

class Setup
{
    public function __construct()
    {
        if (FOKS_PAGE === 'page=' . FOKS_NAME) {
            Logger::file(0, 'total', 'json');
            Logger::file(0, 'current', 'json');

            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
            add_action('admin_head', [SettingsView::class, 'localAdminVars']);
            add_action('init', [$this, 'init']);
        }

        add_action('rest_api_init', [Export::class, 'getGenerateXml']);
    }

    /**
     * @return void
     */
    public function init(): void
    {
        SettingsView::viewData();
    }

    /**
     * @return void
     */
    public function enqueue_admin(): void
    {
        wp_enqueue_script(FOKS_NAME, FOKS_URL . 'inc/frontend/dist/app.js', ['jquery'], time(), true);
        wp_enqueue_style(FOKS_NAME, FOKS_URL . 'inc/frontend/dist/index.css');
    }
}
