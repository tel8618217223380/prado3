<?php
/**
 * IUserManager interface file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package System.Security
 */

/**
 * IUserManager interface
 *
 * IUserManager specifies the interface that must be implemented by
 * a user manager class if it is to be used together with {@link TAuthManager}
 * and {@link TUser}.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package System.Security
 * @since 3.0
 */
interface IUserManager
{
	/**
	 * @return string name for a guest user.
	 */
	public function getGuestName();
	/**
	 * Returns a user instance given the user name.
	 * @param string user name, null if it is a guest.
	 * @return TUser the user instance, null if the specified username is not in the user database.
	 */
	public function getUser($username=null);
	/**
	 * Validates if the username and password are correct.
	 * @param string user name
	 * @param string password
	 * @return boolean true if validation is successful, false otherwise.
	 */
	public function validateUser($username,$password);
}

?>