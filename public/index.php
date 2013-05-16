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
 * The bootstrap pulls in necessary files and sets up the autoloading
 */
require_once '../application/bootstrap.php';

$pageTitle = 'DataLoaders for use with the Ticket Evolution PHP Library';

require_once '../application/header.phtml';

?>
            <div class="page-header">
                <h1>Ticket Evolution DataLoaders <small>for use with the Ticket Evolution PHP Library</small></h1>
            </div>
		    <p>These “DataLoader” scripts can be used to populate local database tables with a cache of the Ticket Evolution data. If you choose to do this then you should be sure to run each of these scripts at least daily. We suggest adding them to your <code>crontab</code> or <code>launchd</code> on your server.</p>

            <?php
                $table = new \TicketEvolution\Db\Table\DataLoaderStatus();

                $scripts = array(
                    'brokerages'        => array(
                        'active',
                    ),
                    'categories'        => array(
                        'active',
                        'deleted',
                    ),
                    'configurations'        => array(
                        'active',
                    ),
                    'events'        => array(
                        'active',
                        'deleted',
                    ),
                    'offices'        => array(
                        'active',
                    ),
                    'performers'        => array(
                        'active',
                        'deleted',
                    ),
                    'users'        => array(
                        'active',
                    ),
                    'venues'        => array(
                        'active',
                        'deleted',
                    ),
                );

                echo '<h2>Status of scripts run based upon <i>updated_at</i> date</h1>' . PHP_EOL
                   . '<table summary="Status of scripts run based upon updated_at date" class="table table-striped table-bordered table-hover">' . PHP_EOL
                   . '<thead>' . PHP_EOL
                   . '<tr>' . PHP_EOL
                   . '<th>Script</th>' . PHP_EOL
                   . '<th>Type</th>' . PHP_EOL
                   . '<th>Last Run</th>' . PHP_EOL
                   . '<th>Run Now</th>' . PHP_EOL
                   . '</tr>' . PHP_EOL
                   . '</thead>' . PHP_EOL

                   . '<tbody>' . PHP_EOL
                ;
                foreach ($scripts as $script => $types) {
                    // See if we have an entry in `tevoDataLoaderStatus` for this script

                    echo '<tr>' . PHP_EOL
                       . '<td rowspan="' . count($types) . '">' . ucwords($script) . '</td>' . PHP_EOL
                    ;
                    foreach ($types as $type) {
                        $row = $table->find($script, $type)->current();

                        $file = strtolower($script);
                        if ($type != 'active') {
                            $file .= '-' . $type;
                        }
                        $file .= '.php';

                        echo '<td>' . ucwords($type) . '</td>' . PHP_EOL
                           . '<td>'
                        ;
                        if (!empty($row)) {
                            $dateLastRun = new DateTime($row->lastRun);
                            echo '<span class="date">' . $dateLastRun->format(TicketEvolution\DateTime::DATE_FULL_US) . '</span> <span class="time">' . $dateLastRun->format('g:i:s a') . '</span>';
                        } else {
                            echo 'Not yet run';
                        }
                        echo '</td>' . PHP_EOL . PHP_EOL

                           . '<td>' . PHP_EOL
                           . '<div class="btn-group">' . PHP_EOL
                           . '<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-tasks icon-white"></span> Run Now <span class="caret"></span></a>' . PHP_EOL
                           . '<ul class="dropdown-menu">' . PHP_EOL
                           . '<li><a href="' . $file . '"><span class="icon-time"></span> Normal</a></li>' . PHP_EOL
                           . '<li><a href="' . $file . '?showMemory=true"><span class="icon-time"></span> Normal <span class="muted">w/Memory Usage</span></a></li>' . PHP_EOL
                           . '<li class="divider"></li>' . PHP_EOL
                           . '<li><a href="' . $file . '?fullRefresh=true"><span class="icon-repeat"></span> Full Refresh</a></li>' . PHP_EOL
                           . '<li><a href="' . $file . '?fullRefresh=true&amp;showMemory=true"><span class="icon-repeat"></span> Full Refresh <span class="muted">w/Memory Usage</span></a></li>' . PHP_EOL
                           . '</ul>' . PHP_EOL
                           . '</div>' . PHP_EOL
                           . '</td>' . PHP_EOL
                           . '</tr>' . PHP_EOL
                        ;

                        unset($row);
                    }
                }
                echo '</tbody>' . PHP_EOL
                   . '</table>' . PHP_EOL;

require_once '../application/footer.phtml';

            ?>
