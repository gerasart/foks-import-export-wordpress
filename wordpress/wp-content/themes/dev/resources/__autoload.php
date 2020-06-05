<?php

//namespace Theme;

//======================================================================================================================
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//======================================================================================================================

use Theme\SetupTheme;
SetupTheme::init();

$path = dirname(get_template_directory()) . '/';
use HaydenPierce\ClassFinder\ClassFinder;
ClassFinder::setAppRoot($path);

$classes = ClassFinder::getClassesInNamespace('Theme');
foreach($classes as $class) {
    new $class();
}

require_once '__autoload.php';