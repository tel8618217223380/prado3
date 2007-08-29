<?php
/**
 * TCompareValidator class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 */

/**
 * Using TBaseValidator class
 */
Prado::using('System.Web.UI.WebControls.TBaseValidator');

/**
 * TCompareValidator class
 *
 * TCompareValidator compares the value entered by the user into an input
 * control with the value entered into another input control or a constant value.
 * To compare the associated input control with another input control,
 * set the {@link setControlToCompare ControlToCompare} property to the ID path
 * of the control to compare with. To compare the associated input control with
 * a constant value, specify the constant value to compare with by setting the
 * {@link setValueToCompare ValueToCompare} property.
 *
 * The {@link setDataType DataType} property is used to specify the data type
 * of both comparison values. Both values are automatically converted to this data
 * type before the comparison operation is performed. The following value types are supported:
 * - <b>Integer</b> A 32-bit signed integer data type.
 * - <b>Float</b> A double-precision floating point number data type.
 * - <b>Currency</b> A decimal data type that can contain currency symbols.
 * - <b>Date</b> A date data type. The format follows the GNU date syntax.
 * - <b>String</b> A string data type.
 *
 * Use the {@link setOperator Operator} property to specify the type of comparison
 * to perform. Valid operators include Equal, NotEqual, GreaterThan, GreaterThanEqual,
 * LessThan and LessThanEqual.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Revision: $  $Date: $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class TCompareValidator extends TBaseValidator
{
	/**
	 * @return string the data type that the values being compared are converted to before the comparison is made. Defaults to String.
	 */
	public function getDataType()
	{
		return $this->getViewState('DataType','String');
	}

	/**
	 * Sets the data type (Integer, Float, Currency, Date, String) that the values being compared are converted to before the comparison is made.
	 * @param string the data type
	 */
	public function setDataType($value)
	{
		$this->setViewState('DataType',TPropertyValue::ensureEnum($value,'Integer','Float','Date','Currency','String'),'String');
	}

	/**
	 * @return string the input component to compare with the input control being validated.
	 */
	public function getControlToCompare()
	{
		return $this->getViewState('ControlToCompare','');
	}

	/**
	 * Sets the input component to compare with the input control being validated.
	 * @param string the ID path of the component to compare with
	 */
	public function setControlToCompare($value)
	{
		$this->setViewState('ControlToCompare',$value,'');
	}

	/**
	 * @return string the constant value to compare with the value entered by the user into the input component being validated.
	 */
	public function getValueToCompare()
	{
		return $this->getViewState('ValueToCompare','');
	}

	/**
	 * Sets the constant value to compare with the value entered by the user into the input component being validated.
	 * @param string the constant value
	 */
	public function setValueToCompare($value)
	{
		$this->setViewState('ValueToCompare',$value,'');
	}

	/**
	 * @return string the comparison operation to perform (Equal, NotEqual, GreaterThan, GreaterThanEqual, LessThan, LessThanEqual). Defaults to Equal.
	 */
	public function getOperator()
	{
		return $this->getViewState('Operator','Equal');
	}

	/**
	 * Sets the comparison operation to perform (Equal, NotEqual, GreaterThan, GreaterThanEqual, LessThan, LessThanEqual)
	 * @param string the comparison operation
	 */
	public function setOperator($value)
	{
		$this->setViewState('Operator',TPropertyValue::ensureEnum($value,'Equal','NotEqual','GreaterThan','GreaterThanEqual','LessThan','LessThanEqual'),'Equal');
	}

	/**
     * Sets the date format for a date validation
     * @param string the date format value
     */
	public function setDateFormat($value)
	{
		$this->setViewState('DateFormat', $value, '');
	}

	/**
	 * @return string the date validation date format if any
	 */
	public function getDateFormat()
	{
		return $this->getViewState('DateFormat', '');
	}

	/**
	 * This method overrides the parent's implementation.
	 * The validation succeeds if the input data compares successfully.
	 * The validation always succeeds if ControlToValidate is not specified
	 * or the input data is empty.
	 * @return boolean whether the validation succeeds
	 */
	public function evaluateIsValid()
	{
		if(($value=$this->getValidationValue($this->getValidationTarget()))==='')
			return true;

		if(($controlToCompare=$this->getControlToCompare())!=='')
		{
			if(($control2=$this->findControl($controlToCompare))===null)
				throw new TInvalidDataValueException('comparevalidator_controltocompare_invalid');
			if(($value2=$this->getValidationValue($control2))==='')
				return false;
		}
		else
			$value2=$this->getValueToCompare();

		$values = $this->getComparisonValues($value, $value2);
		switch($this->getOperator())
		{
			case 'Equal':
				return $values[0] == $values[1];
			case 'NotEqual':
				return $values[0] != $values[1];
			case 'GreaterThan':
				return $values[0] > $values[1];
			case 'GreaterThanEqual':
				return $values[0] >= $values[1];
			case 'LessThan':
				return $values[0] < $values[1];
			case 'LessThanEqual':
				return $values[0] <= $values[1];
		}

		return false;
	}

	/**
	 * Parse the pair of values into the appropriate value type.
	 * @param string value one
	 * @param string second value
	 * @return array appropriate type of the value pair, array($value1, $value2);
	 */
	protected function getComparisonValues($value1, $value2)
	{
		switch($this->getDataType())
		{
			case 'Integer':
				return array(intval($value1), intval($value2));
			case 'Float':
				return array(floatval($value1), floatval($value2));
			case 'Currency':
				if(preg_match('/[-+]?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?/',$value1,$matches))
					$value1=floatval($matches[0]);
				else
					$value1=0;
				if(preg_match('/[-+]?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?/',$value2,$matches))
					$value2=floatval($matches[0]);
				else
					$value2=0;
				return array($value1, $value2);
			case 'Date':
				$dateFormat = $this->getDateFormat();
				if($dateFormat!=='')
				{
					$formatter = Prado::createComponent('System.Data.TSimpleDateFormatter', $dateFormat);
					return array($formatter->parse($value1), $formatter->parse($value2));
				}
				else
					return array(strtotime($value1), strtotime($value2));
		}
		return array($value1, $value2);
	}

	/**
	 * Returns an array of javascript validator options.
	 * @return array javascript validator options.
	 */
	protected function getClientScriptOptions()
	{
		$options = parent::getClientScriptOptions();
		if(($name=$this->getControlToCompare())!=='')
		{
			if(($control=$this->findControl($name))!==null)
				$options['controltocompare']=$options['controlhookup']=$control->getClientID();
		}
		if(($value=$this->getValueToCompare())!=='')
			$options['valuetocompare']=$value;
		if(($operator=$this->getOperator())!=='Equal')
			$options['operator']=$operator;
		$options['type']=$this->getDataType();
		if(($dateFormat=$this->getDateFormat())!=='')
			$options['dateformat']=$dateFormat;
		return $options;
	}
}

?>