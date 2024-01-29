<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp
 * @link        https://www.crosstec.org
 * @license     GNU/GPL
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableStorage_fields extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    public $id = null;
    
    public $storage_id = 0;
    
    public $name = '';
    
    public $title = '';

    public $is_group = 0;
    
    public $group_definition = "Label 1;value1\nLabel 2;value2\nLabel 3;value3";
    
    public $ordering = 0;

    public $published = 1;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct( $db ) {
        parent::__construct('#__contentbuilder_storage_fields', 'id', $db);
    }
}

// as of J! 2.5
class storageTableStorage_fields extends TableStorage_fields{}
