<?php
/**
 * Auto generated by prado-cli.php on 2007-05-01 05:32:34.
 */
class Region extends TActiveRecord
{
	const TABLE='Region';

	public $RegionID;
	public $RegionDescription;

	//comment out the following line to use lazy loading
	//public $Territories=array();

	public static $RELATIONS = array
	(
		'Territories' => array(self::HAS_MANY, 'Territory')
	);

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>