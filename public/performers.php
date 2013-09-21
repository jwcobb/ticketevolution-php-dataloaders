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


namespace DataLoader;
use TicketEvolution\Webservice;


/**
 * The bootstrap pulls in necessary files and sets up the autoloading
 */
require_once '../application/bootstrap.php';
require_once '../application/dataloaders-common.php';

$pageTitle = 'Performers | Active | DataLoaders for use with the Ticket Evolution PHP Library';

require_once '../application/header.phtml';

$webService = new Webservice($registry->config->params);

$dataLoader = new DataLoader\Performers(
    $webService,
    $options
);

$dataLoader->loadAllData();


require_once '../application/footer.phtml';
