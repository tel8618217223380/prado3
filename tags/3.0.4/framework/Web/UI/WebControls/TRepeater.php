<?php
/**
 * TRepeater class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 */

/**
 * Using TDataBoundControl and TDataFieldAccessor cass
 */
Prado::using('System.Web.UI.WebControls.TDataBoundControl');
Prado::using('System.Util.TDataFieldAccessor');

/**
 * TRepeater class
 *
 * TRepeater displays its content defined in templates repeatedly based on
 * the given data specified by the {@link setDataSource DataSource} or
 * {@link setDataSourceID DataSourceID} property. The templates can contain
 * static text, controls and special tags.
 *
 * The {@link setHeaderTemplate HeaderTemplate} property specifies the content
 * template that will be displayed at the beginning, while
 * {@link setFooterTemplate FooterTemplate} at the end.
 * If present, these two templates will only be rendered when the repeater is
 * given non-empty data. In this case, for each data item the content defined
 * by {@link setItemTemplate ItemTemplate} will be generated and displayed once.
 * If {@link setAlternatingItemTemplate AlternatingItemTemplate} is not empty,
 * then the corresponding content will be displayed alternatively with that
 * in {@link setItemTemplate ItemTemplate}. The content in
 * {@link setSeparatorTemplate SeparatorTemplate}, if not empty, will be
 * displayed between items.
 *
 * Each repeater item has a {@link TRepeaterItem::getItemType type}
 * which tells the position of the item in the repeater. An item in the header
 * of the repeater is of type TListItemType::Header. A body item may be of either
 * TListItemType::Item or TListItemType::AlternatingItem, depending whether the item
 * index is odd or even.
 *
 * You can retrive the repeated contents by the {@link getItems Items} property.
 * The header and footer items can be accessed by {@link getHeader Header}
 * and {@link getFooter Footer} properties, respectively.
 *
 * When TRepeater creates an item, it will raise an {@link onItemCreated OnItemCreated}
 * so that you may customize the newly created item.
 * When databinding is performed by TRepeater, for each item once it has finished
 * databinding, an {@link onItemDataBound OnItemDataBound} event will be raised.
 *
 * TRepeater raises an {@link onItemCommand OnItemCommand} whenever a button control
 * within some repeater item raises a <b>OnCommand</b> event. Therefore,
 * you can handle all sorts of <b>OnCommand</b> event in a central place by
 * writing an event handler for {@link onItemCommand OnItemCommand}.
 *
 * Note, the data bound to the repeater are reset to null after databinding.
 * There are several ways to access the data associated with a repeater item:
 * - Access the data in {@link onItemDataBound OnItemDataBound} event
 * - Use {@link getDataKeys DataKeys} to obtain the data key associated with
 * the specified repeater item and use the key to fetch the corresponding data
 * from some persistent storage such as DB.
 * - Save the data in viewstate and get it back during postbacks.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class TRepeater extends TDataBoundControl implements INamingContainer
{
	/**
	 * Repeater item types
	 * @deprecated deprecated since version 3.0.4. Use TListItemType constants instead.
	 */
	const IT_HEADER='Header';
	const IT_FOOTER='Footer';
	const IT_ITEM='Item';
	const IT_SEPARATOR='Separator';
	const IT_ALTERNATINGITEM='AlternatingItem';

	/**
	 * @var ITemplate template for repeater items
	 */
	private $_itemTemplate=null;
	/**
	 * @var ITemplate template for each alternating item
	 */
	private $_alternatingItemTemplate=null;
	/**
	 * @var ITemplate template for header
	 */
	private $_headerTemplate=null;
	/**
	 * @var ITemplate template for footer
	 */
	private $_footerTemplate=null;
	/**
	 * @var ITemplate template used for repeater when no data is bound
	 */
	private $_emptyTemplate=null;
	/**
	 * @var ITemplate template for separator
	 */
	private $_separatorTemplate=null;
	/**
	 * @var TRepeaterItemCollection list of repeater items
	 */
	private $_items=null;
	/**
	 * @var TRepeaterItem header item
	 */
	private $_header=null;
	/**
	 * @var TRepeaterItem footer item
	 */
	private $_footer=null;

	/**
	 * @return ITemplate the template for repeater items
	 */
	public function getItemTemplate()
	{
		return $this->_itemTemplate;
	}

	/**
	 * @param ITemplate the template for repeater items
	 * @throws TInvalidDataTypeException if the input is not an {@link ITemplate} or not null.
	 */
	public function setItemTemplate($value)
	{
		if($value instanceof ITemplate || $value===null)
			$this->_itemTemplate=$value;
		else
			throw new TInvalidDataTypeException('repeater_template_required','ItemTemplate');
	}

	/**
	 * @return ITemplate the alternative template string for the item
	 */
	public function getAlternatingItemTemplate()
	{
		return $this->_alternatingItemTemplate;
	}

	/**
	 * @param ITemplate the alternative item template
	 * @throws TInvalidDataTypeException if the input is not an {@link ITemplate} or not null.
	 */
	public function setAlternatingItemTemplate($value)
	{
		if($value instanceof ITemplate || $value===null)
			$this->_alternatingItemTemplate=$value;
		else
			throw new TInvalidDataTypeException('repeater_template_required','AlternatingItemTemplate');
	}

	/**
	 * @return ITemplate the header template
	 */
	public function getHeaderTemplate()
	{
		return $this->_headerTemplate;
	}

	/**
	 * @param ITemplate the header template
	 * @throws TInvalidDataTypeException if the input is not an {@link ITemplate} or not null.
	 */
	public function setHeaderTemplate($value)
	{
		if($value instanceof ITemplate || $value===null)
			$this->_headerTemplate=$value;
		else
			throw new TInvalidDataTypeException('repeater_template_required','HeaderTemplate');
	}

	/**
	 * @return ITemplate the footer template
	 */
	public function getFooterTemplate()
	{
		return $this->_footerTemplate;
	}

	/**
	 * @param ITemplate the footer template
	 * @throws TInvalidDataTypeException if the input is not an {@link ITemplate} or not null.
	 */
	public function setFooterTemplate($value)
	{
		if($value instanceof ITemplate || $value===null)
			$this->_footerTemplate=$value;
		else
			throw new TInvalidDataTypeException('repeater_template_required','FooterTemplate');
	}

	/**
	 * @return ITemplate the template applied when no data is bound to the repeater
	 */
	public function getEmptyTemplate()
	{
		return $this->_emptyTemplate;
	}

	/**
	 * @param ITemplate the template applied when no data is bound to the repeater
	 * @throws TInvalidDataTypeException if the input is not an {@link ITemplate} or not null.
	 */
	public function setEmptyTemplate($value)
	{
		if($value instanceof ITemplate || $value===null)
			$this->_emptyTemplate=$value;
		else
			throw new TInvalidDataTypeException('repeater_template_required','EmptyTemplate');
	}

	/**
	 * @return ITemplate the separator template
	 */
	public function getSeparatorTemplate()
	{
		return $this->_separatorTemplate;
	}

	/**
	 * @param ITemplate the separator template
	 * @throws TInvalidDataTypeException if the input is not an {@link ITemplate} or not null.
	 */
	public function setSeparatorTemplate($value)
	{
		if($value instanceof ITemplate || $value===null)
			$this->_separatorTemplate=$value;
		else
			throw new TInvalidDataTypeException('repeater_template_required','SeparatorTemplate');
	}

	/**
	 * @return TRepeaterItem the header item
	 */
	public function getHeader()
	{
		return $this->_header;
	}

	/**
	 * @return TRepeaterItem the footer item
	 */
	public function getFooter()
	{
		return $this->_footer;
	}

	/**
	 * @return TRepeaterItemCollection list of {@link TRepeaterItem} controls
	 */
	public function getItems()
	{
		if(!$this->_items)
			$this->_items=new TRepeaterItemCollection;
		return $this->_items;
	}

	/**
	 * @return string the field of the data source that provides the keys of the list items.
	 */
	public function getDataKeyField()
	{
		return $this->getViewState('DataKeyField','');
	}

	/**
	 * @param string the field of the data source that provides the keys of the list items.
	 */
	public function setDataKeyField($value)
	{
		$this->setViewState('DataKeyField',$value,'');
	}

	/**
	 * @return TList the keys used in the data listing control.
	 */
	public function getDataKeys()
	{
		if(($dataKeys=$this->getViewState('DataKeys',null))===null)
		{
			$dataKeys=new TList;
			$this->setViewState('DataKeys',$dataKeys,null);
		}
		return $dataKeys;
	}

	/**
	 * Creates a repeater item instance based on the item type and index.
	 * @param integer zero-based item index
	 * @param string item type, may be 'Header', 'Footer', 'Empty', 'Item', 'Separator', 'AlternatingItem'.
	 * @return TRepeaterItem created repeater item
	 */
	protected function createItem($itemIndex,$itemType)
	{
		return new TRepeaterItem($itemIndex,$itemType);
	}

	/**
	 * Creates a repeater item and does databinding if needed.
	 * This method invokes {@link createItem} to create a new repeater item.
	 * @param integer zero-based item index.
	 * @param string item type, may be 'Header', 'Footer', 'Empty', 'Item', 'Separator', 'AlternatingItem'.
	 * @param boolean whether to do databinding for the item
	 * @param mixed data to be associated with the item
	 * @return TRepeaterItem the created item
	 */
	private function createItemInternal($itemIndex,$itemType,$dataBind,$dataItem)
	{
		$item=$this->createItem($itemIndex,$itemType);
		$this->initializeItem($item);
		$param=new TRepeaterItemEventParameter($item);
		if($dataBind)
		{
			$item->setDataItem($dataItem);
			$this->onItemCreated($param);
			$this->getControls()->add($item);
			$item->dataBind();
			$this->onItemDataBound($param);
			$item->setDataItem(null);
		}
		else
		{
			$this->onItemCreated($param);
			$this->getControls()->add($item);
		}
		return $item;
	}

	/**
	 * Initializes a repeater item.
	 * The item is added as a child of the repeater and the corresponding
	 * template is instantiated within the item.
	 * @param TRepeaterItem item to be initialized
	 */
	protected function initializeItem($item)
	{
		$template=null;
		switch($item->getItemType())
		{
			case TListItemType::Header: $template=$this->_headerTemplate; break;
			case TListItemType::Footer: $template=$this->_footerTemplate; break;
			case TListItemType::Item  : $template=$this->_itemTemplate; break;
			case TListItemType::Separator : $template=$this->_separatorTemplate; break;
			case TListItemType::AlternatingItem : $template=$this->_alternatingItemTemplate===null ? $this->_itemTemplate : $this->_alternatingItemTemplate; break;
		}
		if($template!==null)
			$template->instantiateIn($item);
	}

	/**
	 * Renders the repeater.
	 * This method overrides the parent implementation by rendering the body
	 * content as the whole presentation of the repeater. Outer tag is not rendered.
	 * @param THtmlWriter writer
	 */
	public function render($writer)
	{
		if($this->_items && $this->_items->getCount())
			$this->renderContents($writer);
		else if($this->_emptyTemplate!==null)
			parent::render($writer);
	}

	/**
	 * Saves item count in viewstate.
	 * This method is invoked right before control state is to be saved.
	 */
	public function saveState()
	{
		parent::saveState();
		if($this->_items)
			$this->setViewState('ItemCount',$this->_items->getCount(),0);
		else
			$this->clearViewState('ItemCount');
	}

	/**
	 * Loads item count information from viewstate.
	 * This method is invoked right after control state is loaded.
	 */
	public function loadState()
	{
		parent::loadState();
		if(!$this->getIsDataBound())
			$this->restoreItemsFromViewState();
		$this->clearViewState('ItemCount');
	}

	/**
	 * Clears up all items in the repeater.
	 */
	public function reset()
	{
		$this->getControls()->clear();
		$this->getItems()->clear();
		$this->_header=null;
		$this->_footer=null;
	}

	/**
	 * Creates repeater items based on viewstate information.
	 */
	protected function restoreItemsFromViewState()
	{
		$this->reset();
		if(($itemCount=$this->getViewState('ItemCount',0))>0)
		{
			$items=$this->getItems();
			$hasSeparator=$this->_separatorTemplate!==null;
			if($this->_headerTemplate!==null)
				$this->_header=$this->createItemInternal(-1,TListItemType::Header,false,null);
			for($i=0;$i<$itemCount;++$i)
			{
				if($hasSeparator && $i>0)
					$this->createItemInternal($i-1,TListItemType::Separator,false,null);
				$itemType=$i%2==0?TListItemType::Item : TListItemType::AlternatingItem;
				$items->add($this->createItemInternal($i,$itemType,false,null));
			}
			if($this->_footerTemplate!==null)
				$this->_footer=$this->createItemInternal(-1,TListItemType::Footer,false,null);
		}
		else if($this->_emptyTemplate!==null)
			$this->_emptyTemplate->instantiateIn($this);
		$this->clearChildState();
	}

	/**
	 * Performs databinding to populate repeater items from data source.
	 * This method is invoked by dataBind().
	 * You may override this function to provide your own way of data population.
	 * @param Traversable the data
	 */
	protected function performDataBinding($data)
	{
		$this->reset();

		$keys=$this->getDataKeys();
		$keys->clear();
		$keyField=$this->getDataKeyField();

		$items=$this->getItems();
		$itemIndex=0;
		$hasSeparator=$this->_separatorTemplate!==null;
		foreach($data as $key=>$dataItem)
		{
			if($keyField!=='')
				$keys->add($this->getDataFieldValue($dataItem,$keyField));
			else
				$keys->add($key);
			if($itemIndex===0 && $this->_headerTemplate!==null)
				$this->_header=$this->createItemInternal(-1,TListItemType::Header,true,null);
			if($hasSeparator && $itemIndex>0)
				$this->createItemInternal($itemIndex-1,TListItemType::Separator,true,null);
			$itemType=$itemIndex%2==0?TListItemType::Item : TListItemType::AlternatingItem;
			$items->add($this->createItemInternal($itemIndex,$itemType,true,$dataItem));
			$itemIndex++;
		}
		if($itemIndex>0 && $this->_footerTemplate!==null)
			$this->_footer=$this->createItemInternal(-1,TListItemType::Footer,true,null);
		if($itemIndex===0 && $this->_emptyTemplate!==null)
		{
			$this->_emptyTemplate->instantiateIn($this);
			$this->dataBindChildren();
		}
		$this->setViewState('ItemCount',$itemIndex,0);
	}

	/**
	 * This method overrides parent's implementation to handle
	 * {@link onItemCommand OnItemCommand} event which is bubbled from
	 * {@link TRepeaterItem} child controls.
	 * This method should only be used by control developers.
	 * @param TControl the sender of the event
	 * @param TEventParameter event parameter
	 * @return boolean whether the event bubbling should stop here.
	 */
	public function bubbleEvent($sender,$param)
	{
		if($param instanceof TRepeaterCommandEventParameter)
		{
			$this->onItemCommand($param);
			return true;
		}
		else
			return false;
	}

	/**
	 * Raises <b>OnItemCreated</b> event.
	 * This method is invoked after a repeater item is created and instantiated with
	 * template, but before added to the page hierarchy.
	 * The {@link TRepeaterItem} control responsible for the event
	 * can be determined from the event parameter.
	 * If you override this method, be sure to call parent's implementation
	 * so that event handlers have chance to respond to the event.
	 * @param TRepeaterItemEventParameter event parameter
	 */
	public function onItemCreated($param)
	{
		$this->raiseEvent('OnItemCreated',$this,$param);
	}

	/**
	 * Raises <b>OnItemDataBound</b> event.
	 * This method is invoked right after an item is data bound.
	 * The {@link TRepeaterItem} control responsible for the event
	 * can be determined from the event parameter.
	 * If you override this method, be sure to call parent's implementation
	 * so that event handlers have chance to respond to the event.
	 * @param TRepeaterItemEventParameter event parameter
	 */
	public function onItemDataBound($param)
	{
		$this->raiseEvent('OnItemDataBound',$this,$param);
	}

	/**
	 * Raises <b>OnItemCommand</b> event.
	 * This method is invoked after a button control in
	 * a template raises <b>Command</b> event.
	 * The {@link TRepeaterItem} control responsible for the event
	 * can be determined from the event parameter.
	 * The event parameter also contains the information about
	 * the initial sender of the <b>Command</b> event, command name
	 * and command parameter.
	 * You may override this method to provide customized event handling.
	 * Be sure to call parent's implementation so that
	 * event handlers have chance to respond to the event.
	 * @param TRepeaterCommandEventParameter event parameter
	 */
	public function onItemCommand($param)
	{
		$this->raiseEvent('OnItemCommand',$this,$param);
	}

	/**
	 * Returns the value of the data at the specified field.
	 * If data is an array, TMap or TList, the value will be returned at the index
	 * of the specified field. If the data is a component with a property named
	 * as the field name, the property value will be returned.
	 * Otherwise, an exception will be raised.
	 * @param mixed data item
	 * @param mixed field name
	 * @return mixed data value at the specified field
	 * @throws TInvalidDataValueException if the data is invalid
	 */
	protected function getDataFieldValue($data,$field)
	{
		return TDataFieldAccessor::getDataFieldValue($data,$field);
	}
}

