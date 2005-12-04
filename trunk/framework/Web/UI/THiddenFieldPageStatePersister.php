<?php

class THiddenFieldPageStatePersister extends TComponent implements IPageStatePersister, IModule
{
	private $_application;
	private $_id='persister';

	/**
	 * Initializes the service.
	 * This method is required by IModule interface.
	 * @param TApplication application
	 * @param TXmlElement module configuration
	 */
	public function init($application, $config)
	{
		$this->_application=$application;
		$application->getService()->setPageStatePersister($this);
	}

	/**
	 * @return string id of this module
	 */
	public function getID()
	{
		return $this->_id;
	}

	/**
	 * @param string id of this module
	 */
	public function setID($value)
	{
		$this->_id=$value;
	}

	public function save($state)
	{
		$data=Prado::serialize($state);
		$hmac=$this->computeHMAC($data,$this->getKey());
		if(extension_loaded('zlib'))
			$data=gzcompress($hmac.$data);
		else
			$data=$hmac.$data;
		$this->_application->getService()->getRequestedPage()->getClientScript()->registerHiddenField(TPage::FIELD_PAGESTATE,base64_encode($data));
	}

	public function load()
	{
		$str=base64_decode($this->_application->getRequest()->getItems()->itemAt(TPage::FIELD_PAGESTATE));
		if($str==='')
			return null;
		if(extension_loaded('zlib'))
			$data=gzuncompress($str);
		else
			$data=$str;
		if($data!==false && strlen($data)>32)
		{
			$hmac=substr($data,0,32);
			$state=substr($data,32);
			if($hmac===$this->computeHMAC($state,$this->getKey()))
				return Prado::unserialize($state);
		}
		throw new Exception('viewstate data is corrupted.');
	}

	private function getKey()
	{
		return 'abcdefe';
	}

	private function computeHMAC($data,$key)
	{
		if (strlen($key) > 64)
			$key = pack('H32', md5($key));
		else if (strlen($key) < 64)
			$key = str_pad($key, 64, "\0");
		return md5((str_repeat("\x5c", 64) ^ substr($key, 0, 64)) . pack('H32', md5((str_repeat("\x36", 64) ^ substr($key, 0, 64)) . $data)));
	}
}

?>