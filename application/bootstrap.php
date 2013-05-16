<?php

/**
 * DataLoaders for use with the Ticket Evolution PHP Library
 *
 * LICENSE
 *
 * This source file is subject to the BSD 3-Clause License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/TeamOneTickets/ticket-evolution-dataloaders/blob/master/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@teamonetickets.com so we can send you a copy immediately.
 *
 * @author      J Cobb <j@teamonetickets.com>
 * @copyright   Copyright (c) 2013 Team One Tickets & Sports Tours, Inc. (http://www.teamonetickets.com)
 * @license     https://github.com/TeamOneTickets/ticket-evolution-dataloaders/blob/master/LICENSE.txt     BSD 3-Clause License
 */


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