/**
 * TRepeaterItemEventParameter class
 *
 * TRepeaterItemEventParameter encapsulates the parameter data for
 * {@link TRepeater::onItemCreated ItemCreated} event of {@link TRepeater} controls.
 * The {@link getItem Item} property indicates the repeater item related with the event.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class TRepeaterItemEventParameter extends TEventParameter
{
	/**
	 * The TRepeaterItem control responsible for the event.
	 * @var TRepeaterItem
	 */
	private $_item=null;

	/**
	 * Constructor.
	 * @param TRepeaterItem repeater item related with the corresponding event
	 */
	public function __construct(TRepeaterItem $item)
	{
		$this->_item=$item;
	}

	/**
	 * @return TRepeaterItem repeater item related with the corresponding event
	 */
	public function getItem()
	{
		return $this->_item;
	}
}

/**
 * TRepeaterCommandEventParameter class
 *
 * TRepeaterCommandEventParameter encapsulates the parameter data for
 * {@link TRepeater::onItemCommand ItemCommand} event of {@link TRepeater} controls.
 *
 * The {@link getItem Item} property indicates the repeater item related with the event.
 * The {@link getCommandSource CommandSource} refers to the control that originally
 * raises the Command event.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class TRepeaterCommandEventParameter extends TCommandEventParameter
{
	/**
	 * @var TRepeaterItem the TRepeaterItem control responsible for the event.
	 */
	private $_item=null;
	/**
	 * @var TControl the control originally raises the <b>Command</b> event.
	 */
	private $_source=null;

	/**
	 * Constructor.
	 * @param TRepeaterItem repeater item responsible for the event
	 * @param TControl original event sender
	 * @param TCommandEventParameter original event parameter
	 */
	public function __construct($item,$source,TCommandEventParameter $param)
	{
		$this->_item=$item;
		$this->_source=$source;
		parent::__construct($param->getCommandName(),$param->getCommandParameter());
	}

	/**
	 * @return TRepeaterItem the TRepeaterItem control responsible for the event.
	 */
	public function getItem()
	{
		return $this->_item;
	}

	/**
	 * @return TControl the control originally raises the <b>Command</b> event.
	 */
	public function getCommandSource()
	{
		return $this->_source;
	}
}

