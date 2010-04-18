<?php
/**
 * THtmlElement class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2008 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package System.Web.UI.WebControls
 */

Prado::using('System.Web.UI.WebControls.TWebControl');

/**
 * THtmlElement class.
 *
 * THtmlElement represents a generic HTML element whose tag name is specified
 * via {@link setTagName TagName} property. Because THtmlElement extends from
 * {@link TWebControl}, it enjoys all its functionalities.
 *
 * To change the default tag your subclass should override {@link getDefaultTag}
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Brad Anderson <javalizard@gmail.com>
 * @version $Id$
 * @package System.Web.UI.WebControls
 * @since 3.1.2
 */
class THtmlElement extends TWebControl
{
	/**
	 * @var the tag of this element
	 */
	private $_tagName=null;

	/**
	 * @return string the tag name of this control. Defaults to 'span'.
	 */
	public function getTagName()
	{
		if($this->_tagName !== null) return $this->_tagName;
		
		$this->_tagName = $this->getDefaultTagName();
		
		return $this->_tagName;
	}

	/**
	 * @param string the tag name of this control.
	 */
	public function setTagName($value)
	{
		$this->_tagName=TPropertyValue::ensureString($value);
	}
	
	/**
	 *	This is the default tag when no other is specified
	 * @return string the default tag 
	 */
	protected function getDefaultTagName() {
		return 'span';
	}
}
