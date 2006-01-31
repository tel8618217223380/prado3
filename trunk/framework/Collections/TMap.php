<?php
/**
 * TMap, TMapIterator classes
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Collections
 */

/**
 * TMap class
 *
 * TMap implements a collection that takes key-value pairs.
 *
 * You can access, add or remove an item with a key by using
 * {@link itemAt}, {@link add}, and {@link remove}.
 * To get the number of the items in the map, use {@link getCount}.
 * TMap can also be used like a regular array as follows,
 * <code>
 * $map[$key]=$value; // add a key-value pair
 * unset($map[$key]); // remove the value with the specified key
 * if(isset($map[$key])) // if the map contains the key
 * foreach($map as $key=>$value) // traverse the items in the map
 * </code>
 * Note, count($map) will always return 1. You should use {@link getCount()}
 * to determine the number of items in the map.
 *
 * To extend TMap by doing additional operations with each added or removed item,
 * you can override {@link addedItem} and {@link removedItem}.
 * You can also override {@link canAddItem} and {@link canRemoveItem} to
 * control whether to add or remove a particular item.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Collections
 * @since 3.0
 */
class TMap extends TComponent implements IteratorAggregate,ArrayAccess
{
	/**
	 * @var array internal data storage
	 */
	private $_d=array();

	/**
	 * Constructor.
	 * Initializes the list with an array or an iterable object.
	 * @param array|Iterator the intial data. Default is null, meaning no initialization.
	 * @throws TInvalidDataTypeException If data is not null and neither an array nor an iterator.
	 */
	public function __construct($data=null)
	{
		parent::__construct();
		if($data!==null)
			$this->copyFrom($data);
	}

	/**
	 * Returns an iterator for traversing the items in the list.
	 * This method is required by the interface IteratorAggregate.
	 * @return Iterator an iterator for traversing the items in the list.
	 */
	public function getIterator()
	{
		return new TMapIterator($this->_d);
	}

	/**
	 * @return integer the number of items in the map
	 */
	public function getCount()
	{
		return count($this->_d);
	}

	/**
	 * @return array the key list
	 */
	public function getKeys()
	{
		return array_keys($this->_d);
	}

	/**
	 * Returns the item with the specified key.
	 * This method is exactly the same as {@link offsetGet}.
	 * @param mixed the key
	 * @return mixed the element at the offset, null if no element is found at the offset
	 */
	public function itemAt($key)
	{
		return isset($this->_d[$key]) ? $this->_d[$key] : null;
	}

	/**
	 * Adds an item into the map.
	 * Note, if the specified key already exists, the old value will be removed first.
	 * @param mixed key
	 * @param mixed value
	 */
	public function add($key,$value)
	{
		if(isset($this->_d[$key]))
			$this->remove($key);
		$this->_d[$key]=$value;
	}

	/**
	 * Removes an item from the map by its key.
	 * @param mixed the key of the item to be removed
	 * @return mixed the removed value, null if no such key exists.
	 * @throws TInvalidOperationException if the item cannot be removed
	 */
	public function remove($key)
	{
		if(isset($this->_d[$key]))
		{
			$value=$this->_d[$key];
			unset($this->_d[$key]);
			return $value;
		}
		else
			return null;
	}

	/**
	 * Removes all items in the map.
	 */
	public function clear()
	{
		foreach(array_keys($this->_d) as $key)
			$this->remove($key);
	}

	/**
	 * @param mixed the key
	 * @return boolean whether the map contains an item with the specified key
	 */
	public function contains($key)
	{
		return isset($this->_d[$key]);
	}

	/**
	 * @return array the list of items in array
	 */
	public function toArray()
	{
		return $this->_d;
	}

	/**
	 * Copies iterable data into the map.
	 * Note, existing data in the map will be cleared first.
	 * @param mixed the data to be copied from, must be an array or object implementing Traversable
	 * @throws TInvalidDataTypeException If data is neither an array nor an iterator.
	 */
	public function copyFrom($data)
	{
		if(is_array($data) || $data instanceof Traversable)
		{
			if($this->getCount()>0)
				$this->clear();
			foreach($data as $key=>$value)
				$this->add($key,$value);
		}
		else if($data!==null)
			throw new TInvalidDataTypeException('map_data_not_iterable');
	}

	/**
	 * Merges iterable data into the map.
	 * Existing data in the map will be kept and overwritten if the keys are the same.
	 * @param mixed the data to be merged with, must be an array or object implementing Traversable
	 * @throws TInvalidDataTypeException If data is neither an array nor an iterator.
	 */
	public function mergeWith($data)
	{
		if(is_array($data) || $data instanceof Traversable)
		{
			foreach($data as $key=>$value)
				$this->add($key,$value);
		}
		else if($data!==null)
			throw new TInvalidDataTypeException('map_data_not_iterable');
	}

	/**
	 * Returns whether there is an element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param mixed the offset to check on
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return $this->contains($offset);
	}

	/**
	 * Returns the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param integer the offset to retrieve element.
	 * @return mixed the element at the offset, null if no element is found at the offset
	 */
	public function offsetGet($offset)
	{
		return $this->itemAt($offset);
	}

	/**
	 * Sets the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param integer the offset to set element
	 * @param mixed the element value
	 */
	public function offsetSet($offset,$item)
	{
		$this->add($offset,$item);
	}

	/**
	 * Unsets the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param mixed the offset to unset element
	 */
	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}
}

/**
 * TMapIterator class
 *
 * TMapIterator implements Iterator interface.
 *
 * TMapIterator is used by TMap. It allows TMap to return a new iterator
 * for traversing the items in the map.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Collections
 * @since 3.0
 */
class TMapIterator implements Iterator
{
	/**
	 * @var array the data to be iterated through
	 */
	private $_d;
	/**
	 * @var array list of keys in the map
	 */
	private $_keys;
	/**
	 * @var mixed current key
	 */
	private $_key;

	/**
	 * Constructor.
	 * @param array the data to be iterated through
	 */
	public function __construct(&$data)
	{
		$this->_d=&$data;
		$this->_keys=array_keys($data);
	}

	/**
	 * Rewinds internal array pointer.
	 * This method is required by the interface Iterator.
	 */
	public function rewind()
	{
		$this->_key=reset($this->_keys);
	}

	/**
	 * Returns the key of the current array element.
	 * This method is required by the interface Iterator.
	 * @return mixed the key of the current array element
	 */
	public function key()
	{
		return $this->_key;
	}

	/**
	 * Returns the current array element.
	 * This method is required by the interface Iterator.
	 * @return mixed the current array element
	 */
	public function current()
	{
		return $this->_d[$this->_key];
	}

	/**
	 * Moves the internal pointer to the next array element.
	 * This method is required by the interface Iterator.
	 */
	public function next()
	{
		do
		{
			$this->_key=next($this->_keys);
		}
		while(!isset($this->_d[$this->_key]) && $this->_key!==false);
	}

	/**
	 * Returns whether there is an element at current position.
	 * This method is required by the interface Iterator.
	 * @return boolean
	 */
	public function valid()
	{
		return $this->_key!==false;
	}
}
?>