/**
 * TRepeaterItem class
 *
 * A TRepeaterItem control represents an item in the {@link TRepeater} control,
 * such as heading section, footer section, or a data item.
 * The index and data value of the item can be accessed via {@link getItemIndex ItemIndex}>
 * and {@link getDataItem DataItem} properties, respectively. The type of the item
 * is given by {@link getItemType ItemType} property.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class TRepeaterItem extends TControl implements INamingContainer
{
	/**
	 * index of the data item in the Items collection of repeater
	 */
	private $_itemIndex='';
	/**
	 * type of the TRepeaterItem
	 * @var TListItemType
	 */
	private $_itemType;
	/**
	 * value of the data item
	 * @var mixed
	 */
	private $_dataItem=null;

	/**
	 * Constructor.
	 * @param integer zero-based index of the item in the item collection of repeater
	 * @param TListItemType item type
	 */
	public function __construct($itemIndex,$itemType)
	{
		$this->_itemIndex=$itemIndex;
		$this->setItemType($itemType);
	}

	/**
	 * @return TListItemType item type
	 */
	public function getItemType()
	{
		return $this->_itemType;
	}

	/**
	 * @param TListItemType item type.
	 */
	public function setItemType($value)
	{
		$this->_itemType=TPropertyValue::ensureEnum($value,'TListItemType');
	}

	/**
	 * @return integer zero-based index of the item in the item collection of repeater
	 */
	public function getItemIndex()
	{
		return $this->_itemIndex;
	}

	/**
	 * @return mixed data associated with the item
	 */
	public function getDataItem()
	{
		return $this->_dataItem;
	}

	/**
	 * @param mixed data to be associated with the item
	 */
	public function setDataItem($value)
	{
		$this->_dataItem=$value;
	}

	/**
	 * This method overrides parent's implementation by wrapping event parameter
	 * for <b>Command</b> event with item information.
	 * @param TControl the sender of the event
	 * @param TEventParameter event parameter
	 * @return boolean whether the event bubbling should stop here.
	 */
	public function bubbleEvent($sender,$param)
	{
		if($param instanceof TCommandEventParameter)
		{
			$this->raiseBubbleEvent($this,new TRepeaterCommandEventParameter($this,$sender,$param));
			return true;
		}
		else
			return false;
	}
}

/**
 * TRepeaterItemCollection class.
 *
 * TRepeaterItemCollection represents a collection of repeater items.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class TRepeaterItemCollection extends TList
{
	/**
	 * Inserts an item at the specified position.
	 * This overrides the parent implementation by inserting only TRepeaterItem.
	 * @param integer the speicified position.
	 * @param mixed new item
	 * @throws TInvalidDataTypeException if the item to be inserted is not a TRepeaterItem.
	 */
	public function insertAt($index,$item)
	{
		if($item instanceof TRepeaterItem)
			parent::insertAt($index,$item);
		else
			throw new TInvalidDataTypeException('repeateritemcollection_repeateritem_required');
	}
}

?>