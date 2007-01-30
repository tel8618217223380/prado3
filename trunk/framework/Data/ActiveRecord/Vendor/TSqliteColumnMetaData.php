<?php
/**
 * TSqliteColumnMetaData class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package System.Data.ActiveRecord.Vendor
 */

/**
 * TSqliteColumnMetaData class.
 *
 * Column details for SQLite version 2.x or 3.x. database.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package System.Data.ActiveRecord.Vendor
 * @since 3.1
 */
class TSqliteColumnMetaData extends TComponent
{
	private $_name;
	private $_type;
	private $_notNull;
	private $_autoIncrement;
	private $_default;
	private $_primary=false;
	private $_property;
	private $_length;

	public function __construct($property,$name,$type,$notNull,$autoIncrement,$default,$primary)
	{
		$this->_property=$property;
		$this->_name=$name;
		$this->_notNull=$notNull;
		$this->_autoIncrement=$autoIncrement;
		$this->_default=$default;
		$this->_primary=$primary;
		$this->processType($type);
	}

	protected function processType($type)
	{
		if(is_int($pos=strpos($type, '(')))
		{
			$match=array();
			if(preg_match('/\((.*)\)/', $type, $match))
			{
				$this->_length=floatval($match[1]);
				$this->_type = substr($type,0,$pos);
			}
			else
				$this->_type = $type;
		}
		else
			$this->_type = $type;
	}

	public function getLength()
	{
		return $this->_length;
	}

	public function getPHPType()
	{
		switch(strtolower($this->_type))
		{
			case 'int': case 'integer': case 'mediumint': case 'smallint': case 'tinyint': case 'year':
				return 'integer';
			case 'boolean':
				return 'boolean';
			case 'decimal': case 'double': case 'float': case 'bigint':
				return 'float';
			default:
				return 'string';
		}
	}

	/**
	 * @return string column name, used as active record property name
	 */
	public function getProperty()
	{
		return $this->_property;
	}

	/**
	 * @return string quoted column name.
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @return boolean true if column is a sequence, false otherwise.
	 */
	public function hasSequence()
	{
		return $this->_autoIncrement;
	}

	/**
	 * @return null no sequence name.
	 */
	public function getSequenceName()
	{
		return null;
	}

	/**
	 * @return boolean true if the column is a primary key, or part of a composite primary key.
	 */
	public function getIsPrimaryKey()
	{
		return $this->_primary;
	}

	public function getType()
	{
		return $this->_type;
	}


	public function getNotNull()
	{
		return $this->_notNull;
	}

	/**
	 * @return boolean true if column has default value, false otherwise.
	 */
	public function hasDefault()
	{
		return $this->_default !== null;
	}

	public function getDefaultValue()
	{
		return $this->_default;
	}
}

?>