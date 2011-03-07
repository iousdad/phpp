<?php
/**
 * Description of PHP file wsp\class\database\DbTableObject.class.php
 * Class DbTableObject
 *
 * WebSite-PHP : PHP Framework 100% object (http://www.website-php.com)
 * Copyright (c) 2009-2011 WebSite-PHP.com
 * PHP versions >= 5.2
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Emilien MOREL <admin@website-php.com>
 * @link        http://www.website-php.com
 * @copyright   WebSite-PHP.com 03/10/2010
 *
 * @version     1.0.40
 * @access      public
 * @since       1.0.17
 */

class DbTableObject {
	/**#@+
	* @access private
	*/
	private $db_schema_name = "";
	private $db_table_name = "";
	private $db_table_attributes = array();
	private $db_table_attributes_type = array();
	private $db_table_primary_keys = array();
	/**#@-*/
	
	/**
	 * Constructor DbTableObject
	 */
	function __construct() {}

	/**
	 * Method getDbSchemaName
	 * @access 
	 * @return mixed
	 * @since 1.0.35
	 */
	function getDbSchemaName() {
		return $this->db_schema_name;
	}
	
	/**
	 * Method getDbTableName
	 * @access 
	 * @return mixed
	 * @since 1.0.35
	 */
	function getDbTableName() {
		return $this->db_table_name;
	}
	
	/**
	 * Method getDbTableSchemaName
	 * @access 
	 * @return mixed
	 * @since 1.0.35
	 */
	function getDbTableSchemaName() {
		return (($this->db_schema_name!="")? $this->db_schema_name."." : "").$this->db_table_name;
	}
	
	/**
	 * Method getDbTableAttributes
	 * @access 
	 * @return mixed
	 * @since 1.0.35
	 */
	function getDbTableAttributes() {
		return $this->db_table_attributes;
	}
	
	/**
	 * Method getDbTableAttributesType
	 * @access 
	 * @return mixed
	 * @since 1.0.35
	 */
	function getDbTableAttributesType() {
		return $this->db_table_attributes_type;
	}
	
	/**
	 * Method getDbTablePrimaryKeys
	 * @access 
	 * @return mixed
	 * @since 1.0.35
	 */
	function getDbTablePrimaryKeys() {
		return $this->db_table_primary_keys;
	}
	
	/**
	 * Method setDbSchemaName
	 * @access 
	 * @param mixed $db_schema_name 
	 */
	function setDbSchemaName($db_schema_name) {
		$this->db_schema_name = $db_schema_name;
	}
	
	/**
	 * Method setDbTableName
	 * @access 
	 * @param mixed $db_table_name 
	 */
	function setDbTableName($db_table_name) {
		$this->db_table_name = $db_table_name;
	}
	
	/**
	 * Method setDbTableAttributes
	 * @access 
	 * @param mixed $db_table_attributes 
	 */
	function setDbTableAttributes($db_table_attributes) {
		$this->db_table_attributes = $db_table_attributes;
	}
	
	/**
	 * Method setDbTableAttributesType
	 * @access 
	 * @param mixed $db_table_attributes_type 
	 */
	function setDbTableAttributesType($db_table_attributes_type) {
		$this->db_table_attributes_type = $db_table_attributes_type;
	}
	
	/**
	 * Method setDbTablePrimaryKeys
	 * @access 
	 * @param mixed $db_table_primary_keys 
	 */
	function setDbTablePrimaryKeys($db_table_primary_keys) {
		$this->db_table_primary_keys = $db_table_primary_keys;
	}
}
?>
