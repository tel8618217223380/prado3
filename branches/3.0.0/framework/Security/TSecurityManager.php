<?php
/**
 * TSecurityManager class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Security
 */

/**
 * TSecurityManager class
 *
 * TSecurityManager provides private keys, hashing and encryption
 * functionalities that may be used by other PRADO components,
 * such as viewstate persister, cookies.
 *
 * TSecurityManager is mainly used to protect data from being tampered
 * and viewed. It can generate HMAC and encrypt the data.
 * The private key used to generate HMAC is set by {@link setValidationKey ValidationKey}.
 * The key used to encrypt data is specified by {@link setEncryptionKey EncryptionKey}.
 * If the above keys are not explicitly set, random keys will be generated
 * and used.
 *
 * To prefix data with an HMAC, call {@link hashData()}.
 * To validate if data is tampered, call {@link validateData()}, which will
 * return the real data if it is not tampered.
 * The algorithm used to generated HMAC is specified by {@link setValidation Validation}.
 *
 * To encrypt and decrypt data, call {@link encrypt()} and {@link decrypt()}
 * respectively. The encryption algorithm can be set by {@link setEncryption Encryption}.
 *
 * Note, to use encryption, the PHP Mcrypt extension must be loaded.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Security
 * @since 3.0
 */
class TSecurityManager extends TModule
{
	const STATE_VALIDATION_KEY='prado:securitymanager:validationkey';
	const STATE_ENCRYPTION_KEY='prado:securitymanager:encryptionkey';
	private $_validationKey;
	private $_encryptionKey;
	private $_validation='SHA1';
	private $_encryption='3DES';

	/**
	 * Initializes the module.
	 * The security module is registered with the application.
	 * @param TXmlElement initial module configuration
	 */
	public function init($config)
	{
		$this->getApplication()->setSecurityManager($this);
	}

	/**
	 * Generates a random key.
	 */
	protected function generateRandomKey()
	{
		$v1=rand();
		$v2=rand();
		$v3=rand();
		return md5("$v1$v2$v3");
	}

	/**
	 * @return string the private key used to generate HMAC.
	 * If the key is not explicitly set, a random one is generated and used.
	 */
	public function getValidationKey()
	{
		if(empty($this->_validationKey))
		{
			if(($this->_validationKey=$this->getApplication()->getGlobalState(self::STATE_VALIDATION_KEY))===null)
			{
				$this->_validationKey=$this->generateRandomKey();
				$this->getApplication()->setGlobalState(self::STATE_VALIDATION_KEY,$this->_validationKey,null);
			}
		}
		return $this->_validationKey;
	}

	/**
	 * @param string the key used to generate HMAC
	 * @throws TInvalidDataValueException if the key is shorter than 8 characters.
	 */
	public function setValidationKey($value)
	{
		if(strlen($value)<8)
			throw new TInvalidDataValueException('securitymanager_validationkey_invalid');
		$this->_validationKey=$value;
	}

	/**
	 * @return string the private key used to encrypt/decrypt data.
	 * If the key is not explicitly set, a random one is generated and used.
	 */
	public function getEncryptionKey()
	{
		if(empty($this->_encryptionKey))
		{
			if(($this->_encryptionKey=$this->getApplication()->getGlobalState(self::STATE_ENCRYPTION_KEY))===null)
			{
				$this->_encryptionKey=$this->generateRandomKey();
				$this->getApplication()->setGlobalState(self::STATE_ENCRYPTION_KEY,$this->_encryptionKey,null);
			}
		}
		return $this->_encryptionKey;
	}

	/**
	 * @param string the key used to encrypt/decrypt data.
	 * @throws TInvalidDataValueException if the key is shorter than 8 characters.
	 */
	public function setEncryptionKey($value)
	{
		if(strlen($value)<8)
			throw new TInvalidDataValueException('securitymanager_encryptionkey_invalid');
		$this->_encryptionKey=$value;
	}

	/**
	 * @return string hashing algorithm used to generate HMAC. Defaults to 'SHA1'.
	 */
	public function getValidation()
	{
		return $this->_validation;
	}

	/**
	 * @param string hashing algorithm used to generate HMAC. Valid values include 'SHA1' and 'MD5'.
	 */
	public function setValidation($value)
	{
		$this->_validation=TPropertyValue::ensureEnum($value,'SHA1','MD5');
	}

	/**
	 * @return string the algorithm used to encrypt/decrypt data. Defaults to '3DES'.
	 */
	public function getEncryption()
	{
		return $this->_encryption;
	}

	/**
	 * @throws TNotSupportedException Do not call this method presently.
	 */
	public function setEncryption($value)
	{
		throw new TNotSupportedException('Currently only 3DES encryption is supported');
	}

	/**
	 * Encrypts data with {@link getEncryptionKey EncryptionKey}.
	 * @param string data to be encrypted.
	 * @return string the encrypted data
	 * @throws TNotSupportedException if PHP Mcrypt extension is not loaded
	 */
	public function encrypt($data)
	{
		if(function_exists('mcrypt_encrypt'))
		{
			return mcrypt_encrypt(MCRYPT_3DES, $this->getEncryptionKey(), $data, MCRYPT_MODE_CBC);
		}
		else
			throw new TNotSupportedException('securitymanager_mcryptextension_required');
	}

	/**
	 * Decrypts data with {@link getEncryptionKey EncryptionKey}.
	 * @param string data to be decrypted.
	 * @return string the decrypted data
	 * @throws TNotSupportedException if PHP Mcrypt extension is not loaded
	 */
	public function decrypt($data)
	{
		if(function_exists('mcrypt_decrypt'))
		{
			return mcrypt_decrypt(MCRYPT_3DES, $this->getEncryptionKey(), $data, MCRYPT_MODE_CBC);
		}
		else
			throw new TNotSupportedException('securitymanager_mcryptextension_required');
	}

	/**
	 * Prefixes data with an HMAC.
	 * @param string data to be hashed.
	 * @return string data prefixed with HMAC
	 */
	public function hashData($data)
	{
		$hmac=$this->computeHMAC($data);
		return $hmac.$data;
	}

	/**
	 * Validates if data is tampered.
	 * @param string data to be validated. The data must be previously
	 * generated using {@link hashData()}.
	 * @return string the real data with HMAC stripped off. False if the data
	 * is tampered.
	 */
	public function validateData($data)
	{
		$len=$this->_validation==='SHA1'?40:32;
		if(strlen($data)>=$len)
		{
			$hmac=substr($data,0,$len);
			$data2=substr($data,$len);
			return $hmac===$this->computeHMAC($data2)?$data2:false;
		}
		else
			return false;
	}

	/**
	 * Computes the HMAC for the data with {@link getValidationKey ValidationKey}.
	 * @param string data to be generated HMAC
	 * @return string the HMAC for the data
	 */
	protected function computeHMAC($data)
	{
		if($this->_validation==='SHA1')
		{
			$pack='H40';
			$func='sha1';
		}
		else
		{
			$pack='H32';
			$func='md5';
		}
		$key=$this->getValidationKey();
		if (strlen($key) > 64)
			$key = pack($pack, $func($key));
		if (strlen($key) < 64)
			$key = str_pad($key, 64, chr(0));
		return $func((str_repeat(chr(0x5C), 64) ^ substr($key, 0, 64)) . pack($pack, $func((str_repeat(chr(0x36), 64) ^ substr($key, 0, 64)) . $data)));
	}
}

?>