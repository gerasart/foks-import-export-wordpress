<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

declare(strict_types=1);

namespace Foks;

class Settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'settingPage'], 12);
        add_filter('plugin_action_links_' . FOKS_BASENAME, [$this, 'plugin_action_links']);
    }

    /**
     * @return void
     */
    public function settingPage(): void
    {
        add_menu_page(
            'FOKS Import/Export',
            'FOKS Import/Export',
            'edit_posts',
            FOKS_NAME,
            [$this, FOKS_NAME],
            FOKS_URL . '/inc/img/icon.png'
        );
    }

    /**
     * @return void
     */
    public function foks(): void
    {
        include FOKS_PATH . 'views/index.php';
    }

    /**
     * @param $links
     * @return mixed
     */
    public function plugin_action_links($links)
    {
        $settings_link = '<a href="' . menu_page_url(FOKS_NAME, false) . '">' . esc_html(__('Settings', 'custom')) . '</a>';
        array_unshift($links, $settings_link);

        return $links;
    }
}
