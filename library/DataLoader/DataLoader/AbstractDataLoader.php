<?php

/**
 * LICENSE
 *
 * This source file is subject to the new BSD (3-Clause) License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://choosealicense.com/licenses/bsd-3-clause/
 *
 * @category    DataLoader
 * @package     DataLoader\DataLoader
 * @copyright   Copyright (c) 2013 J Cobb. (http://jcobb.org)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */


namespace DataLoader\DataLoader;
use TicketEvolution\Webservice;
use TicketEvolution\DateTime;
use DateTimeZone;
use Zend_Measure_Binary;
use Zend_Loader;


/**
 * Abstract to be used by all DataLoaders.
 *
 * @category    DataLoader
 * @package     DataLoader\DataLoader
 * @copyright   Copyright (c) 2013 J Cobb. (http://jcobb.org)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */
abstract class AbstractDataLoader
{
    /**
     * Set a variable to record when we started this script. This time
     * will be stored in the appropriate row of `dataLoaderStatus` so we know what
     * time to use the next time this script runs
     *
     * @var DateTime
     */
    protected $_startTime;


    /**
     * The class of the `dataLoaderStatus` table
     *
     * @var string
     */
    protected $_statusTableClass = '\DataLoader\Db\Table\DataLoaderStatus';


    /**
     * The `dataLoaderStatus` table object
     *
     * @var Zend_Db_Table
     */
    protected $_statusTable;


    /**
     * The `dataLoaderStatus` table row
     *
     * @var Zend_Db_Table_Row
     */
    protected $_statusTableRow;


    /**
     * The last time this dataloader was run
     *
     * @var DateTime
     */
    protected $_lastRun;


    /**
     * The page to start with when fetching API results
     *
     * @var int
     */
    protected $_startPage = 1;


    /**
     * The number of results to request per page from the API.
     * Maximum 100
     *
     * @var int
     */
    protected $_perPage = 100;


    /**
     * Set to true to have some memory statistics displayed during run
     *
     * @var bool
     */
    protected $_showMemory = false;


    /**
     * Set to true to have display progress during run
     *
     * @var bool
     */
    protected $_showProgress = true;


    /**
     * Reference to the data loader object
     *
     * @var DataLoader\DataLoader\Abstract
     */
    protected $_dataLoader;


    /**
     * Which endpoint we are hitting. This is used in the `dataLoaderStatus` table
     *
     * @var string
     */
    var $endpoint;


    /**
     * The state of items to get from the endpoint [active|deleted]
     *
     * @var string
     */
    var $endpointState;


    /**
     * The TicketEvolution\Webservice object
     *
     * @var DataLoader\Webservice
     */
    protected $_webService;


    /**
     * The TicketEvolution\Webservice method to use for the API request
     *
     * @var string
     */
    protected $_webServiceMethod;


    /**
     * The Zend_Db_Table subclass for the table to update
     *
     * @var Zend_Db_Table
     */
    var $tableClass;


    /**
     * Reference to the table object to be created from $tableClass
     *
     * @var Zend_Db_Table
     */
    protected $_table;


    /**
     * Reference to the Zend_Db_Table_Row for each item
     *
     * @var Zend_Db_Table_Row
     */
    protected $_tableRow;


    /**
     * Indicator for whether the save() will be an INSERT or UPDATE
     *
     * @var string [INSERT|UPDATE]
     */
    protected $_rowAction;


    /**
     * The total number of pages to process
     *
     * @var int
     */
    var $totalPages;


    /**
     * Constructor
     *
     * Supported params for $options are:-
     * * lastRun        = (string|DateTime) The datetime for updated_at or deleted_at
     * * startPage      = (int) which page to start the pagination loop
     * * perPage        = (int) how many items to fetch per page. Max 100 per API
     * * showProgress   = (bool) whether or not to output progress
     * * showMemory     = (bool) whether or not to output memory statistics
     *
     * @param TicketEvolution\Webservice $webService    TicketEvolution\Webservice object to use for API calls
     * @param array $options    Array of options
     */
    public function __construct(Webservice $webService, array $options=array())
    {
        // Set the startTime to now
        $this->_startTime = new DateTime();

        // Reference to the webService object
        $this->_webService = $webService;

        // Set the status row to store the completed progress
        $this->_setStatusRow();


        /**
         * Process the $options
         */
        if (!empty($options['startPage'])) {
            $this->_startPage = $options['startPage'];
        }

        if (!empty($options['perPage'])) {
            $this->_perPage = $options['perPage'];
        }

        if (isset($options['showMemory'])) {
            $this->_showMemory = (bool) $options['showMemory'];
        }

        if (isset($options['showProgress'])) {
            $this->_showProgress = (bool) $options['showProgress'];
        }

        $this->_originalOptions = $options;

        /**
         * Set the date we last ran this script so we can get only entries that have
         * been added/changed/deleted since then
         */
        if (!empty($options['lastRun'])) {
            if (!$options['lastRun'] instanceOf DateTime) {
                if (!$this->_lastRun = new DateTime($options['lastRun'])) {
                    throw new namespace\Exception('The $lastRun date you provided appears to be malformed');
                }
            } else {
                $this->_lastRun = $options['lastRun'];
            }
        } else {
            // The table should have either a previously set value
            // OR a default date of 2010-01-01 for the column
            $this->_lastRun = new DateTime($this->_statusRow->lastRun);
        }

        /**
         * Convert $_lastRun to UTC because the API currently ignores the time if it is
         * not specified as UTC. This is not expected behavior and should be fixed soon.
         */
        $this->_lastRun->setTimezone(new DateTimeZone('UTC'));


    }


