<?php

/**
 * Website application directory
 */
$application = 'app';

/**
 * Green system directory
 */
$system = 'system';

define('EXT', '.php');
define('DS', DIRECTORY_SEPARATOR);

// Define application and system paths
define('APPPATH',  dirname(__FILE__).DS.$application.DS);
define('SYSPATH', dirname(__FILE__).DS.$system.DS);

//  Inititalize
require SYSPATH.'/core/Bootstrap'.EXT;