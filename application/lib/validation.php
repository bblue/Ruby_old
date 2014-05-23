<?php
namespace Lib;
final class Validation {

	// Define required variables
	public $errors = array();
	private $validation_rules = array();
	public $sanitized = array();
	private $data = array();

    public function addSource(array $source) {
        $this->data = $source;
    }

    public function resetValidationRules(){
    	$this->validation_rules = array();
    }

    public function resetSource(){
    	$this->data = array();
    }

   	private function resetErrors(){
   		$this->errors = array();
   	}

   	private function resetSanitized(){
   		$this->sanitized = array();
   	}

    public function resetAll(){
    	$this->resetSource();
    	$this->resetValidationRules();
    	$this->resetErrors();
    	$this->resetSanitized();
    }

	public function validate(){
		// We use the php built-in ArrayIterator to prepare the variables
		foreach( new ArrayIterator($this->validation_rules) as $var=>$opt){
			if($opt['required'] == true){
				$this->is_set($var);
			}

			// Trim whitespace from beginning and end of variable
			if( array_key_exists('trim', $opt) && $opt['trim'] == true ){
				$this->data[$var] = trim( $this->data[$var] );
			}

			switch($opt['type']){
				default:
					throw new Exception('Validation type ' . $opt['type'] . ' does not exist');
					break;
				case 'email':
					$this->validateEmail($var, $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizeEmail($var);
					}
					break;

				case 'url':
					$this->validateUrl($var);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizeUrl($var);
					}
					break;

				case 'integer':
					$this->validateNumeric($var, $opt['min'], $opt['max'], $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizeNumeric($var);
					}
					break;

				case 'string':
					$this->validateString($var, $opt['min'], $opt['max'], $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizeString($var);
					}
					break;

				case 'username':
					$this->validateUsername($var, $opt['min'], $opt['max'], $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizeString($var);
					}
					break;

				case 'password':
					$this->validatePassword($var, $opt['min'], $opt['max'], $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizePassword($var);
					}
					break;

				case 'float':
					$this->validateFloat($var, $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizeFloat($var);
					}
					break;

				case 'ipv4':
					$this->validateIpv4($var, $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitizeIpv4($var);
					}
					break;

				case 'boolean':
					$this->validateBool($var, $opt['required']);
					if(!array_key_exists($var, $this->errors)){
						$this->sanitized[$var] = (bool) $this->data[$var];
					}
					break;
			}
		}
	}

	public function addValidationRules(array $aValidationRules)
	{
		foreach($aValidationRules as $varname => $aValidationRule)
		{
			$this->validation_rules[$varname] = $aValidationRule;
		}
	}

	// This function is for chaining different rules
	public function addValidationRule($varname, $type, $required=false, $min=0, $max=0, $trim=false){
		$this->validation_rules[$varname] = array('type'=>$type, 'required'=>$required, 'min'=>$min, 'max'=>$max, 'trim'=>$trim);
		return $this;
	}

	// Merge all rules
	public function mergeValidationRules(array $rules_array){
		$this->validation_rules = array_merge($this->validation_rules, $rules_array);
	}

	private function is_set($var){
		if(!isset($this->data[$var])){
			$this->errors[$var] = $var . ' is not set';
		}
	}

	private function validateIpv4($var, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}
		if(filter_var($this->data[$var], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE){
			$this->errors[$var] = $var . ' is not a valid IPv4';
		}
	}

	public function validateIpv6($var, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}

		if(filter_var($this->data[$var], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE){
			$this->errors[$var] = $var . ' is not a valid IPv6';
		}
	}

	private function validateFloat($var, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}
		if(filter_var($this->data[$var], FILTER_VALIDATE_FLOAT) === false){
			$this->errors[$var] = $var . "($this->data[$var]) is an invalid float";
		}
	}

	private function validateString($var, $min=0, $max=0, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}

		if(isset($this->data[$var])){
			if(strlen($this->data[$var]) < $min){
				$this->errors[$var] = $var . ' is too short: ' . strlen($this->data[$var]) . ' characters. Minimum required is ' . $min;
			}
			elseif(strlen($this->data[$var]) > $max){
				$this->errors[$var] = $var . ' is too long:' . strlen($this->data[$var]) . ' characters . Max allowed is ' . $max;
			}
			elseif(!is_string($this->data[$var])){
				$this->errors[$var] = $var . ' is invalid';
			}
		}
	}

	private function validateUsername($var, $min=USERNAME_MIN_LENGTH, $max=USERNAME_MAX_LENGTH, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}

		if(isset($this->data[$var])){
			if($required && strlen($this->data[$var]) == 0){
				$this->errors[$var] = $var . ' is a required field';
			}
			elseif(strlen($this->data[$var]) < $min){
				$this->errors[$var] = $var . ' is too short';
			}
			elseif(strlen($this->data[$var]) > $max){
				$this->errors[$var] = $var . ' is too long';
			}
			elseif(!is_string($this->data[$var])){
				$this->errors[$var] = $var . ' is invalid';
			}
			elseif(!preg_match('/^[a-zA-Z0-9]+$/', $this->data[$var])){
				$this->errors[$var] = $var . ' contains invalid characters';
			}
		}
	}

	private function validatePassword($var, $min=PASSWORD_MIN_LENGTH, $max=PASSWORD_MAX_LENGTH, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}

		if(isset($this->data[$var])){
			if($required && strlen($this->data[$var]) == 0){
				$this->errors[$var] = $var . ' is a required field';
			}
			elseif(strlen($this->data[$var]) < $min){
				$this->errors[$var] = $var . ' is too short';
			}
			elseif(strlen($this->data[$var]) > $max){
				$this->errors[$var] = $var . ' is too long';
			}
		}
	}

	private function validateNumeric($var, $min=0, $max=0, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}
		if(filter_var($this->data[$var], FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min, "max_range"=>$max)))===FALSE){
			$this->errors[$var] = $var . ' is an invalid number: ' . $this->data[$var];
		}
	}

	private function validateUrl($var, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}
		if(filter_var($this->data[$var], FILTER_VALIDATE_URL) === FALSE){
			$this->errors[$var] = $var . ' is not a valid URL';
		}
	}

	private function validateEmail($var, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}
		if(filter_var($this->data[$var], FILTER_VALIDATE_EMAIL) === FALSE){
			$this->errors[$var] = $var . ' is not a valid email address';
		}
	}

	private function validateBool($var, $required=false){
		if($required==false && strlen($this->data[$var]) == 0){
			return true;
		}

		if(filter_var($this->data[$var], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === NULL){
			$this->errors[$var] = $var . ' is invalid (' . $this->data[$var] . ')';
		}
	}

	########## SANITIZING METHODS ############
	public function sanitizeEmail($var){
		$email = preg_replace( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', $this->data[$var] );
		$this->sanitized[$var] = (string) filter_var($email, FILTER_SANITIZE_EMAIL);
	}

	private function sanitizeUrl($var){
		$this->sanitized[$var] = (string) filter_var($this->data[$var],  FILTER_SANITIZE_URL);
	}

	private function sanitizeNumeric($var){
		$this->sanitized[$var] = (int) filter_var($this->data[$var], FILTER_SANITIZE_NUMBER_INT);
	}

	private function sanitizeString($var){
		$this->sanitized[$var] = (string) filter_var($this->data[$var], FILTER_SANITIZE_STRING);
	}

	private function sanitizePassword($var){
		$this->sanitized[$var] = md5(SALT . (string) filter_var($this->data[$var], FILTER_SANITIZE_MAGIC_QUOTES) . PEPPER);
	}

	private function sanitizeMagicQuotes($var){
		$this->sanitized[$var] = (string) filter_var($this->data[$var], FILTER_SANITIZE_MAGIC_QUOTES);
	}

	private function sanitizeFloat($var){
		$this->sanitized[$var] = (float) filter_var($this->data[$var], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
}