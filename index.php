<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

if (PHP_SAPI === 'cli-server') {
	$path = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
	$path = $path ? urldecode($path) : $path;
	$docroot = realpath(__DIR__);
	$file = $path ? realpath(__DIR__ . '/' . ltrim($path, '/')) : false;
	if ($docroot && $file && strpos($file, $docroot) === 0 && is_file($file)) {
		return false;
	}
}

// Set default timezone to Philippine Time (Asia/Manila, UTC+08:00)
date_default_timezone_set('Asia/Manila');

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */

    // Load .env file if it exists (with advanced parsing)
    if (file_exists(__DIR__ . '/.env')) {
        $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                // Remove quotes if present
                if ($value !== '' && 
                    (($value[0] === '"' && substr($value, -1) === '"') || 
                     ($value[0] === "'" && substr($value, -1) === "'"))) {
                    $value = substr($value, 1, -1);
                }
                
                // Now, check if there are nested variables in the value
                // This is to handle cases like: KEY="VALUE1 KEY2=VALUE2"
                $matches = [];
                preg_match_all('/([A-Z][A-Z0-9_]{2,})=/', $value, $matches, PREG_OFFSET_CAPTURE);
                if (!empty($matches[1])) {
                    $first_offset = (int)$matches[1][0][1];
                    if ($first_offset > 0) {
                        $main_value = substr($value, 0, $first_offset);
                        $main_value = trim($main_value);
                        if ($main_value !== '' && 
                            (($main_value[0] === '"' && substr($main_value, -1) === '"') || 
                             ($main_value[0] === "'" && substr($main_value, -1) === "'"))) {
                            $main_value = substr($main_value, 1, -1);
                        }
                        
                        // Set main variable
                        putenv(sprintf('%s=%s', $key, $main_value));
                        $_ENV[$key] = $main_value;
                        $_SERVER[$key] = $main_value;
                        
                        // Process nested variables
                        for ($i = 0; $i < count($matches[1]); $i++) {
                            $k2 = (string)$matches[1][$i][0];
                            $start = (int)$matches[1][$i][1];
                            $val_start = $start + strlen($k2) + 1;
                            $end = ($i + 1 < count($matches[1])) ? (int)$matches[1][$i + 1][1] : strlen($value);
                            $v2 = substr($value, $val_start, $end - $val_start);
                            $v2 = trim($v2);
                            if ($v2 !== '' && 
                                (($v2[0] === '"' && substr($v2, -1) === '"') || 
                                 ($v2[0] === "'" && substr($v2, -1) === "'"))) {
                                $v2 = substr($v2, 1, -1);
                            }
                            putenv(sprintf('%s=%s', $k2, $v2));
                            $_ENV[$k2] = $v2;
                            $_SERVER[$k2] = $v2;
                        }
                        continue;
                    }
                }
                
                // Default case - set the variable
                putenv(sprintf('%s=%s', $key, $value));
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '8.1', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}

/*
 *---------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" directory.
 * Set the path if it is not in the same directory as this file.
 */
	$system_path = 'system';
	if (!is_dir($system_path)) {
		if (is_dir(__DIR__ . '/system')) {
			$system_path = __DIR__ . '/system';
		} elseif (is_dir(__DIR__ . '/Instroom_System/system')) {
			$system_path = __DIR__ . '/Instroom_System/system';
		} elseif (is_dir(dirname(__DIR__) . '/system')) {
			$system_path = dirname(__DIR__) . '/system';
		}
	}

/*
 *---------------------------------------------------------------
 * APPLICATION DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * directory than the default one you can set its name here. The directory
 * can also be renamed or relocated anywhere on your server. If you do,
 * use an absolute (full) server path.
 * For more info please see the user guide:
 *
 * https://codeigniter.com/userguide3/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 */
	$application_folder = 'application';
	if (!is_dir($application_folder)) {
		if (is_dir(__DIR__ . '/application')) {
			$application_folder = __DIR__ . '/application';
		} elseif (is_dir(__DIR__ . '/Instroom_System/application')) {
			$application_folder = __DIR__ . '/Instroom_System/application';
		} elseif (is_dir(dirname(__DIR__) . '/application')) {
			$application_folder = dirname(__DIR__) . '/application';
		}
	}

/*
 *---------------------------------------------------------------
 * VIEW DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view directory out of the application
 * directory, set the path to it here. The directory can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application directory.
 * If you do move this, use an absolute (full) server path.
 *
 * NO TRAILING SLASH!
 */
	$view_folder = '';


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here. For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT: If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller. Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 */
	// The directory name, relative to the "controllers" directory.  Leave blank
	// if your controller is not in a sub-directory within the "controllers" one
	// $routing['directory'] = '';

	// The controller class file name.  Example:  mycontroller
	// $routing['controller'] = '';

	// The controller function you wish to be called.
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (($_temp = realpath($system_path)) !== FALSE)
	{
		$system_path = $_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		// Ensure there's a trailing slash
		$system_path = strtr(
			rtrim($system_path, '/\\'),
			'/\\',
			DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
		).DIRECTORY_SEPARATOR;
	}

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// Normalize all path variables to use forward slashes (fixes Windows/Linux backslash mismatches)
	$system_path = str_replace('\\', '/', $system_path);
	$application_folder = str_replace('\\', '/', $application_folder);

	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the system directory
	define('BASEPATH', rtrim($system_path, '/') . '/');

	// Path to the front controller (this file) directory
	define('FCPATH', str_replace('\\', '/', dirname(__FILE__)) . '/');

	// Name of the "system" directory
	define('SYSDIR', basename(BASEPATH));

	// The path to the "application" directory
	if (is_dir($application_folder))
	{
		if (($_temp = realpath($application_folder)) !== FALSE)
		{
			$application_folder = str_replace('\\', '/', $_temp);
		}
		else
		{
			$application_folder = rtrim($application_folder, '/');
		}
	}
	elseif (is_dir(BASEPATH.$application_folder.'/'))
	{
		$application_folder = rtrim(BASEPATH.$application_folder, '/');
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('APPPATH', rtrim($application_folder, '/') . '/');

	// The path to the "views" directory
	if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views/'))
	{
		$view_folder = APPPATH.'views';
	}
	elseif (is_dir($view_folder))
	{
		if (($_temp = realpath($view_folder)) !== FALSE)
		{
			$view_folder = str_replace('\\', '/', $_temp);
		}
		else
		{
			$view_folder = rtrim($view_folder, '/');
		}
	}
	elseif (is_dir(APPPATH.$view_folder.'/'))
	{
		$view_folder = rtrim(APPPATH.$view_folder, '/');
	}
	else
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
		exit(3); // EXIT_CONFIG
	}

	define('VIEWPATH', rtrim(str_replace('\\', '/', $view_folder), '/') . '/');

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 */
require_once BASEPATH.'core/CodeIgniter.php';
