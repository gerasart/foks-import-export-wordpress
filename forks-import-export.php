<?php
/*
 * Plugin Name: FoksImportExport
 * Version: 3.0.0
 * Plugin URI: https://metasync.site
 * Description: Import Export integration.
 * Author: Gerasart
 * Author URI: https://t.me/gerasart
 */

if (!defined('ABSPATH')) {
    exit;
}

const FOKS_VERSION = '3.0.0';
const FOKS_NAME = 'foks';

/**
 * Git settings for update.
 */
const GIT_SLAG = 'forks-import-export';
const GIT_BRANCH = 'master';

define('FOKS_BASENAME', plugin_basename(__FILE__));
define('FOKS_PATH', plugin_dir_path(__FILE__));
define('FOKS_URL', plugin_dir_url(__FILE__));
define('FOKS_PAGE', $_SERVER['QUERY_STRING']);
define('FOKS_DIR_IMAGE', wp_get_upload_dir());
require_once __DIR__ . '/vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

class ForksImportExport
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        try {
            self::cc_autoload();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private static function cc_autoload(): void
    {
        $namespaces = self::getDefinedNamespaces();

        foreach ($namespaces as $namespace => $path) {
            $clear = substr($namespace, 0, -1);
            ClassFinder::setAppRoot(FOKS_PATH);
            $level = error_reporting(E_ERROR);
            $classes = ClassFinder::getClassesInNamespace($clear);
            error_reporting($level);

            foreach ($classes as $class) {
                new $class();
            }
        }
    }

    /**
     * @return array
     */
    private static function getDefinedNamespaces(): array
    {
        $composerJsonPath = __DIR__ . '/composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));
        $psr4 = "psr-4";

        return (array)$composerConfig->autoload->$psr4;
    }
}

new ForksImportExport();


require 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';


$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/gerasart/foksImportExport/',
    __FILE__,
    GIT_SLAG
);

$myUpdateChecker->setBranch(GIT_BRANCH);
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
