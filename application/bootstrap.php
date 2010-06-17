<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Asia/Chongqing');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
//setlocale(LC_ALL, 'en_US.utf-8');
i18n::lang('zh-cn');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

Kohana::$environment = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')?Kohana::DEVELOPMENT:Kohana::PRODUCTION;

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => dirname($_SERVER['SCRIPT_NAME']).'/',
	'index_file' => FALSE,
	'profiling' => (Kohana::$environment == Kohana::DEVELOPMENT)?true:false,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth' => MODPATH.'auth',
	'cache' => MODPATH.'cache',
	'codebench' => MODPATH.'codebench',
	'database' => MODPATH.'database',
	'image' => MODPATH.'image',
	'orm' => MODPATH.'orm',
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));


$request = Request::instance();
 
try
{
	// Attempt to execute the response
	$request->execute();
}
catch (Exception $e)
{
	if (Kohana::$environment !== Kohana::PRODUCTION)
	{
		throw $e;
	}
	else
	{
		// Log the error
		Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));

		$request->response = View::factory('template')
			->set('title', $request->status)
			->set('content', View::factory('errors/'.$request->status));
	}
}
 
if ($request->response)
{
	// Get the total memory and execution time
	$total = array(
	'{memory_usage}' => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2).'KB',
	'{execution_time}' => number_format(microtime(TRUE) - KOHANA_START_TIME, 5).' seconds');
	 
	// Insert the totals into the response
	$request->response = strtr((string) $request->response, $total);
}
 
 
/**
* Display the request response.
*/
echo $request->send_headers()->response;
