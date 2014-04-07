<?php

/**
 * LICENSE
 *
 * This source file is subject to the new BSD (3-Clause) License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://choosealicense.com/licenses/bsd-3-clause/
 *
 * @copyright   Copyright (c) 2013 J Cobb (http://jcobb.org)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */

/**
* Checks if timezone set in php.ini
* If not set default is Central Standard Time
*/
if(ini_get('date.timezone') == ''){
    date_default_timezone_set('America/Chicago');
}

/**
 * Display errors
 */
error_reporting (E_ALL);


/**
 * Increase the max_execution_time because some of these can take a while to run
 */
ini_set('max_execution_time', 2400);


/**
 * Use Composer’s autoloader.
 */
require_once '../vendor/autoload.php';


/**
 * Get the configuration
 * Be sure to copy config.sample.php to config.php and enter your own information.
 */
(include_once 'config.php')
    OR die ('You need to copy /application/config.sample.php to /application/config.php and enter your own API credentials');


/**
 * Put the config data into registry
 */
$config = new \Zend_Config($dlConfig, true);
$registry = \Zend_Registry::getInstance();
$registry->set('config', $config);


/**
 * Set up the Db adapter
 */
$regConfig = $registry->get('config');
$dbConfig = $regConfig->database;
$db = \Zend_Db::factory($dbConfig);
\Zend_Db_Table::setDefaultAdapter($db);
