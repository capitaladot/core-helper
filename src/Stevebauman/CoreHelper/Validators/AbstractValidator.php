<?php

namespace Stevebauman\CoreHelper\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;

/**
 * Class AbstractValidator
 * @package Stevebauman\CoreHelper\Validators
 */
abstract class AbstractValidator
{
    /**
     * Holds the input to be passed to the validator
     *
     * @var array
     */
    protected $input;

    /**
     * Holds the error messages of the current request
     *
     * @var mixed
     */
    protected $errors;

    /**
     * Holds the rules for the validator
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Holds the current validator object
     *
     * @var
     */
    protected $validator;

    /**
     * @param array $input
     */
    public function __construct($input = [])
    {
        if(count($input) > 0)
        {
            $this->setInput($input);
        } else
        {
            $this->setInput(Input::all());
        }
    }

    /**
     * Allows rules to be set on the fly if needed
     *
     * @param array $rules
     */
    public function setRules($rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * Sets the input property
     *
     * @param array $input
     */
    public function setInput($input = [])
    {
        $this->input = $input;
    }

    /**
     * Sets the errors property
     *
     * @param array $errors
     */
    public function setErrors($errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * Sets the validator property
     *
     * @param $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * Returns the current validator object and creates a new validator
     * instance if it does not exist
     *
     * @return mixed
     */
    public function validator()
    {
        if (!$this->validator)
        {
            $validator = Validator::make($this->input, $this->rules);

            $this->setValidator($validator);
        }

        return $this->validator;
    }

    /**
     * Quick helper for validating input. Returns boolean on success/failure
     *
     * @return bool
     */
    public function passes()
    {
        $validation = $this->validator();

        if ($validation->passes()) return true;

        $this->setErrors($validation->messages());

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
        if (Request::ajax())
        {
            return $this->errors->getMessages();
        } else {
            return $this->errors;
        }
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
     * @param NULL $ignore
     */
    public function unique($field, $table, $column, $ignore = NULL)
    {
        if(array_key_exists($field, $this->rules))
        {
            if($ignore)
            {
                $this->rules[$field] .= sprintf('|unique:%s,%s,%s', $table, $column, $ignore);
            } else
            {
                $this->rules[$field] .= sprintf('|unique:%s,%s', $table, $column);
            }
        }
    }

    /**
     * Allows dynamic adding of rules to a field under validation
     *
     * @param string $field
     * @param string $rule
     */
    public function addRule($field, $rule)
    {
        if (array_key_exists($field, $this->rules))
        {
            $this->rules[$field] .= sprintf('|%s', $rule);
        } else
        {
            $this->rules[$field] = $rule;
        }
    }

    /**
     * Allows dynamic removal of rules to a field under validation
     *
     * @param $field
     * @param $rule
     */
    public function removeRule($field, $rule)
    {
        if (array_key_exists($field, $this->rules))
        {
            $newRule = str_replace($rule, '', $this->rules[$field]);
            $this->rules[$field] = $newRule;
        }
    }
}
