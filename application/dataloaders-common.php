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
 * This file is just some code that was common to all the data-loaders
 */

/**
* Checks if timezone set in php.ini
* If not set default is Central Standard Time
*/
if(ini_get('date.timezone') == ''){
    date_default_timezone_set('America/Chicago');
}

/**
 * Set a $startTime variable to record when we started this script. This time
 * will be stored in the appropriate row of `dataLoaderStatus` so we know what
 * time to use the next time this script runs
 */
$startTime = new DateTime();


/**
 * Filter any input
 */
$filters = array(
    'lastRun' => array(
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'startPage' => array(
        'Int',
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'perPage' => array(
        'Int',
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'showMemory' => array(
        new \Zend_Filter_Boolean(array(
            'type'      => \Zend_Filter_Boolean::ALL,
        )),
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'showProgress' => array(
        new \Zend_Filter_Boolean(array(
            'type'      => \Zend_Filter_Boolean::ALL,
        )),
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
    'fullRefresh' => array(
        new \Zend_Filter_Boolean(array(
            'type'      => \Zend_Filter_Boolean::ALL,
        )),
        'StringTrim',
        'StripTags',
        'StripNewlines'
    ),
);
$validators = array(
    'lastRun' => array(
        'Date',
        'presence'      => 'optional',
        'allowEmpty'    => true,
        'default'       => null,
    ),
    'startPage' => array(
        'Int',
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => (int) 1,
    ),
    'perPage' => array(
        'Int',
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => (int) 100,
    ),
    'showMemory' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => false
    ),
    'showProgress' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => true
    ),
    'fullRefresh' => array(
        'presence'      => 'optional',
        'allowEmpty'    => false,
        'default'       => false
    ),
);
$GET = new \Zend_Filter_Input($filters, $validators, $_GET);

/**
 * Get the Zend_Config object from the registry
 */
$registry = \Zend_Registry::getInstance();


/**
 * Set the default options for the request(s)
 */
$options = array(
    'lastRun'           => $GET->lastRun,
    'startPage'         => $GET->startPage,
    'perPage'           => $GET->perPage,
    'showMemory'        => (bool) $GET->showMemory,
    'showProgress'      => (bool) $GET->showProgress,
);


/**
 * If a "fullRefresh" was specified, overwrite the $lastRun date with one old
 * enough to grab everything.
 */
if ($GET->fullRefresh) {
    $options['lastRun'] = '2010-01-01';
}
