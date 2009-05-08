<?php
require SYSPATH.'/helpers/Folder'.EXT;
/**
 * Bootstrap File
 */

/**
 * Getting all config files,
 * check application directory first then the system directory.
 */
$config_dir = 'config/';
$files = Folder::getFiles(SYSPATH.$config_dir);

foreach($files as $file):
	$ext = end(explode('.', $file));
	if($ext === 'php'):
	    if(file_exists(APPPATH.$config_dir.$file)):
			include APPPATH.$config_dir.$file;
	    ; else :
			include SYSPATH.$config_dir.$file;
	    endif;
	endif;
endforeach;

// Set default controller and action
define('DEFAULT_CONTROLLER', $route['_default']);
define('DEFAULT_ACTION', 'index');

// Load core files
require SYSPATH.'/core/Model'.EXT;
require SYSPATH.'/core/View'.EXT;
require SYSPATH.'/core/Controller'.EXT;
require SYSPATH.'/helpers/Inflector'.EXT;
require SYSPATH.'/core/Benchmark'.EXT;
require SYSPATH.'/core/Observer'.EXT;
require SYSPATH.'/core/Autoloader'.EXT;
require SYSPATH.'/core/Simplengine'.EXT;

Benchmark::start('total');

// Set view suffix
define('VIEW_SUFFIX', $config['view_suffix']);

// Set base url
define('BASE_URL',  $config['base_url']);

if(isset($config[$db_group]['host'])){
	// Database setup
	define('DB_DSN',  $db_driver . ':dbname='. $config[$db_group]['database'] .';host=' . $config[$db_group]['host']);
	define('DB_USER', $config[$db_group]['username']);
	define('DB_PASS', $config[$db_group]['password']);
	
	define('TABLE_PREFIX', $config[$db_group]['prefix']);
	
	define('USE_PDO', $config[$db_group]['pdo']);
	
	if (USE_PDO)
	{
	    try 
		{
	        $__SE_CONN__ = new PDO(DB_DSN, DB_USER, DB_PASS);
		} 
		catch (PDOException $error) 
		{
	        die('DB Connection failed: '.$error->getMessage());
		}
	    
	    if ($__SE_CONN__->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql')
	        $__SE_CONN__->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	}
	else
	{
	    require_once SYSPATH . '/libraries/DoLite.php';
	    $__SE_CONN__ = new DoLite(DB_DSN, DB_USER, DB_PASS);
	}
	
	Model::connection($__SE_CONN__);
	Model::getConnection()->exec("set names 'utf8'");
}

// Set debugger
define('DEBUG', $config['debug']);

/**
 * Get all routes and process them
 */
if(isset($route)):
    foreach($route as $key => $value):
		SE::addRoute($key, $value);
    endforeach;
endif;

// Dispatch Simplengine
SE::dispatch();