    /**
     * Gets a row from $_statusTable to record the completion time.
     * If one doesn't exist, create an empty one.
     *
     * @return
     * @throws
     */
    protected function _setStatusRow()
    {
        /**
         * See if we have a lastRun stored in the `dataLoaderStatus` table
         */
        $statusRow = $this->_getStatusTable()->find($this->endpoint, $this->endpointState)->current();
        if (!empty($statusRow)) {
            $this->_statusRow = $statusRow;
        } else {
            // We didn't get a row from the table so create an empty one
            $this->_statusRow = $this->_getStatusTable()->createRow();
            $this->_statusRow->endpoint = $this->endpoint;
            $this->_statusRow->state = $this->endpointState;

        }
    }


    /**
     * Gets a row from $_statusTable to record the completion time.
     * If one doesn't exist, create an empty one.
     *
     * @return Zend_Db_Table
     */
    protected function _getStatusTable()
    {
        if ($this->_statusTable === null) {
            /**
             * Load the status table class.  This throws an exception
             * if the specified class cannot be loaded.
             */
            if (!class_exists($this->_statusTableClass)) {
                Zend_Loader::loadClass($this->_statusTableClass);
            }

            /**
             * Create an instance of the status table class.
             */
            $this->_statusTable = new $this->_statusTableClass();
        }

        return $this->_statusTable;
    }


    /**
     * Runs the data loader, looping through all pages and all results
     *
     * @return
     * @throws
     */
    public function loadAllData()
    {
        /**
         * Set the default options for the request(s)
         */
        $options = array(
            'page'              => $this->_startPage,
            'per_page'          => $this->_perPage,
            'updated_at.gte'    => $this->_lastRun->format('c'),
        );

        // "deleted" endpoints are more reliable using "deleted_at" instead of "updated_at"
        if ($this->endpointState == 'deleted') {
            $options['deleted_at.gte'] = $options['updated_at.gte'];
            unset($options['updated_at.gte']);
        }


        if ($this->_showProgress) {
            echo '<div class="page-header">' . PHP_EOL
               . '<h1>Updating <i>' . $this->endpointState . ' ' . $this->_statusRow->endpoint . '</i><br /><small> ' . $this->_perPage . ' at a time  with entries updated since ' . $this->_lastRun->format('r') . '</small></h1>' . PHP_EOL
               . '</div>' . PHP_EOL
               ;
        }


   		// Outer loop through pages of API results
        while ($this->totalPages === null || $options['page'] <= $this->totalPages) {
            $this->processResults($options, $this->_showProgress, $this->_showMemory);
            $options['page']++;
        }


        // Update `tevoDataLoaderStatus` with current info
        $this->_statusRow->lastRun = (string) $this->_startTime->format('c');

        try {
            $this->_statusRow->save();
        } catch (\Exception $e) {
            throw new namespace\Exception($e);
        }

        if ($this->_showProgress) {
            echo '<h2>Finished updating <i>' . $this->endpointState . ' ' . $this->_statusRow->endpoint . '</i></h2>' . PHP_EOL;
        }

        if ($this->_showMemory) {
            $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
            $peakMem = new Zend_Measure_Binary(memory_get_peak_usage(true), Zend_Measure_Binary::BYTE);
            echo '<p class="lead">Current memory usage at end of script: ' . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</p>' . PHP_EOL
               . '<p class="lead">PEAK memory usage: ' . $peakMem->convertTo(Zend_Measure_Binary::MEGABYTE) . '</p>' . PHP_EOL;
        }


    }


    /**
     * Perform the API call
     *
     * @param array $options Options for the API call
     * @return \TicketEvolution\Webservice\ResultSet
     */
    protected function _doApiCall(array $options)
    {
        try {
            return $this->_webService->{$this->_webServiceMethod}($options);


        } catch(\Exception $e) {
            throw new namespace\Exception($e);
        }
    }


