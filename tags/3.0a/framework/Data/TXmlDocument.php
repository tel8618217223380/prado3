<?php
/**
 * TXmlElement, TXmlDocument, TXmlElementList class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Data
 */

/**
 * TXmlElement class.
 *
 * TXmlElement represents an XML element node.
 * You can obtain its tagname, attributes, text between the openning and closing
 * tags via the TagName, Attributes, and Value properties, respectively.
 * You can also retrieve its parent and child elements by Parent and Elements
 * properties, respectively.
 *
 * TBD: xpath
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
class TXmlElement extends TComponent
{
	/**
	 * @var TXmlElement parent of this element
	 */
	private $_parent=null;
	/**
	 * @var string tagname of this element
	 */
	private $_tagName;
	/**
	 * @var string text enclosed between openning and closing tags of this element
	 */
	private $_value;
	/**
	 * @var TXmlElementList list of child elements of this element
	 */
	private $_elements=null;
	/**
	 * @var TMap attributes of this element
	 */
	private $_attributes=null;

	/**
	 * Constructor.
	 * @param string tagname for this element
	 */
	public function __construct($tagName)
	{
		parent::__construct();
		$this->setTagName($tagName);
	}

	/**
	 * @return TXmlElement parent element of this element
	 */
	public function getParent()
	{
		return $this->_parent;
	}

	/**
	 * @param TXmlElement parent element of this element
	 */
	public function setParent($parent)
	{
		$this->_parent=$parent;
	}

	/**
	 * @return string tagname of this element
	 */
	public function getTagName()
	{
		return $this->_tagName;
	}

	/**
	 * @param string tagname of this element
	 */
	public function setTagName($tagName)
	{
		$this->_tagName=$tagName;
	}

	/**
	 * @return string text enclosed between opening and closing tag of this element
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param string text enclosed between opening and closing tag of this element
	 */
	public function setValue($value)
	{
		$this->_value=$value;
	}

	/**
	 * @return boolean true if this element has child elements
	 */
	public function getHasElement()
	{
		return $this->_elements!==null && $this->_elements->getCount()>0;
	}

	/**
	 * @return boolean true if this element has attributes
	 */
	public function getHasAttribute()
	{
		return $this->_attributes!==null && $this->_attributes->getCount()>0;
	}

	/**
	 * @return string the attribute specified by the name, null if no such attribute
	 */
	public function getAttribute($name)
	{
		if($this->_attributes!==null)
			return $this->_attributes->itemAt($name);
		else
			return null;
	}

	/**
	 * @return TXmlElementList list of child elements
	 */
	public function getElements()
	{
		if(!$this->_elements)
			$this->_elements=new TXmlElementList($this);
		return $this->_elements;
	}

	/**
	 * @return TMap list of attributes
	 */
	public function getAttributes()
	{
		if(!$this->_attributes)
			$this->_attributes=new TMap;
		return $this->_attributes;
	}

	/**
	 * @return TXmlElement the first child element that has the specified tagname, null if not found
	 */
	public function getElementByTagName($tagName)
	{
		if($this->_elements)
		{
			foreach($this->_elements as $element)
				if($element->_tagName===$tagName)
					return $element;
		}
		return null;
	}

	/**
	 * @return TList list of all child elements that have the specified tagname
	 */
	public function getElementsByTagName($tagName)
	{
		$list=new TList;
		if($this->_elements)
		{
			foreach($this->_elements as $element)
				if($element->_tagName===$tagName)
					$list->add($element);
		}
		return $list;
	}

	/**
	 * @return string string representation of this element
	 */
	public function toString($indent=0)
	{
		$attr='';
		if($this->_attributes!==null)
		{
			foreach($this->_attributes as $name=>$value)
				$attr.=" $name=\"$value\"";
		}
		$prefix=str_repeat(' ',$indent*4);
		if($this->getHasElement())
		{
			$str=$prefix."<{$this->_tagName}$attr>\n";
			foreach($this->getElements() as $element)
				$str.=$element->toString($indent+1)."\n";
			$str.=$prefix."</{$this->_tagName}>";
			return $str;
		}
		else if($this->getValue()!=='')
		{
			return $prefix."<{$this->_tagName}$attr>{$this->_value}</{$this->_tagName}>";
		}
		else
			return $prefix."<{$this->_tagName}$attr />";
	}
}

