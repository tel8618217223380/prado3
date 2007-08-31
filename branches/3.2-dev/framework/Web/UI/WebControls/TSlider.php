<?php
/**
 * TSlider class file.
 *
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package System.Web.UI.WebControls
 */

/**
 * TSlider class
 *
 * TODO: Class Documentation
 * 
 * Quick ex :
 * 
 * <com:TSlider id="hSlider" AutoPostBack="true" HandleType="Image" Axis="Horizontal" Enabled="true" Width="200px" Minimum="0" Maximum="100" OnSliderChanged="sliderChanged">
 *	<prop:ClientSide.onSlide>
 * 		$('hslidervalue').innerHTML = value;
 * 	</prop:ClientSide.onSlide>
 * </com:TSlider>
 * Value  : <span id="hslidervalue"><%=$this->slider->value%></span>
 * 
 * Properties :
 * AutoPostBack=true ==> postback occured if slider changes.
 * HandleType ==> "Cursor" or "Image" or "Custom"
 * 		"Cursor" : display a simple cursor as handle on the track (see TSliderHandleCursor)
 * 		"Image" : display an image as handle on the track (see TSliderHandleImage)
 * 		"Custom" : Use a custom control as handle (extends from TSliderHandle, and specify with HandleClass Property)
 * Axis ==> Horizontal or Vertical
 * Width ==> Width of track
 * Height ==> Height of track
 * Minimum & Maximum => min & max values
 * Values ==> Allowed values (default = all values between Minimum and Maximum)
 * 
 * Handle.* ==> Properties for handle (widht, height, color for Cursor, Width, Height, ImageUrl for Image)
 * 
 * Events :
 * ClientSide.onSlide : JS code to be executed when cursor is slided
 * ClientSide.onChange : JS code to be executed when the value has changed
 * onSliderChanged : Serverside Event raised on postback when value has changed.
 * 
 * 
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @version $Id$
 * @package System.Web.UI.WebControls
 * @since 3.1.1
 */
class TSlider extends TWebControl implements IPostBackDataHandler
{

	/**
	 * @var TSliderHandle handle component
	 */
	private $_handle;
	/**
	 * @var string Custom handle class name
	 */
	private $_handleClass;
	
	private $_dataChanged=false;
	private $_clientScript=null;
	
	public function getAxis() 
	{ 
		return $this->getViewState('axis');
	}
	
	public function setAxis($v) 
	{
		$this->setViewState('axis', TPropertyValue::ensureEnum($v,TSliderAxisType));
	}

	public function getIncrement() 
	{ 
		return $this->getViewState('increment',1);
	}
	public function setIncrement($v) 
	{ 
		$this->setViewState('increment', TPropertyValue::ensureInteger($v),1);
	}

	public function getMaximum() 
	{ 
		return $this->getViewState('maximum',100);
	}
	
	public function setMaximum($v) 
	{ 
		$this->setViewState('maximum', TPropertyValue::ensureInteger($v),100);
	}

	public function getMinimum() 
	{ 
		return $this->getViewState('minimum',0);
	}
	
	public function setMinimum($v) 
	{ 
		$this->setViewState('minimum', TPropertyValue::ensureInteger($v),0);
	}

	public function getAlignX() 
	{ 
		return $this->getViewState('alignx');
	}
	
	public function setAlignX($v) 
	{ 
		$this->setViewState('alignx', $v);
	}

	public function getAlignY() 
	{ 
		return $this->getViewState('aligny');
	}
	
	public function setAlignY($v) 
	{ 
		$this->setViewState('aligny', $v);
	}

	public function getValue() 
	{ 
		return $this->getViewState('value',0);
	}
	
	public function setValue($v) 
	{ 
		$this->setViewState('value', TPropertyValue::ensureFloat($v),0);
	}

	public function getValues() 
	{ 
		return $this->getViewState('values');
	}
	
	public function setValues($v) 
	{ 
		$this->setViewState('values', TPropertyValue::ensureArray($v));
	}

	public function getHandle ()
	{
		if ($this->_handle==null)
		{
			if (($type=$this->getHandleType()) != TSliderHandleType::Custom) 
				$class='TSliderHandle'.$type;
			else
				$class=$this->getHandleClass();
			$this->_handle=prado::createComponent($class, $this);
			if (!$this->_handle instanceof TSliderHandle)
			{
				throw new TInvalidDataTypeException('slider_handle_class_invalid', get_class($this->_handle));
			}
		}
		return $this->_handle;
	}
	
	public function getHandleType ()
	{
		return $this->getViewState('handleType', TSliderHandleType::Cursor);
	}
	
