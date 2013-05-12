<?php

/**
 * @class Address Model that contain address information and methods for it changing
 * @author Maslakov Alexander <jmas.ukraine@gmail.com>
 */
class Address extends Model
{
	public $ADDRESSID;
        public $LABEL;
        public $STREET;
        public $HOUSENUMBER;
        public $POSTALCODE;
        public $CITY;
        public $COUNTRY;

	/**
	 * Information about table name for Model class.
	 * @return string Table name
	 */
	public function tableName() { return 'ADDRESS'; }

	/**
	 * Information about PK column name for Model class.
	 * @return string PK column name
	 */
	public function pkColumnName() { return 'ADDRESSID'; }

	/**
	 * Rules for model attributes validation
	 * @return array Model validation rules
	 */
	public function validatorRules()
	{
		return array(
			'LABEL'=>array('length', 'max'=>100),
			'LABEL'=>array('required'),
			'STREET'=>array('length', 'max'=>100),
			'HOUSENUMBER'=>array('length', 'max'=>10),
			'HOUSENUMBER'=>array('number'),
			'POSTALCODE'=>array('length', 'max'=>6),
			'POSTALCODE'=>array('number'),
			'CITY'=>array('length', 'max'=>100),
			'COUNTRY'=>array('length', 'max'=>100),
		);
	}
}
