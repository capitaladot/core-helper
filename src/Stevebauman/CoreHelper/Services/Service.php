<?php

namespace Stevebauman\CoreHelper\Services;

use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Event;

abstract class Service {
    
    /*
     * Holds the data to be inserted into the database
     */
    protected $input =  array();

    /**
     * Set's the input data to be inserted into DB
     *
     * @param array $input
     * @return $this
     */
    public function setInput($input = array())
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Retrieves data from the input array
     *
     * @param string $field
     * @param null $default
     * @param bool $clean
     * @return null|mixed
     */
    public function getInput($field,  $default = NULL, $clean = FALSE)
    {
        
        /*
         * If the field exists in the input array
         */
        if(array_key_exists($field, $this->input)){
            
            /*
             * If clean is set to true, clean the input and return it
             */
            if($clean){
                
                return $this->clean($this->input[$field]);
                
            } else{
                
                //If clean is set to false, return the input
                return $this->input[$field];
                
            }
            
        } else{
            
            /*
             * If key does not exist in the input array, and a 
             * default value is specified, return the default value
             */
            if($default !== NULL){
                
                return $default;
                
            } else{
                /*
                 * Return NULL if the default value is not set
                 */
                return NULL;
            }
        }
    }
    
    /**
     * Cleans input from data removing invalid HTML tags such as scripts
     * 
     * @param string $input
     * @return mixed
     */
    protected function clean($input)
    {
        if($input){
            $cleaned = Purifier::clean($input);
            
            return $cleaned;
        } else{
            return NULL;
        }
    }
    
    /**
     * Alias for firing events easily that extend from this class
     * 
     * @param string $name
     * @param array $args
     * @return mixed
     */
    protected function fireEvent($name, $args = array())
    {
        return Event::fire((string) $name, (array) $args);
    }
    
}