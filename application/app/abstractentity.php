<?php
namespace App;

abstract class AbstractEntity implements \Iterator
{
	protected $_values = array();
	protected $_allowedFields = array();

	/* Error handling */
	private $_errors = array();

	protected $sName;
	protected $validation;

	public function __construct()
	{
		$this->validation = new \Lib\Validation();
	}

	public function hasError()
	{
		return sizeof($this->_errors > 0) ? $this->getErrors() : false;
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	public function setError($errorText)
	{
		$this->_errors[] = $errorText;
	}

	/**
	 * Assign a value to the specified field via the corresponding mutator (if it exists);
	 * otherwise, assign the value directly to the '$_values' protected array
	 */
	public function __set($name, $value)
	{
		if (!in_array($name, $this->_allowedFields) && $name != 'relevance') {
			throw new \Exception('The field ' . $name . ' is not allowed for this entity ('.get_called_class().').');
		}
		$mutator = 'set' . ucfirst($name);

		if (method_exists($this, $mutator) && is_callable(array($this, $mutator))) {
			$this->$mutator($value);
		}
		else {
			$this->_values[$name] = $value;
		}
	}

	/** Function to set Id of entity, ensuring it is an integer */
	public function setId($id)
	{
		settype($id, 'int');
		$this->_values['id'] = $id;
	}

	public function getName()
	{
		if(!isset($this->sName))
		{
			$array = explode('\\', get_called_class());
			$this->sName = end($array);
		}
		return $this->sName;
	}

	public function getAllowedFields()
	{
		return $this->_allowedFields;
	}

	/**
	 * Get the value assigned to the specified field via the corresponding getter (if it exists);
	otherwise, get the value directly from the '$_values' protected array
	 */
	public function __get($name)
	{
		if(!in_array($name, $this->_allowedFields) && $name != 'relevance') {
			return null;
		}
		$accessor = 'get' . ucfirst($name);
		if(method_exists($this, $accessor) && is_callable(array($this, $accessor))) {
			return $this->$accessor();
		}

		if(isset($this->_values[$name])) {
	       return $this->_values[$name];
		}

		return null; //@todo: Rydde opp i dette
		throw new \Exception('The field ' . $name . ' has not been set for this entity yet ('.get_called_class().').');
	}

	/**
	 * Check if the specified field has been assigned to the entity
	 */
	public function __isset($name)
	{
		if (!in_array($name, $this->_allowedFields)) {
			throw new \Exception('The field ' . $name . ' is not allowed for this entity.');
		}
		return isset($this->_values[$name]);
	}

	/**
	 * Unset the specified field from the entity
	 */
	public function __unset($name)
	{
		if (!in_array($name, $this->_allowedFields)) {
			throw new \Exception('The field ' . $name . ' is not allowed for this entity.');
		}
		if (isset($this->_values[$name])) {
			unset($this->_values[$name]);
		}
	}

	/**
	 * Get an associative array with the values assigned to the fields of the entity
	 */
	public function toArray()
	{
		return $this->_values;
	}

	public function rewind()
	{
		reset($this->_allowedFields);
	}

	public function current()
	{
		$key = current($this->_allowedFields);
		return $this->__get($key);
	}

	public function key()
	{
		$key = current($this->_allowedFields);
		return $key;
	}

	public function next()
	{
		next($this->_allowedFields);
		$key = current($this->_allowedFields);
		return $this->__get($key);
	}

	public function valid()
	{
		$key = current($this->_allowedFields);
		$var = ($key !== null && $key !== false);
		return $var;
	}
}