	public function setHandleType ($value)
	{
		$this->setViewState ('handleType', TPropertyValue::ensureEnum($value, TSliderHandleType));
	}
	
	public function getHandleClass ()
	{
		return $this->_handleClass?$this->_handleClass:TSliderHandleCursor;
	}
	
	public function setHandleClass ($value)
	{
		$handle=prado::createComponent($value, $this);
		if ($handle instanceof TSliderHandle)
		{
			$this->_handleClass=$value;
			$this->_handle=$handle;
		} else {
			throw new TInvalidDataTypeException('slider_handle_class_invalid', get_class($this->_handle));
		}
	}
	
	/**
	 * @return boolean a value indicating whether an automatic postback to the server
     * will occur whenever the user modifies the text in the TTextBox control and
     * then tabs out of the component. Defaults to false.
	 */
	public function getAutoPostBack()
	{
		return $this->getViewState('AutoPostBack',false);
	}

	/**
	 * Sets the value indicating if postback automatically.
	 * An automatic postback to the server will occur whenever the user
	 * modifies the text in the TTextBox control and then tabs out of the component.
	 * @param boolean the value indicating if postback automatically
	 */
	public function setAutoPostBack($value)
	{
		$this->setViewState('AutoPostBack',TPropertyValue::ensureBoolean($value),false);
	}
	
	
	/**
	 * Creates a style object to be used by the control.
	 * This method override parent implementation to provide some default values to slider style
	 * @return TStyle the default style created for TSlider
	 */
	protected function createStyle ()
	{
		$style=new TStyle();
		$style->setWidth($this->Axis == 'Vertical'?'5px':'100px');
		$style->setHeight($this->Axis == 'Vertical'?'100px':'5px');
		$style->setBackColor('rgb(170,170,170)');
		$style->setStyleField('padding', '0');
		$style->setStyleField('margin', '0');
		return $style;
	}
	

	
	/**
	 * Gets the name of the javascript class responsible for performing postback for this control.
	 * This method overrides the parent implementation.
	 * @return string the javascript class name
	 */
	protected function getClientClassName()
	{
		return 'Prado.WebUI.TSlider';
	}


	/**
	 * Returns a value indicating whether postback has caused the control data change.
	 * This method is required by the IPostBackDataHandler interface.
	 * @return boolean whether postback has caused the control data change. False if the page is not in postback mode.
	 */
	public function getDataChanged()
	{
		return $this->_dataChanged;
	}
	
	/**
	 * Raises postdata changed event.
	 * This method is required by {@link IPostBackDataHandler} interface.
	 * It is invoked by the framework when {@link getValue Value} property
	 * is changed on postback.
	 * This method is primarly used by framework developers.
	 */
	public function raisePostDataChangedEvent()
	{
		$this->onSliderChanged(null);
	}
	
	/**
	 * Raises <b>OnSliderChanged</b> event.
	 * This method is invoked when the {@link getValue Value}
	 * property changes on postback.
	 * If you override this method, be sure to call the parent implementation to ensure
	 * the invocation of the attached event handlers.
	 * @param TEventParameter event parameter to be passed to the event handlers
	 */
	public function onSliderChanged($param)
	{
		$this->raiseEvent('OnSliderChanged',$this,$param);
	}
	
	/**
	 * Loads user input data.
	 * This method is primarly used by framework developers.
	 * @param string the key that can be used to retrieve data from the input data collection
	 * @param array the input data collection
	 * @return boolean whether the data of the component has been changed
	 */
	public function loadPostData($key,$values)
	{
		$value=$values[$this->getClientID().'_1'];
		if($this->getValue()!==$value)
		{
			$this->setValue($value);
			return $this->_dataChanged=true;
		}
		else
			return false;
	}
	
	/**
	 * Gets the TSliderClientScript to set the TSlider event handlers.
	 *
	 * The slider on the client-side supports the following events.
	 * # <tt>OnSliderMove</tt> -- raised when the slider is moved.
	 * # <tt>OnSliderChanged</tt> -- raised when the slider value is changed
	 *
	 * You can attach custom javascript code to each of these events
	 *
	 * @return TSliderClientScript javascript validator event options.
	 */
	public function getClientSide()
	{
		if(is_null($this->_clientScript))
			$this->_clientScript = $this->createClientScript();
		return $this->_clientScript;
	}

	/**
	 * @return TDatePickerClientScript javascript validator event options.
	 */
	protected function createClientScript()
	{
		return new TSliderClientScript;
	}
	
	public function getTagName ()
	{
		return "div";
	}
	
