<?php
namespace App\Exceptions;
final class UnexpectedValueException extends \Exception
{
	private $value;

	public function __construct($message, $value, $code = 0, \Exception $previous = null)
	{
		$this->value = $value;
		parent::__construct($message, $code, $previous);
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getType()
	{
		switch($type = gettype($this->value)) {
			default:
				return $type.'('.print_r($this->value).')';
				break;
			case 'array':
				return $type.'(size='.sizeof($this->value).')';
				break;
			case 'NULL':
				return $type;
				break;
			case 'object':
				return $type . '('. get_class($this->value) . ')';
				break;

		}
	}
}