<?php

namespace WpConfig;
use Exception;

/**
 * 1st - Load default config
 */
require(__DIR__ . '/wp-config.default.php');

/**
 * 2st - Load ENV config
 */
new EnvLoader();

/**
 * 3st - class WP_env
 */
class EnvLoader {

    private $env;
    private $hostname;

    public function __construct() {
        $this->getEnv();
        $this->setEnvVar();
        $this->setEnv_WP_CLI();
        $this->get_hostname();
        $this->set_define_Env();
        $this->Define_WPsite_URLs();
        $this->generate_define();
    }

    /**
     * @return array
     */
    public function getEnv() {
        $envPath = dirname(__FILE__).'/wp-config.env.php';
        $this->env = include($envPath);
        return (array) $this->env;
    }

    /**
     * Set env if set via environment variable
     */
    private function setEnvVar() {
        if (getenv('WP_ENV') !== false) {
            define('WP_ENV', preg_replace('/[^a-z]/', '', getenv('WP_ENV')));
        }
    }

    /**
     * Set env via --env=<environment> argument if running via WP-CLI
     */
    public function setEnv_WP_CLI() {
        if (PHP_SAPI == "cli" && defined('WP_CLI_ROOT')) {
            foreach (isset($argv) ? $argv : [] as $arg) {
                if (preg_match('/--env=(.+)/', $arg, $m)) {
                    define('WP_ENV', $m[1]);
                    break;
                }
            }
            // Also support via .env file in config directory
            if (!defined('WP_ENV')) {
                if (file_exists(__DIR__ . '/.env')) {
                    $environment = trim(file_get_contents(__DIR__ . '/.env'));
                    define('WP_ENV', preg_replace('/[^a-z]/', '', $environment));
                }
            }
        }
    }

    /**
     * Define site host
     */
    private function get_hostname() {
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $hostname = strtolower(filter_var($_SERVER['HTTP_X_FORWARDED_HOST'], FILTER_SANITIZE_STRING));
        } else {
            $hostname = strtolower(filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING));
        }
        if (!defined('WP_ENV') && empty($hostname)) {
            throw new Exception("Невозможно определить текущую среду через WP_ENV или имя хоста");
        }
        return (string) $this->hostname = $hostname;
    }

    /**
     * @throws Exception
     * @internal param mixed $env
     */
    private function set_define_Env() {
        foreach ($this->env as $environment => $env_vars) {
            if (!isset($env_vars['domain'])) {
                throw new Exception('You must set the domain value in your environment array, see wp-config.env.php');
            }
            $domain = $env_vars['domain'];
            if (!is_array($domain)) {
                $domain = [$domain];
            }
            foreach ($domain as $domain_name) {
                $wildcard = (strpos($domain_name, '*') !== false) ? true : false;
                if ($wildcard) {
                    $match = '/' . str_replace('\*', '([^.]+)', preg_quote($domain_name, '/')) . '/';
                    if (preg_match($match, $this->hostname, $m)) {
                        if (!defined('WP_ENV')) {
                            define('WP_ENV', $environment);
                        }
                        define('WP_ENV_DOMAIN', str_replace('*.', $m[1], $domain_name));
                        if (isset($env_vars['ssl'])) {
                            define('WP_ENV_SSL', (bool) $env_vars['ssl']);
                        }
                        if (isset($env_vars['path'])) {
                            define('WP_ENV_PATH', trim($env_vars['path'], '/'));
                        }
                        break;
                    }
                } elseif ($this->hostname === $domain_name) {
                    if (!defined('WP_ENV')) {
                        define('WP_ENV', $environment);
                    }
                    define('WP_ENV_DOMAIN', $domain_name);
                    if (isset($env_vars['ssl'])) {
                        define('WP_ENV_SSL', (bool) $env_vars['ssl']);
                    }
                    if (isset($env_vars['path'])) {
                        define('WP_ENV_PATH', trim($env_vars['path'], '/'));
                    }
                    break;
                }
            }
        }
    }

    /**
     * Define WordPress Site URLs
     */
    private function Define_WPsite_URLs() {

        $hostname = preg_replace('/^www\./', '', $this->hostname);

        if (!defined('WP_ENV_DOMAIN')) {
            throw new Exception("Cannot determine current environment domain, make sure this is set in wp-config.env.php");
        }
        if (!defined('WP_ENV_SSL')) {
            define('WP_ENV_SSL', false);
        }
        if (WP_ENV_SSL && (!defined('FORCE_SSL_ADMIN'))) {
            define('FORCE_SSL_ADMIN', true);
        }
        $protocol = (WP_ENV_SSL) ? 'https://' : 'http://';
        $path = (defined('WP_ENV_PATH')) ? '/' . trim(WP_ENV_PATH, '/') : '';

        if (!defined('WP_SITEURL')) {
            define('WP_SITEURL', $protocol . trim($hostname, '/') . $path);
        }
        if (!defined('WP_HOME')) {
            define('WP_HOME', $protocol . trim($hostname, '/') . $path);
        }

        // Define W3 Total Cache hostname
        if (defined('WP_CACHE')) {
            @define('COOKIE_DOMAIN', $hostname);
        }
    }

    private function generate_define() {
        foreach ($this->env[WP_ENV]['define'] as $define => $val) {
            define("{$define}", "{$val}");
        }
    }
}
