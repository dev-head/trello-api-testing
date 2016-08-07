<?php

namespace Trello\Model;

use Trello\Logger;

class Model
{
    protected $_request_arguments   = array();
    protected $_uri                 = null;
    private $_response_data = array();
    private $_request_data  = array();
    private $_model_options = array();

    /**
     * Class constructor for instantiated instances will accept 
     * optional arguments to define class properties.
     * The passed data will be parsed to ensure only the defined request args are used.
     *
     * @param array $options
     *  Optional array to define the options.
     *
     * @param array $model_options
     * Model options is an optional array of data that is passed through from the caller,
     * generally this is data that gives us some meta information about the model. 
     * Currently: array('action_type' => 'get', 'namespace' => 'Boards', 'model_name' => 'Cards');
     *
     * @return void
    **/
    public function __construct($data = array(), $model_options = array())
    {
        $this->_model_options   = $model_options;
        $this->addRequestData($data);
    }

    /**
     * Used to build up the uri for this models end point.
     * We allow wildcarding in the format of %%param_name%%,
     * the data used to fill that is passed to the constructor and saved as a 
     * model option. The fragment name should not be used in the request_arguments as a key.
     *
     * @return string
     *  The uri
    **/
    public function getApiUri()
    {
        $return     = $this->_uri;
        $options    = $this->_model_options;

        // Replace uri fragments with data from the models options.
        foreach ($options as $key => $val) {
            $return    = preg_replace('/%%' . $key . '%%/', $val, $return);
        }
        
        if (!$return) {
            $options    = $this->_model_options;
            if (isset($options['namespace'])) {
                $return     = '/' . strtolower($options['namespace']);
            }
        }

        return $return;        
    }
    
    /**
     * The passed data is parsed and saved in a new property to ensure we only pass
     * the allowed keys in the request.
     * 
     * @param array $data
     *  An associative array of data, akin to a post.
     * 
     * @return void
    **/
    public function addRequestData($data = array())
    {
        $request_data   = array();
        $allowed_args   = $this->getRequestArguments();
        $data           = $data? $data : array();

        // We only allow the defined request arguments to be used in the request
        // If there is no matching key, we use the defined in the models arguments.
        foreach ($allowed_args as $key => $val) {
            $request_data[$key] = isset($data[$key])? $data[$key] : $val;
        }
        
        // Merge any of the unused options into the model options data.        
        $this->_model_options   = array_merge($this->_model_options, array_diff_key($data, $request_data));

        $this->setRequestData($request_data);
    }

    /**
     * Will return the response data if found otherwise the request args are returned.
     *
     * @return string
     *  Resulting output from a print_r.
    **/
    public function __toString() 
    {
        $data   = $this->getResponseData();
        if (!$data) {
            $data   = $this->getRequestArguments();
        }

        return print_r($data, true);
    }

    /**
     * Accessor to get a models defined request arguments.
     *
     * @return array
     *  Associative array of request arguments for this model.
    **/
    public function getRequestArguments()
    {
        return $this->_request_arguments;
    }

    /**
     * Accessor to get the response data for this model.
     *
     * @return array
     *  Associative array of response data for this model.
    **/
    public function getResponseData()
    {
        return $this->_response_data;
    }

    /**
     * Setter for this models response data.
     *
     * @return void
    **/
    public function setResponseData($data)
    {
        $this->_response_data   = $data;
    }

    /**
     * Accessor to get the request data for this model.
     *
     * @return array
     *  Associative array of response data for this model.
    **/
    public function getRequestData()
    {
        return $this->_request_data;
    }
    
    /**
     * Used to get the request type for a given models request, default is get.
     *
     * @param string $default
     * The default request type to return if one isn't found.
     *
     * @return string
     * The request type.
    **/
    public function getRequestType($default = 'get')
    {
        $options    = $this->_model_options;
        return $options['action_type']? $options['action_type'] : $default;
    }
    
    /**
     * Setter for this models response data.
     *
     * @return void
    **/
    public function setRequestData($data)
    {
        $this->_request_data    = $data;
    }
    
    public function setRequestArguments($args) 
    {
        $this->_request_arguments   = array_merge($args, $this->_request_arguments);
    }
}