	/**
	 * Renders body content.
	 * This method renders the handle of slider
	 * This method overrides parent implementation 
	 * @param THtmlWriter writer
	 */
	public function renderContents($writer)
	{
		// Render the handle
		$this->getHandle()->render ($writer);
		
	}
	
	/**
	 * Add the differents style properties to slider track, and register the client scripts.
	 * @param THtmlWriter writer
	 */
	protected function addAttributesToRender($writer)
	{
		parent::addAttributesToRender($writer);
		$writer->addAttribute('id',$this->getClientID());
		// If style has not been rendered yet, do it.
		if (! $this->getHasStyle()) $this->getStyle()->addAttributesToRender($writer);
	}	
	
	public function onPreRender ($param)
	{
		parent::onPreRender($param);
		$this->registerSliderClientScript();
		
	}
	
	/**
	 * Registers the javascript code to initialize the slider.
	 */
	protected function registerSliderClientScript()
	{

		$cs = $this->getPage()->getClientScript();
		$cs->registerPradoScript("slider");
		$id=$this->getClientID();
		$page=$this->getPage();
		$cs->registerHiddenField($id.'_1',$this->getValue());
		$page->registerRequiresPostData($this);
		$cs->registerPostBackControl($this->getClientClassName(),$this->getSliderOptions());
	}
	
	/**
	 * Get javascript sliderr options.
	 * @return array slider client-side options
	 */
	protected function getSliderOptions()
	{
		// PostBack Options :
		$options['ID'] = $this->getClientID();
		$options['EventTarget'] = $this->getUniqueID();
		$options['AutoPostBack'] = $this->getAutoPostBack();
		
		// Slider Control options
		$options['axis'] = strtolower($this->getAxis());
		$options['increment'] = $this->getIncrement();
		$options['step'] = $this->getIncrement();
		$options['alignX'] = $this->getAlignX();
		$options['alignY'] = $this->getAlignY();
		$options['maximum'] = $this->getMaximum();
		$options['minimum'] = $this->getMinimum();
		$options['range'] = 'javascript:$R('.$this->getMinimum().",".$this->getMaximum().")";
		$options['sliderValue'] = $this->getValue();
		$options['disabled'] = !$this->getEnabled(); 
		if (($values=$this->getValues()))
			$options['values'] = TJavascript::Encode($values,false);
		
					
		if(!is_null($this->_clientScript))
			$options = array_merge($options,
				$this->_clientScript->getOptions()->toArray());
		return $options;
	}
}

/**
 * TSliderClientScript class.
 *
 * Client-side slider events {@link setOnChange OnChange} and {@line setOnMove OnMove}
 * can be modified through the {@link TSlider:: getClientSide ClientSide}
 * property of a slider.
 * 
 * The current value of the slider can be get in the 'value' js variable
 * 
 * The <tt>OnMove</tt> event is raised when the slider moves
 * The <tt>OnChange</tt> event is raised when the slider value is changed (or at the end of a move)
 *
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @version $Id$
 * @package System.Web.UI.WebControls
 * @since 3.1.1
 */
class TSliderClientScript extends TClientSideOptions
{
	/**
	 * Javascript code to execute when the slider value is changed.
	 * @param string javascript code
	 */
	public function setOnChange($javascript)
	{
		$code="javascript: function (value) { {$javascript} }";
		$this->setFunction('onChange', $code);
	}

	/**
	 * @return string javascript code to execute when the slider value is changed.
	 */
	public function getOnChanged()
	{
		return $this->getOption('onChange');
	}
	
 	/* Javascript code to execute when the slider moves.
	 * @param string javascript code
	 */
	public function setOnSlide($javascript)
	{
		$code="javascript: function (value) { {$javascript} }";
		$this->setFunction('onSlide', $code);
	}

	/**
	 * @return string javascript code to execute when the slider moves.
	 */
	public function getOnSlide()
	{
		return $this->getOption('onSlide');
	}
}


class TSliderAxisType extends TEnumerable
{
	const Horizontal='Horizontal';
	const Vertical='Vertical';
}

class TSliderHandleType extends TEnumerable
{
	const Image='Image';
	const Cursor='Cursor';
	const Custom='Custom';
}

/**
 * TSliderHandle abstract class
 * 
 * Base class for slider handles. All custom handles must derive from this class.
 *
 */
abstract class TSliderHandle extends TWebControl
{
	private $_track;
	
	public function __construct ($track)
	{
		if ($track instanceof TSlider)
		{
			$this->_track=$track;	
		} else {
			throw new TInvalidDataTypeException ('slider_track_class_invalid', get_class($this));
		}
	}
	
	public function getTrack() {
		return $this->_track;
	}
	
