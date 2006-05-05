<?php
/**
 * TActiveLabel class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.ActiveControls
 */

/**
 * TActiveLabel class
 *
 * The active control counterpart of TLabel component. During a callback
 * request, setting {@link setText Text} property will also set the text of the
 * label on the client upon callback completion. Similarly, setting {@link
 * setForControl ForControl} will set the client-side for attribute on the
 * label.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.ActiveControls
 * @since 3.0
 */
class TActiveLabel extends TLabel
{
	/**
	 * Creates a new callback control, sets the adapter to
	 * TActiveControlAdapter. If you override this class, be sure to set the
	 * adapter appropriately by, for example, by calling this constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setAdapter(new TActiveControlAdapter($this));
	}

	/**
	 * On callback response, the inner HTMl of the label is updated.
	 * @param string the text value of the label
	 */
	public function setText($value)
	{
		parent::setText($value);
		if($this->getIsInitialized())
		{
			$this->getPage()->getCallbackClient()->update($this, $value);
		}
	}
	
	/**
	 * Sets the ID of the control that the label is associated with.
	 * The control must be locatable via {@link TControl::findControl} using the ID.
	 * On callback response, the For attribute of the label is updated.
	 * @param string the associated control ID
	 */
	public function setForControl($value)
	{
		parent::setForControl($value);
		if($this->getIsInitialized())
		{
			$id=$this->findControl($value)->getClientID();
			$this->getPage()->getCallbackClient()->setAttribute($this, 'for', $id);
		}
	}
} 

?>