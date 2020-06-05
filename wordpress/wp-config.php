<?php
define('WPCF7_AUTOP', false );

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Load the Studio 24 WordPress Multi-Environment Config. */
require_once(ABSPATH . 'wp-config/EnvLoader.php');

//define('SCRIPT_DEBUG', 1);

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define('FS_METHOD', 'direct');