<?php

namespace Trello\Client;

use Trello;
use Trello\Request;
use Trello\Logger;

class Client
{
    private $_options       = array();
    private $_request       = null;
    private $_namespaces    = null;
    
    /**
     * Class constructor for instantiated instances will accept 
     * optional arguments to define class properties.
     *
     * @param array $options
     *  Optional array to define the options.
     *
     * @return void
    **/
    public function __construct($options) 
    {
        $this->_options = $options;
    }
    
    /**
     * Parse the requested function and handle the request process.
     *
    **/
    public function __call($name, $arguments)
    {
        $request        = $this->getRequest();
        $model_details  = $this->parseRequestName($name);

        if (!empty($model_details['model_name'])) {
            $model_name     = $model_details['model_name'] . '\\' . $model_details['model_name'];
        } else {
            $model_name     = $model_details['namespace'];
        }        

        $class  = 'Trello\Model\\' . $model_details['namespace'] . '\\' . $model_name;
        
        // Currently only accepting one argument, if we don't need to expand this
        // we can refactor this to be fixed method.
        $model          = new $class(@$arguments[0], $model_details);
        $response   = $request->execute($model, $model_details);

        return $response;
    }
    
    /**
     * Gets all the api namespaces, based upon directory structure.
     *
    **/
    public function getNameSpaces()
    {
        // Get all the model name spaces.
        $return     = $this->_namespaces;

        if ($return === null) {
            $path       = dirname(__FILE__) . '/Model/*';
            $dirs       = glob($path);
            $namespaces = array();
            for ($i = 0; $i < $c = count($dirs); $i++) {

                if (is_dir($dirs[$i])) {
                    $path_info      = pathinfo($dirs[$i]);
                    $return[]       = $path_info['filename'];
                }
            }
            
            $this->_namespaces  = $return;
        }

        return $return;    
    }
    
    /**
     *
     * call format: [action type]/[Namespace]/[Model]
     *  eg: /get/Boards/Cards()
     *
     *
     **/
    public function parseRequestName($name)
    {
        $action_types   = array('get', 'put', 'delete', 'post');
        $namespaces     = $this->getNameSpaces();
        $action_type    = 'get';
        $namespace      = null;

        // Parsing out for the action type.
        for ($i = 0; $i < $c = count($action_types); $i++) {
            $matches    = array();
            $check      = '/^' . $action_types[$i] . '/';

            preg_match($check, $name, $matches);

            // Set the action type and remove that from the model name.
            if ($matches) {
                $action_type    = $action_types[$i];
                $name           = preg_replace($check, '', $name);
                break;
            }
        }

        // Parsing out for the namespace.
        for ($i = 0; $i < $c = count($namespaces); $i++) {
            $matches    = array();
            $check      = '/^' . $namespaces[$i] . '/';

            preg_match($check, $name, $matches);

            // Set the action type and remove that from the model name.
            if ($matches) {
                $namespace  = $namespaces[$i];
                $name       = preg_replace($check, '', $name);
                break;
            }
        }

        return array(
            'action_type'   => $action_type,
            'namespace'     => $namespace,
            'model_name'    => $name,
        );        
    }
    
    /**
     * Accessor for the request object instance.
     *
     * @return object
     *  Instantiated instance of the Client class.
    **/
    public function getRequest()
    {
        $return = $this->_request;
        if (!$return) {
            $return = $this->setRequest();
        }
        
        return $return;
    }

    /**
     * Setter for the option array.
     *
     * @return void
    **/
    public function setOptions($options)
    {
        $this->_options = $options;
    }
    
    /**
     * Set the request class which handles the building of the request.
     *
     * @return object
     *  The initialized request object is returned.
    **/
    public function setRequest()
    {
        return $this->_request = new Request($this->_options);
    }

}