<?php namespace Stevebauman\CoreHelper\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;

abstract class AbstractValidator {
		
	protected $input;
 	
	protected $errors;
	
        protected $rules;
        
        protected $validator;
        
 	public function __construct($input = NULL)
        {
            $this->input = $input ?: Input::all();
	}
        
        /**
         * Returns the current validator object
         * 
         * @return Validator
         */
        public function validator()
        {
            if(!$this->validator) {
                return $this->validator = Validator::make($this->input, $this->rules);
            }
            
            return $this->validator;
        }
        
        /**
         * Quick helper for validating input. Returns boolean on success/failure
         * 
         * @return boolean
         */
	public function passes()
        {
            $validation = $this->validator();
            
            if($validation->passes()) {
                return true;
            }
     
            $this->errors = $validation->messages();
		
            return false;
  	}
        
        /**
         * Returns errors from the validator. This will return only messages
         * if the request is from ajax.
         * 
         * @return mixed
         */
 	public function getErrors()
        {
            if(Request::ajax()){
                    return $this->errors->getMessages();
            } else{
                    return $this->errors;
            }
  	}
        
        /**
         * Allows rules to be set on the fly if needed
         * 
         * @param array $rules
         */
        public function setRules($rules = array())
        {
            $this->rules = $rules;
        }
        
        /**
         * Adds an ignore validation to be able to dynamically ignore a specific
         * table value
         * 
         * @param string $field
         * @param string $table
         * @param string $column
         * @param string $ignore
         */
        public function ignore($field, $table, $column, $ignore = 'NULL')
        {
            $this->rules[$field] .= sprintf('|unique:%s,%s,%s', $table, $column, $ignore);
        }
        
        /**
         * Adds a unique validation to the specified field
         * 
         * @param string $field
         * @param string $table
         * @param string $column
         */
        public function unique($field, $table, $column)
        {
            $this->rules[$field] .= sprintf('|unique:%s,%s', $table, $column);
        }
        
        /**
         * Allows dynamic adding of rules to a field under validation
         * 
         * @param string $field
         * @param string $rule
         */
        public function addRule($field, $rule)
        {
            if(array_key_exists($field, $this->rules)){
                
                $this->rules[$field] .= sprintf('|%s',$rule);
                
            } else{
                $this->rules[$field] = $rule;
            }
        }
	
}