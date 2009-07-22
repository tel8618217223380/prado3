<?php
/**
 * UserList page class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package Demos
 */

/**
 * List all users in a repeater.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version $Id$
 * @package Demos
 * @since 3.1
 */
class UserList extends TPage
{
	/**
	 * Load all the users and display them in a repeater.
	 */
	function onLoad($param)
	{
		$userDao = $this->Application->Modules['daos']->getDao('UserDao');
		$this->list->DataSource = $userDao->getAllUsers();
		$this->list->dataBind(); 	
	}
}

?>