    /**
     * The procedural part that gets executes an API call and loops through the
     * results, processing them and saving them to the database table.
     *
     * @return void
     */
    public function processResults($options, $showProgress=true, $showMemory=false)
    {
        $this->_showProgress = $showProgress;
        $this->_showMemory = $showMemory;

        /**
         * Perform the actual API call
         */
        $results = $this->_doApiCall($options);
        //var_dump($results);
        //var_dump($this->_perPage);
        //var_dump($this);

        //var_dump($results->totalPages());
        $this->totalPages = $results->totalPages();
        //var_dump($this->totalPages);


        if ($this->_showMemory) {
            $curMem = memory_get_usage(true);
            $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
            echo '<h4>Current memory usage after fetching page '
               . $options['page'] . ' of ' . $this->totalPages . ': '
               . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE)
               . '</h4>' . PHP_EOL;
        }

        foreach ($results as $result) {
            $this->_setRow($result);

            $this->_formatData($result);

            $this->_preSave($result);

            $this->_saveRow($result);

            $this->_postSave($result);

            $this->_rowAction = null;
            $this->_tableRow = null;
        }

        if ($this->_showMemory) {
            $curMem = new Zend_Measure_Binary(memory_get_usage(true), Zend_Measure_Binary::BYTE);
            echo '<h4>Current memory usage after database work of page '
               . $options['page'] . ' of ' . $this->totalPages . ': '
               . $curMem->convertTo(Zend_Measure_Binary::MEGABYTE)
               . '</h4>' . PHP_EOL;
        }

        if ($this->_showProgress) {
            echo '<h3>Done with page ' . $options['page'] . '</h3>' . PHP_EOL;

            @ob_end_flush();
            @ob_flush();
            @flush();
        }
    }


    /**
     *
     * @return
     * @throws
     */
    protected function _getTable()
    {
        if ($this->_table === null) {
            /**
             * Load the table class.  This throws an exception
             * if the specified class cannot be loaded.
             */
            if (!class_exists($this->_tableClass)) {
                Zend_Loader::loadClass($this->_tableClass);
            }

            /**
             * Create an instance of the status table class.
             */
            $this->_table = new $this->_tableClass();
        }

        return $this->_table;
    }


    /**
     * Attempts to fetch an existing row from the table for updating.
     * If one isn't found it creates an empty one.
     */
    protected function _setRow($result)
    {
        $row = $this->_getTable()->find($result->id)->current();
        if (empty($row)) {
            // We didn't get a row from the table so create an empty one
            $this->_tableRow = $this->_getTable()->createRow();
            unset($this->_tableRowPrevious);
            $this->_rowAction = 'INSERT';
        } else {
            $this->_tableRow = $row;
            $this->_tableRowPrevious = $row;
            $this->_rowAction = 'UPDATE';
        }
    }


    /**
     * Manipulates the $result data into an array to be passed to the table row
     *
     * @param object $result    The current result item. Only passed to enable progress output
     * @return void
     */
    protected function _formatData($result)
    {
    }


    /**
     * Allows pre-save logic to be applied.
     * Subclasses may override this method.
     *
     * @param object $result    The current result item
     * @return void
     */
    protected function _preSave($result)
    {
        // Set these here rather than in the table class so that we can use the startTime
        if ($this->_rowAction === 'INSERT') {
            $this->_data['createdDate']     = (string) $this->_startTime->format('c');
        }
        $this->_data['lastModifiedDate']    = (string) $this->_startTime->format('c');
    }


    /**
     * Save the row
     *
     * @param object $result    The current result item
     * @return void
     * @throws
     */
    protected function _saveRow($result)
    {
        if ($this->_rowAction === 'INSERT' && $this->endpointState === 'deleted') {
            // No need to INSERT rows of deleted items.
            if ($this->_showProgress) {
                echo '<p>Skipping INSERT of deleted item ' . $result->id . '</p>' . PHP_EOL;
            }
            unset($this->_data);
        } else {
            try {
                $this->_tableRow->setFromArray($this->_data);
                unset($this->_data);

                $this->_tableRow->save();

                if ($this->_showProgress) {
                    $message = 'Successful ' . $this->_rowAction . ' of ' . $result->id;
                    if (!empty($result->name)) {
                        $message .= ': ' . $result->name;
                    }

                    echo '<p>' . $message . '</p>' . PHP_EOL;
                }
            } catch (Exception $e) {
                if ($this->_showProgress) {
                    $message = 'Error attempting to ' . $this->_rowAction . ' ' . $result->id;
                    if (!empty($result->name)) {
                        $message .= ': ' . $result->name;
                    }

                    echo '<h2 class="error">' . $message . '</h2>' . PHP_EOL;
                }

                throw new namespace\Exception($e);
            }
        }
    }


    /**
     * Allows post-save logic to be applied.
     * Subclasses may override this method.
     *
     * @param object $result    The current result item
     * @return void
     */
    protected function _postSave($result)
    {
    }


}
