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


namespace TicketEvolution;


/**
 * The bootstrap pulls in necessary files and sets up the autoloading
 */
require_once '../application/bootstrap.php';
require_once '../application/dataloaders-common.php';

$pageTitle = 'Configurations | Active | DataLoaders for use with the Ticket Evolution PHP Library';

require_once '../application/header.phtml';

$webService = new Webservice($registry->config->params);

$dataLoader = new DataLoader\Configurations(
    $webService,
    $options
);

$dataLoader->loadAllData();


require_once '../application/footer.phtml';
