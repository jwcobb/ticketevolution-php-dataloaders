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
 * Make sure the Zend Framework library is in your include_path
 * You may need to adjust this.
 */
set_include_path (get_include_path() . PATH_SEPARATOR . '../library');


/**
 * Set up autoloading
 */
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('TicketEvolution\\');
$autoloader->setFallbackAutoloader(true);


/**
 * Set your Ticket Evolution API information.
 * This is available from your account under Brokerage->API Keys
 *
 * NOTE: These are exclusive to your company and should NEVER be shared with
 *       anyone else. These should be protected just like your bank password.
 *
 * @link http://exchange.ticketevolution.com/brokerage/credentials
 */
$dlConfig['params']['apiToken']                 = (string) 'YOUR_API_TOKEN_HERE';
$dlConfig['params']['secretKey']                = (string) 'YOUR_SECRET_KEY_HERE';
$dlConfig['params']['buyerId']                  = (string) 'YOUR_OFFICEID_HERE';
$dlConfig['params']['apiVersion']               = (string) '9';
$dlConfig['params']['usePersistentConnections'] = (bool) true;

//$cfg['params']['baseUri']                       = (string) 'https://api.sandbox.ticketevolution.com'; // Sandbox
$dlConfig['params']['baseUri']                  = (string) 'https://api.ticketevolution.com'; // Production



/**
 * Database setup
 * Make sure you have created the database using the script
 * provided in scripts/create_tables.mysql
 * as well as applying any updates in chronological order
 *
 */
$dlConfig['database']['adapter']                = 'Mysqli';
$dlConfig['database']['params']['host']         = 'YOUR_MYSQL_HOST';
$dlConfig['database']['params']['dbname']       = 'YOUR_DATABASE_NAME';
$dlConfig['database']['params']['username']     = 'YOUR_DATABASE_USER';
$dlConfig['database']['params']['password']     = 'YOUR_DATABASE_PASSWORD';


/**
 * LOCALE SETTINGS
 * If this isn't set in your php.ini set it here.
 * @link http://www.php.net/manual/en/timezones.america.php
 */
//date_default_timezone_set('America/Phoenix');
Zend_Locale::setDefault('en_US');


