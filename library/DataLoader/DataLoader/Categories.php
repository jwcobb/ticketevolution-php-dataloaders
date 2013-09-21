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


/**
 * DataLoader for a specific API endpoint to cache the data into local table(s)
 *
 * @category    DataLoader
 * @package     DataLoader\DataLoader
 * @copyright   Copyright (c) 2013 J Cobb. (http://jcobb.org)
 * @license     http://choosealicense.com/licenses/bsd-3-clause/ BSD (3-Clause) License
 */
class Categories extends AbstractDataLoader
{
    /**
     * Which endpoint we are hitting. This is used in the `dataLoaderStatus` table
     *
     * @var string
     */
    var $endpoint = 'categories';


    /**
     * The type of items to get [active|deleted]
     *
     * @var string
     */
    var $endpointState = 'active';


    /**
     * The \TicketEvolution\Webservice method to use for the API request
     *
     * @var string
     */
    protected $_webServiceMethod = 'listCategories';


    /**
     * The class of the table
     *
     * @var Zend_Db_Table
     */
    protected $_tableClass = '\DataLoader\Db\Table\Categories';


    /**
     * Manipulates the $result data into an array to be passed to the table row
     *
     * @param object $result    The current result item
     * @return void
     */
    protected function _formatData($result)
    {
        $this->_data = array(
            'categoryId'        => (int)    $result->id,
            'categoryName'      => (string) $result->name,
            'categoryUrl'       => (string) $result->url,
            'updated_at'        => (string) $result->updated_at,
            'categoriesStatus'  => (int)    1,

            'created_at'        => null,
            'deleted_at'        => null,
            'parentCategoryId'  => null,
        );

        if (!empty($result->created_at)) {
            $this->_data['created_at'] = (string) $result->created_at;
        }

        if (!empty($result->deleted_at)) {
            $this->_data['deleted_at'] = (string) $result->deleted_at;
        }

        if (isset($result->parent->id)) {
            $this->_data['parentCategoryId'] = (int) $result->parent->id;
        }
    }


    /**
     * Allows pre-save logic to be applied.
     * Subclasses may override this method.
     *
     * @param object $result    The current result item. Only passed to enable progress output
     * @return void
     */
    protected function _preSave($result)
    {
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
