<?php

/**
 * @class Model Base class for all application models
 * @author Maslakov Alexander <jmas.ukraine@gmail.com>
 */
abstract class Model
{
	/**
	 * @var $_db Db Instance for comfortably working with DB
	 */
	protected $_db;

	/**
	 * @var $_errors array Contain result of model validation errors
	 */
	protected $_errors=array();

	/**
	 * Get Db class and store to $_db property
	 * @return Db Instance of Db class
	 */
	public function getDb()
	{
		if ($this->_db === null) {
			$this->_db = App::getInstance()->db;
		}

		return $this->_db;
	}

	/**
	 * Should return table name for this model
	 * @return string Table name
	 */
	abstract function tableName();

	/**
	 * Should return PK column name for this model
	 * @return string PK column name
	 */
	abstract function pkColumnName();

	/**
	 * Rules for model attributes validation
	 * @return array Model validation rules
	 */
	abstract function validatorRules();


	/**
	 * Save model data.
	 * If model is new PK is null - we generate INSERT SQL request.
	 * If model data already exists in DB, PK not null - we generate UPDATE request.
	 * Method return true if model data saved successfully. False if error.
	 * @param $validate bool Option that run validation before save
	 * @return bool Saving status
	 */
	public function save($validate=true)
	{
		if ($validate === true && $this->validate() === false) {
			return false;
		}

		$columns = $this->getClearColumns();

		$values = array();

		foreach ($columns as $column) {
			$values[] = $this->{$column};
		}

		$keyPosition = array_search($this->pkColumnName(), $columns);
		array_splice($columns, $keyPosition, 1);
		array_splice($values, $keyPosition, 1);

		if ($this->isNew()) {
			$sql = 'INSERT INTO ' . $this->tableName()
					. ' (' . join(',', $columns) . ') '
					. 'VALUES (' . join(',', array_fill(0, count($columns), '?')) . ')';
		} else {
			$sql = 'UPDATE ' . $this->tableName() . ' SET '
					. join(',', array_map(array($this, 'makeBindedColumn'), $columns))
					. ' WHERE ' . $this->pkColumnName() . '=? LIMIT 1';

			$values[] = $this->{$this->pkColumnName()};
		}

		$values = array_map('strval', $values);

		$sth = $this->getDb()->prepare($sql);

		if ($sth->execute($values) === true) {
			if ($this->isNew()) {
				$this->{$this->pkColumnName()} = $this->getDb()->lastInsertId();
			}
			return true;
		}

		return false;
	}

	/**
	 * Check model status.
	 * If model new PK is 1 - return true.
	 * If model data already exists in DB, PK not null - return false.
	 * @return bool Model status
	 */
	public function isNew()
	{
		return $this->{$this->pkColumnName()} === null ? true: false;
	}

	/**
	 * Fill model with DB record data that has PK = $pk.
	 * @param $pk integer PK of DB record
	 * @return bool Status: if model founded = true
	 */
	public function findByPk($pk)
	{	
		$sql = 'SELECT * FROM ' . $this->tableName() . ' WHERE ' . $this->pkColumnName() . '=? LIMIT 1';
		$sth = $this->getDb()->prepare($sql);

		$sth->execute(array($pk));
		
		if (($attrs = $sth->fetch(PDO::FETCH_ASSOC)) !== false) {
			$this->setAttributes($attrs, false);
			return true;
		}

		return false;
	}

	/**
	 * Find all DB records and generate array with models that filled by records data.
	 * Example:
	 * $model->findAll('name=?', array($name), 5);
	 * Return:
	 * array(Model, Model, Model, Model, Model)
	 * @param $where string SQL WHERE instructions
	 * @param $params array Params for $where instructions
	 * @limit $limit string|null SQL LIMIT instructions
	 * @return array Array of models
	 */
	public function findAll($where='1=1', array $params=array(), $limit=null)
	{
		$sql = 'SELECT * FROM ' . $this->tableName() . ' WHERE ' . $where . ' ' . ($limit !== null ? ' LIMIT ' . $limit: '');
		$sth = $this->getDb()->prepare($sql);

		$models = array();

		if ($sth->execute($params)) {
			while (($model = $sth->fetchObject(get_class($this))) !== false) {
				$models[] = $model;
			}
		}

		return $models;
	}

	/**
	 * Delete current model data from DB.
	 * @return bool Delete status, if deleted return true
	 */
	public function delete()
	{
		$sql = 'DELETE FROM ' . $this->tableName() . ' WHERE ' . $this->pkColumnName() . '=? LIMIT 1';
		$sth = $this->getDb()->prepare($sql);
		
		if ($sth->execute(array($this->{$this->pkColumnName()})) === true) {
			unset($this->{$this->pkColumnName()});
			return true;
		}

		return false;
	}

	/**
	 * Model validation.
	 * return bool State: if all model attributes valid - return true
	 */
	public function validate()
	{
		$validator = new ModelValidator($this);
		return $validator->validate();
	}

	/**
	 * Return array of attributes of this model.
	 * Contain only clear attributes.
	 * @return array Attributes of this model
	 */
	public function getAttributes()
	{
		$columns = $this->getClearColumns();

		$result = array();

		foreach ($columns as $column) {
			$result[$column] = $this->{$column};
		}

		return $result;
	}

	/**
	 * Set attributes for this model.
	 * @param $attrs array Array of attributes key=>value
	 * @param $excludePk bool If you want change PK - set true
	 * @return null
	 */
	public function setAttributes(array $attrs, $excludePk=true)
	{
		$columns = $this->getClearColumns();

		foreach ($columns as $column) {
			if ($excludePk === true && $column === $this->pkColumnName()) {
				continue;
			}

			if (isset($attrs[$column])) {
				$this->{$column} = $attrs[$column];
			}
		}
	}

	/**
	 * Set model error.
	 * @param $attr string Attribute name
	 * @error $error string Error description
	 * @return null
	 */
	public function setError($attr, $error)
	{
		$this->_errors[$attr] = $error;
	}

	/**
	 * Get model errors.
	 * @return array Model errors
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Special method for get "clear" attributes of this model.
	 * Clear attributes is all public attributes of this class.
	 * @return array Array of clear attributes
	 */
	private function getClearColumns()
	{
		$dirtyColumns = array_keys(get_class_vars(get_class($this)));
		$columns = array_filter($dirtyColumns, array($this, 'dirtyColumnsFilter'));

		return $columns;
	}

	/**
	 * Special protected method that used in array_filter function.
	 * This method get $column and check first char.
	 * If char is "_" this column exclude from array.
	 * @param $column string Name of column
	 * @return bool Status: if column has "_" as first char - return false
	 */
	private function dirtyColumnsFilter($column)
	{
		if ($column[0] === '_') {
			return false;
		}

		return true;
	}

	/**
	 * Special method that used in array_map for adding "=?"
	 * to each element of array.
	 * This function need for create PDO placeholders.
	 * @param $value string Column name
	 * @return string Binded value
	 */
	private function makeBindedColumn($column)
	{
		return $column . '=?';
	}
}