/**
 * TXmlDocument class.
 *
 * TXmlDocument represents a DOM representation of an XML file.
 * Besides all properties and methods inherited from {@link TXmlElement},
 * you can load an XML file or string by {@link loadFromFile} or {@link loadFromString}.
 * You can also get the version and encoding of the XML document by
 * the Version and Encoding properties.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
class TXmlDocument extends TXmlElement
{
	/**
	 * @var string version of this XML document
	 */
	private $_version;
	/**
	 * @var string encoding of this XML document
	 */
	private $_encoding;

	/**
	 * Constructor.
	 * @param string version of this XML document
	 * @param string encoding of this XML document
	 */
	public function __construct($version='1.0',$encoding='')
	{
		parent::__construct('');
		$this->setversion($version);
		$this->setEncoding($encoding);
	}

	/**
	 * @return string version of this XML document
	 */
	public function getVersion()
	{
		return $this->_version;
	}

	/**
	 * @param string version of this XML document
	 */
	public function setVersion($version)
	{
		$this->_version=$version;
	}

	/**
	 * @return string encoding of this XML document
	 */
	public function getEncoding()
	{
		return $this->_encoding;
	}

	/**
	 * @param string encoding of this XML document
	 */
	public function setEncoding($encoding)
	{
		$this->_encoding=$encoding;
	}

	/**
	 * Loads and parses an XML document.
	 * @param string the XML file path
	 * @return boolean whether the XML file is parsed successfully
	 * @throws TIOException if the file fails to be opened.
	 */
	public function loadFromFile($file)
	{
		if(($str=@file_get_contents($file))!==false)
			return $this->loadFromString($str);
		else
			throw new TIOException('xmldocument_file_read_failed',$file);
	}

	/**
	 * Loads and parses an XML string.
	 * The version and encoding will be determined based on the parsing result.
	 * @param string the XML string
	 * @return boolean whether the XML string is parsed successfully
	 */
	public function loadFromString($string)
	{
		// TODO: since PHP 5.1, we can get parsing errors and throw them as exception
		$doc=new DOMDocument();
		if($doc->loadXML($string)===false)
			return false;

		$this->setEncoding($doc->encoding);
		$this->setVersion($doc->version);

		$element=$doc->documentElement;
		$this->setTagName($element->tagName);
		$this->setValue($element->nodeValue);
		$elements=$this->getElements();
		$attributes=$this->getAttributes();
		$elements->clear();
		$attributes->clear();
		foreach($element->attributes as $name=>$attr)
			$attributes->add($name,$attr->value);
		foreach($element->childNodes as $child)
		{
			if($child instanceof DOMElement)
				$elements->add($this->buildElement($child));
		}

		return true;
	}

	/**
	 * Saves this XML document as an XML file.
	 * @param string the name of the file to be stored with XML output
	 * @throws TIOException if the file cannot be written
	 */
	public function saveToFile($file)
	{
		if(($fw=fopen($file,'w'))!==false)
		{
			fwrite($fw,$this->saveToString());
			fclose($fw);
		}
		else
			throw new TIOException('xmldocument_file_write_failed',$file);
	}

	/**
	 * Saves this XML document as an XML string
	 * @return string the XML string of this XML document
	 */
	public function saveToString()
	{
		$version=empty($this->_version)?' version="1.0"':' version="'.$this->_version.'"';
		$encoding=empty($this->_encoding)?'':' encoding="'.$this->_encoding.'"';
		return "<?xml{$version}{$encoding}?>\n".$this->toString(0);
	}

	/**
	 * Recursively converts DOM XML nodes into TXmlElement
	 * @param DOMXmlNode the node to be converted
	 * @return TXmlElement the converted TXmlElement
	 */
	private function buildElement($node)
	{
		$element=new TXmlElement($node->tagName);
		$element->setValue($node->nodeValue);
		foreach($node->attributes as $name=>$attr)
			$element->getAttributes()->add($name,$attr->value);
		foreach($node->childNodes as $child)
		{
			if($child instanceof DOMElement)
				$element->getElements()->add($this->buildElement($child));
		}
		return $element;
	}
}


/**
 * TXmlElement class.
 *
 * TXmlElement represents an XML element node.
 * You can obtain its tagname, attributes, text between the openning and closing
 * tags via the TagName, Attributes, and Value properties, respectively.
 * You can also retrieve its parent and child elements by Parent and Elements
 * properties, respectively.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System
 * @since 3.0
 */
class TXmlElementList extends TList
{
	/**
	 * @var TXmlElement owner of this list
	 */
	private $_o;

	/**
	 * Constructor.
	 * @param TXmlElement owner of this list
	 */
	public function __construct(TXmlElement $owner)
	{
		parent::__construct();
		$this->_o=$owner;
	}

	/**
	 * @return TXmlElement owner of this list
	 */
	protected function getOwner()
	{
		return $this->_o;
	}

	/**
	 * Overrides the parent implementation with customized processing of the newly added item.
	 * @param mixed the newly added item
	 */
	protected function addedItem($item)
	{
		if($item->getParent()!==null)
			$item->getParent()->getElements()->remove($item);
		$item->setParent($this->_o);
	}

	/**
	 * Overrides the parent implementation with customized processing of the removed item.
	 * @param mixed the removed item
	 */
	protected function removedItem($item)
	{
		$item->setParent(null);
	}

	/**
	 * This method is invoked before adding an item to the map.
	 * If it returns true, the item will be added to the map, otherwise not.
	 * You can override this method to decide whether a specific can be added.
	 * @param mixed item to be added
	 * @return boolean whether the item can be added to the map
	 */
	protected function canAddItem($item)
	{
		return ($item instanceof TXmlElement);
	}
}

?>