	public function getTagName()
	{
		return 'div';
	}
	
	public function setWidth($v)
	{
		parent::setWidth(TPropertyValue::ensureInteger($v).'px');
	}
	
	public function setHeight($v)
	{
		parent::setHeight(TPropertyValue::ensureInteger($v).'px');
	}
	
	
	public function getRelativeX() 
	{ 
		return $this->getStyle()->getStyleField('left');
	}
	
	public function setRelativeX($v) 
	{ 
		$this->getStyle()->setStyleField('left', TPropertyValue::ensureInteger($v)."px");
	}

	public function getRelativeY() 
	{ 
		return $this->getStyle()->getStyleField('top');
	}
	
	public function setRelativeY($v) 
	{ 
		$this->getStyle()->setStyleField('top', TPropertyValue::ensureInteger($v)."px");
	}
	
	public function addAttributesToRender($writer)
	{
		parent::addAttributesToRender($writer);
		$writer->addAttribute('id', $this->getTrack()->getClientID()."_handle");
		if (! $this->getHasStyle()) $this->getStyle()->addAttributesToRender($writer);
	}
	
	
}

/**
 * TSliderHandleCursor class
 * 
 * Draw a simple block cursor as a handle.
 * Cursor size can be set with {@link setWidth Width} and {@link setHeight Height} properties.
 * Its color can be set with {@link setColor Color} property
 *
 */
class TSliderHandleCursor extends TSliderHandle
{
	
	public function getColor ()
	{
		return $this->getStyle()->getBackColor();
	}
	
	public function setColor ($value)
	{
		$this->getStyle()->setBackColor($value);
	}
	
	/**
	 * Creates a style object to be used by the control.
	 * This method override parent implementation to provide some default values to handle style
	 * @return TStyle the default style created for TSlider
	 */
	protected function createStyle ()
	{
		$style=new TStyle();
		$style->setWidth($this->getTrack()->getAxis()==TSliderAxisType::Horizontal?'5px':'10px');
		$style->setHeight($this->getTrack()->getAxis()==TSliderAxisType::Horizontal?'10px':'5px');
		$style->setBackColor('rgb(255,0,0)');
		/* cursor: ew-resize & ns-resize don't work in IE... stay with the 'move' cursor for now */
		// $style->setStyleField('cursor',$this->getTrack()->getEnabled()?TSliderAxisType::Horizontal?'ew-resize':'ns-resize':'not-allowed');
		$style->setStyleField('cursor',$this->getTrack()->getEnabled()?'move':'not-allowed');
		return $style;
	}
	
}

/**
 * TSliderHandleImage class
 * 
 * Use an image for the slider handle.
 * Handle size sould be set to the image size with {@link setWidth Width} and {@link setHeight Height} properties.
 * {@link SetImageUrl ImageUrl} property set the url of the image
 * 
 * TODO Handle DisabledImageUrl
 *
 */
class TSliderHandleImage extends TSliderHandle
{
	const DEFAULTIMAGE='TSliderHandle.png';
	const DEFAULTIMAGEWIDTH='14';
	const DEFAULTIMAGEHEIGHT='16';
	
	public function getImageUrl ()
	{
		return $this->getViewState('hImageUrl', $this->publishFilePath(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.self::DEFAULTIMAGE, __CLASS__));
	}
	
	public function setImageUrl ($value)
	{
		$this->setViewState('hImageUrl', $value);
	}
	
	protected function createStyle ()
	{
		$style=new TStyle();
		// Our default image is 14x15px
		$style->setWidth(self::DEFAULTIMAGEWIDTH.'px');
		$style->setHeight(self::DEFAULTIMAGEHEIGHT.'px');
		// Default relative position
		if ($this->getTrack()->getAxis()==TSliderAxisType::Horizontal)
			$style->setStyleField('top', ceil((intval($this->getTrack()->getHeight()-self::DEFAULTIMAGEHEIGHT))/2).'px');
		else 
			$style->setStyleField('left', ceil((intval($this->getTrack()->getWidth()-self::DEFAULTIMAGEWIDTH))/2).'px');
		$style->setStyleField('cursor',$this->getTrack()->getEnabled()?'move':'not-allowed');
		$style->setStyleField('background-image', 'url('.$this->getImageUrl().')');
		$style->setStyleField('background-repeat', 'no-repeat');
		return $style;
	}
	
	public function renderContents ($writer)
	{
	/*	$writer->addAttribute('style', 'float: left');
		$writer->addAttribute('alt', '');
		$writer->addAttribute('src', $this->getImageUrl());
		$writer->renderBeginTag('img');
		$writer->renderEndTag();*/
	}
}

?>