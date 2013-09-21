<?php

/**
 * LICENSE
 *
 * This source file is subject to the new BSD (3-Clause) License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://choosealicense.com/licenses/bsd-3-clause/
 *
 * @category    TicketEvolution
 * @package     TicketEvolution\Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2013 J Cobb. (http://jcobb.org)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */


namespace DataLoader\Db\Table;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2013 J Cobb. (http://jcobb.org)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */
class Venues extends AbstractTable
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $_name   = 'tevoVenues';

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key
     * as a string.
     *
     * @var mixed
     */
    protected $_primary   = 'venueId';

    /**
     * The column that we use to indicate status in boolean form
     *
     * @var string
     */
    protected $_statusColumn   = 'venuesStatus';

    /**
     * Classname for row
     *
     * @var string
     */
    //protected $_rowClass = 'DataLoader\Db\Table\Row';

    /**
     * Sets where default column values should be taken from
     *
     * @var string
     */
    protected $_defaultSource = self::DEFAULT_DB;

    /**
     * Simple array of class names of tables that are "children" of the current
     * table, in other words tables that contain a foreign key to this one.
     * Array elements are not table names; they are class names of classes that
     * extend Zend_Db_Table_Abstract.
     *
     * @var array
     */
    protected $_dependentTables = array(
        'DataLoader\Db\Table\Configurations',
        'DataLoader\Db\Table\Events',
        'DataLoader\Db\Table\Performers',
    );


    /**
     * Associative array map of declarative referential integrity rules.
     * This array has one entry per foreign key in the current table.
     * Each key is a mnemonic name for one reference rule.
     *
     * Each value is also an associative array, with the following keys:
     * - columns       = array of names of column(s) in the child table.
     * - refTableClass = class name of the parent table.
     * - refColumns    = array of names of column(s) in the parent table,
     *                   in the same order as those in the 'columns' entry.
     * - onDelete      = "cascade" means that a delete in the parent table also
     *                   causes a delete of referencing rows in the child table.
     * - onUpdate      = "cascade" means that an update of primary key values in
     *                   the parent table also causes an update of referencing
     *                   rows in the child table.
     *
     * @var array
     */
    protected $_referenceMap    = array();

}
