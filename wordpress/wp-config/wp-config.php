<?php

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Load the Studio 24 WordPress Multi-Environment Config. */
require_once(ABSPATH . 'wp-config/EnvLoader.php');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define('FS_METHOD', 'direct');
//define('SCRIPT_DEBUG', 1);
/*
UPDATE rvrstn_options SET option_value = replace(option_value, 'http://riverstone.msmc.com.ua', 'http://wp.docker.localhost:8000') WHERE option_name = 'home' OR option_name = 'siteurl';
UPDATE rvrstn_posts SET guid = replace(guid, 'http://riverstone.msmc.com.ua','http://wp.docker.localhost:8000');
UPDATE rvrstn_posts SET post_content = replace(post_content, 'http://riverstone.msmc.com.ua', 'http://wp.docker.localhost:8000');
*/
