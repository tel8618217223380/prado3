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
 * CssClass => Track Css Class. Defaults to 'slider'. The classname is appended with -horitontal or -vertical depending on the Direction.
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
	/**
	 * @var boolean Wether the data has changed during postback
	 */
	private $_dataChanged=false;
	/**
	 * @var TSliderClientScript Clients side javascripts
	 */
	private $_clientScript=null;

	/**
	 * @return TSliderDirection Direction of slider (Horizontal or Vertical)
	 */
	public function getDirection()
	{
		return $this->getViewState('Direction', TSliderDirection::Horizontal);
	}

	/**
	 * @param TSliderDirection Direction of slider (Horizontal or Vertical)
	 */
	public function setDirection($value)
	{
		$this->setViewState('Direction', TPropertyValue::ensureEnum($value,TSliderDirection));
	}

	/**
	 * @return string URL for the CSS file including all relevant CSS class definitions. Defaults to ''.
	 */
	public function getCssUrl()
	{
		return $this->getViewState('CssUrl','');
	}

	/**
	 * @param string URL for the CSS file including all relevant CSS class definitions.
	 */
	public function setCssUrl($value)
	{
		$this->setViewState('CssUrl',TPropertyValue::ensureString($value),'');
	}

	/**
	 * @return string CssClass for an horizontal track of the slider control. Defaults to 'hslider'
	 */
	public function getHorizontalCssClass ()
	{
		return $this->getViewState('HorizontalCssClass', 'hslider');
	}

	/**
	 * @param string CssClass for an horizontal track of the slider control. Defaults to 'hslider'
	 */
	public function setHorizontalCssClass ($value)
	{
		$this->setViewState('HorizontalCssClass', $value, 'hslider');
	}

	/**
	 * @return string CssClass for a vertical track of the slider control. Defaults to 'slider'
	 */
	public function getVerticalCssClass ()
	{
		return $this->getViewState('VerticalCssClass', 'vslider');
	}

	/**
	 * @param string CssClass for a vertical track of the slider control. Defaults to 'vslider'
	 */
	public function setVerticalCssClass ($value)
	{
		$this->setViewState('VerticalCssClass', $value, 'vslider');
	}

	/**
	 * @return int Maximum value for the slider
	 */
	public function getMaxValue()
	{
		return $this->getViewState('MaxValue',100);
	}

	/**
	 * @param int Maximum value for slider
	 */
	public function setMaxValue($value)
	{
		$value=TPropertyValue::ensureInteger($value);
		// Should I ensure the values are the same than the allowed values ?
		/*
		if (($values=$this->getValues())!==null && $value != max($values)) $value=max($values);
		*/
		$this->setViewState('MaxValue', $value,100);
	}

	/**
	 * @return int Minimum value for slider
	 */
	public function getMinValue()
	{
		return $this->getViewState('MinValue',0);
	}

	/**
	 * @param int Minimum value for slider
	 */
	public function setMinValue($value)
	{
		$value=TPropertyValue::ensureInteger($value);
		// Should I ensure the values are the same than the allowed values ?
		/*
		if (($values=$this->getValues())!==null && $value != min($values)) $value=min($values);
		*/
		$this->setViewState('MinValue', $value,0);
	}

	/**
	 * @return float current value of slider
	 */
	public function getValue()
	{
		return $this->getViewState('Value',0);
	}

	/**
	 * @param float current value of slider
	 */
	public function setValue($value)
	{
		$this->setViewState('Value', TPropertyValue::ensureFloat($value),0);
	}

	/**
	 * @return array list of allowed values the slider can take
	 */
	public function getValues()
	{
		return $this->getViewState('Values', null);
	}

	/**
	 * @param array list of allowed values the slider can take
	 */
	public function setValues($value)
	{
		$value=TPropertyValue::ensureArray($value);
		$this->setViewState('Values', $value, null);
		// Should I ensure Min/MaxValue are correct ???
		/* 
		if ($this->getMinValue() < min($value)) $this->setMinValue(min($value));
		if ($this->getMaxValue() > max($value)) $this->setMaxValue(max($value));
		*/ 
	}

	/**
	 * @return int number of decimals for the value. Defaults to 0
	 */
	public function getDecimals()
	{
		return $this->getViewState("Decimals", 0);
	}

	/**
	 * @param int number of decimas for the value. Defaults to 0.
	 */
	public function setDecimals($value)
	{
		$this->setViewState ('Decimals', TPropertyValue::ensureInteger($value), 0);
	}

	/**
	 * This method will return the handle control.
	 * @return TSliderHandle the control for the slider's handle (must inherit from TSliderHandle}
	 */
	public function getHandle ()
	{
		if ($this->_handle==null)
		{
			$this->_handle=prado::createComponent($this->getHandleClass(), $this);
			if (!$this->_handle instanceof TSliderHandle)
			{
				throw new TInvalidDataTypeException('slider_handle_class_invalid', get_class($this->_handle));
			}
		}
		return $this->_handle;
	}


	/**
	 * @return string Custom handle class. Defaults to TSliderHandle;
	 */
	public function getHandleClass ()
	{
		return $this->_handleClass?$this->_handleClass:TSliderHandle;
	}

	/**
	 * Set custom handle class. This class must exists, and be an instance of TSliderHandle
	 *
	 * @param string Custom Handle Class
	 */
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
	 * will occur whenever the user modifies the slider value. Defaults to false.
	 */
	public function getAutoPostBack()
	{
		return $this->getViewState('AutoPostBack',false);
	}

	/**
	 * Sets the value indicating if postback automatically.
	 * An automatic postback to the server will occur whenever the user
	 * modifies the slider value.
	 * @param boolean the value indicating if postback automatically
	 */
	public function setAutoPostBack($value)
	{
		$this->setViewState('AutoPostBack',TPropertyValue::ensureBoolean($value),false);
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
		if ($this->getDataChanged()) $this->raiseEvent('OnSliderChanged',$this,$param);
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
	 * @return TSliderClientScript javascript event options.
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
	 * Add the specified css classes to the track
	 * @param THtmlWriter writer
	 */
	protected function addAttributesToRender($writer)
	{

		$writer->addAttribute('id',$this->getClientID());
		$class=($this->getDirection()==TSliderDirection::Horizontal)?$this->getHorizontalCssClass():$this->getVerticalCssClass();
		$writer->addAttribute('class', $class);
		parent::addAttributesToRender($writer);
	}

	/**
	 * Registers CSS and JS.
	 * This method is invoked right before the control rendering, if the control is visible.
	 * @param mixed event parameter
	 */
	public function onPreRender ($param)
	{
		parent::onPreRender($param);
		$this->registerStyleSheet();
		$this->registerSliderClientScript();

	}

	/**
	 * Registers the CSS relevant to the TSlider.
	 * It will register the CSS file specified by {@link getCssUrl CssUrl}.
	 * If that is not set, it will use the default CSS.
	 */
	protected function registerStyleSheet()
	{
		if(($url=$this->getCssUrl())==='')
		$url=$this->getApplication()->getAssetManager()->publishFilePath(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'TSlider.css');
		$this->getPage()->getClientScript()->registerStyleSheetFile($url,$url);
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
		$options['axis'] = strtolower($this->getDirection());
		$options['maximum'] = $this->getMaxValue();
		$options['minimum'] = $this->getMinValue();
		$options['range'] = 'javascript:$R('.$this->getMinValue().",".$this->getMaxValue().")";
		$options['sliderValue'] = $this->getValue();
		$options['disabled'] = !$this->getEnabled();
		if (($values=$this->getValues()))
		$options['values'] = TJavascript::Encode($values,false);
		$options['decimals'] = $this->getDecimals();
			
		if(!is_null($this->_clientScript))
			$options = array_merge($options,$this->_clientScript->getOptions()->toArray());
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


/**
 * TSliderDirection class.
 * 
 * TSliderDirection defines the enumerable type for the possible direction that can be used in a {@link TSlider}
 * 
 * The following enumerable values are defined :
 * - Horizontal : Horizontal slider
 * - Vertical : Vertical slider
 * 
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @version $Id$
 * @package System.Web.UI.WebControls
 * @since 3.1.1
 */
class TSliderDirection extends TEnumerable
{
	const Horizontal='Horizontal';
	const Vertical='Vertical';
}


/**
 * TSliderHandle class
 *
 * TSliderHandle is responsible of rendering the 'handle' control on a {@link TSlider}
 * Users can override this class to personalize the handle.
 * Default class renders a 'div' tag, and apply the css class provided by {@link setCssClass CssClass} property.
 * 
 * Two css classes are provided by default : 
 * - handle : render a simple red cursor
 * - handle-image : render an image as handle
 * 
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @version $Id$
 * @package System.Web.UI.WebControls
 * @since 3.1.1
 */
class TSliderHandle extends TWebControl
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

	/**
	 * @return string CssClass for the handle of the slider control. Defaults to 'handle'
	 */
	public function getCssClass ()
	{
		$class=parent::getCssClass();
		return ($class=='')?'handle':$class;
	}

	public function addAttributesToRender($writer)
	{
		parent::addAttributesToRender($writer);
		$writer->addAttribute('id', $this->getTrack()->getClientID()."_handle");
		$writer->addAttribute('class', $this->getCssClass());
	}


}


?>