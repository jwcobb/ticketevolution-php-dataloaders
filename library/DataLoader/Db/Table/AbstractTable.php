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
use DataLoader\Db\Table\AbstractTable;
use DateTime;


/**
 * @category    TicketEvolution
 * @package     TicketEvolution\Db
 * @subpackage  Table
 * @copyright   Copyright (c) 2013 J Cobb. (http://jcobb.org)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */
class AbstractTable extends \Zend_Db_Table_Abstract
{
    /**
     * The column that we use to indicate status in boolean form
     *
     * @var string
     */
    protected $_statusColumn   = 'status';

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'DataLoader\Db\Table\Row';

    /**
     * Returns the name of the column we are using to track status
     *
     * @return string
     */
    public function getStatusColumn()
    {
        // If a _statusColumn is explicitly set the return the column name
        if (isset($this->_statusColumn)) {
            return $this->_statusColumn;
        }

        // If _statusColumn is not set find a column with 'status' in the name
        foreach ($this->_getCols() as $column) {
            if (stripos($column, 'status') !== false) {
                return $column;
            }
        }

        return false;
    }


    /**
     * Run trim() on all the values of $data
     *
     * @param array $data
     * @return void
     */
    protected function _trimAllFields(array &$data)
    {
        array_map('trim', $data);
    }


    /**
     * Uses the table metadata to see which fields are NULLable and if the value
     * for that field is currently empty it will change it from an
     * empty string '' to NULL
     *
     * @param array $data
     * @return void
     */
    protected function _setEmptyFieldsToNull(array &$data)
    {
        array_walk($data, array('DataLoader\Db\Table\AbstractTable', '_emptyFieldsToNull'));
    }


    /**
     * Used as the callback function for _setEmptyFieldsToNull()
     *
     * @param mixed $field
     * @param string $key
     * @return void
     */
    protected function _emptyFieldsToNull(&$field, $key)
    {
        if ($this->_metadata[$key]['NULLABLE'] && empty($field)) {
            $field = null;
        }
    }


    /**
     * Override the default insert() method to ensure certain data integrity
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data)
    {
        $this->_trimAllFields($data);
        $this->_setEmptyFieldsToNull($data);

        // Make sure we don't try and set an empty createdDate
        // We don't need have to check if it exists because MySQL will add it
        // automatically but we will for consistency
        if (!isset($data['createdDate']) || empty($data['createdDate'])) {
            $data['createdDate'] = date('c');
        }

        // Make sure we have a lastModifiedDate
        if (!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }

        return parent::insert($data);
    }


    /**
     * Override the default update() method to ensure certain data integrity
     *
     * @param  array        $data  Column-value pairs.
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int          The number of rows updated.
     */
    public function update(array $data, $where)
    {
        $this->_trimAllFields($data);

        // Make sure we don't mess with createdDate
        if (isset($data['createdDate'])) {
            unset($data['createdDate']);
        }
        // Make sure we have a lastModifiedDate
        if (!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }

        return parent::update($data, $where);
    }


    /**
     * Override the default delete() because we never delete, we just change status
     */
    public function delete($where)
    {
        $data = array();
        $data[$this->_statusColumn] = 0;

        // Make sure we have a lastModifiedDate
        if (!isset($data['lastModifiedDate']) || empty($data['lastModifiedDate'])) {
            $data['lastModifiedDate'] = date('c');
        }

        return parent::update($data, $where);
    }

    /**
     * Get results via an array of parameters
     *
     * @param  mixed $params Options to use for the search query or a `uid`
     * @throws DataLoader\Db\Table\Exception
     * @return mixed
     */
    public function getByParameters($params, $limit=null, $orderBy=null)
    {
        if (!is_array($params) && !is_array($this->_primary)) {
            // Assume this is a single Id and find it
            $row = $this->find((int)$params);
            if (isset($row[0])) {
                return $row[0];
            } else {
                return false;
            }
        }

        // It appears that we have an array of search options
        $options = $this->_prepareOptions($params);

        $select = $this->select();
        foreach ($options as $column => $value) {
            // Some parameters may be like 'tevoPerformerId'
            // We need to change those to just 'performerId'
            $column = lcfirst(preg_replace('/^tevo(\w{1})/i', "$1", $column));
            if (is_array($value)) {
                $select->where($column ." IN (?)", $value);
            } elseif ($value instanceof DateTime) {
                $select->where($column ." = ?", $value->format('c'));
            } else {
                $select->where($column ." = ?", $value);
            }
        }

        if (!is_null($orderBy)) {
            if (is_array($orderBy)) {
                foreach ($orderBy as $order) {
                    $select->order($order);
                }
            } else {
                $select->order($orderBy);
            }
        }

        if (!is_null($limit)) {
            $select->limit($limit);
        }
        //dump($select->__toString());
        try{
            if ($limit == 1) {
                $results = $this->fetchRow($select);
            } else {
                $results = $this->fetchAll($select);
            }
        } catch(\Exception $e) {
            throw new namespace\Exception($e);
        }
        return $results;
    }


    /**
     * Prepare options for queries
     *
     * @param  array $params Parameters to use for the search query
     * @return array
     */
    protected function _prepareOptions(array $params)
    {
        // Verify that parameters are in an array.
        if (!is_array($params)) {
            throw new namespace\Exception('Query parameters must be in an array');
        }

        return $params;
    }
}
