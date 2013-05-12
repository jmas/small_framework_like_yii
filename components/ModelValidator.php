<?php

/**
 * @class ModelValidator Class for validation model attributes
 * @author Maslakov Alexander <jmas.ukraine@gmail.com>
 */
class ModelValidator
{
	/**
	 * @var $_model Model instance
	 */
	protected $_model;

	/**
	 * Constructor. Receive model instance for validation.
	 * @param Model Model instance for validation
	 */
	public function __construct(Model $model)
	{
		$this->_model = $model;
	}

	/**
	 * Validate all model attributes using model rules.
	 * @throw Exception
	 * @return bool State: if all model attributes valid - return true
	 */
	public function validate()
	{
		$valid = true;
		$rules = $this->_model->validatorRules();

		if (empty($rules) === true) {
			return $valid;
		}

		foreach ($rules as $attr=>$rule) {
			$validatorName = 'validator' . ucfirst($rule[0]);
			if (method_exists($this, $validatorName) === true) {
				if (call_user_func_array(array($this, $validatorName), array($attr, $rule)) === false) {
					$valid = false;
				}
			} else {
				throw new Exception("Validator '{$validatorName}' not found.");
			}
		}

		return $valid;
	}

	/**
	 * Length validator. Receive attribute name and params for validation.
	 * Params can contain min and max properties that tell validator what string slength should be.
	 * @param $attr string Model attribute name
	 * @param $params array Validation params
	 * @return bool State: valid or not model attribute value
	 */
	public function validatorLength($attr, array $params)
	{
		$value = $this->_model->{$attr};

		if (isset($params['min']) === true && strlen($value) < $params['min']) {
			$this->_model->setError($attr, "Length not valid. Minimum {$params['min']}.");
			return false;
		}

		if (isset($params['max']) === true && strlen($value) > $params['max']) {
			$this->_model->setError($attr, "Length not valid. Maximum {$params['max']}.");
			return false;
		}

		return true;
	}

	/**
	 * Number validator. Receive attribute name and params for validation.
	 * If attribute value is number - return true.
	 * @param $attr string Model attribute name
	 * @param $params array Validation params
	 * @return bool State: valid or not model attribute value
	 */
	public function validatorNumber($attr, array $params)
	{
		if (is_numeric($this->_model->{$attr}) === false) {
			$this->_model->setError($attr, 'Should be an number.');
			return false;
		}

		return true;
	}

	/**
	 * Required validator. Receive attribute name and params for validation.
	 * If attribute value not empty - return true.
	 * @param $attr string Model attribute name
	 * @param $params array Validation params
	 * @return bool State: valid or not model attribute value
	 */
	public function validatorRequired($attr, array $params)
	{
		if (empty($this->_model->{$attr}) === true) {
			$this->_model->setError($attr, 'Required. Should contain some data.');
			return false;
		}

		return true;
	}
}
