<?php
/**
 * Base DAO class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package Demos
 */

/**
 * Base DAO class.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version $Id$
 * @package Demos
 * @since 3.1
 */
class BaseDao
{
	/**
	 * @var TSqlMapGateway sqlmap client.
	 */
	private $_sqlmap;

	/**
	 * @param TSqlMapGateway sqlmap client.
	 */
	public function setSqlMap($sqlmap)
	{
		$this->_sqlmap = $sqlmap;
	}

	/**
	 * @return TSqlMapGateway sqlmap client.
	 */
	protected function getSqlMap()
	{
		return $this->_sqlmap;
	}
}

?>