<?php

namespace Trello;

/**
 * @todo auto load this
 *
**/
include_once dirname(__FILE__) . '/Trello/Client.php';
include_once dirname(__FILE__) . '/Trello/Logger.php';
include_once dirname(__FILE__) . '/Trello/Model.php';
include_once dirname(__FILE__) . '/Trello/Request.php';
include_once dirname(__FILE__) . '/Trello/Model/Boards.php';
include_once dirname(__FILE__) . '/Trello/Model/Boards/Cards.php';
include_once dirname(__FILE__) . '/Trello/Model/Boards/Lists.php';
include_once dirname(__FILE__) . '/Trello/Model/Cards.php';
include_once dirname(__FILE__) . '/Trello/Model/Lists.php';
include_once dirname(__FILE__) . '/Trello/Model/Lists/Cards.php';

/**
 * 
 *
**/
class Trello
{
    protected   $_auth_options  = array();
    protected   $_client        = null;
    
    /**
     * Class constructor for instantiated instances will accept 
     * optional arguments to define class properties.
     *
     * @param array $auth_options
     *  Optional array to define the auth options.
     *
     * @return void
    **/
    public function __construct($auth_options = array())
    {
        $this->setAuthOptions($auth_options);
    }

	/**
	 * Used to allow calls to proxy through to the Request which proxies to the Client.
	 * 
	 * @param string $name
	 *  The name of the api method to be called.
	 *
	 * @param mixed $arguments
	 *  An associative array of parameters to be passed to the api method. 
	 *
	 * @return object
	 *  An instance of a model is returned.
	**/
	public function __call($name, $arguments)
	{
	    $return = null;
	    
		try {
            $client = $this->getClient();
            $client->setOptions($this->getAuthOptions());
            $return = call_user_func_array(array($client, $name), $arguments);
		}

		catch (Exception $e) {
			Logger::log('[ERROR]::' . $e->getMessage());
		}

		return $return;
	}
	
    /**
     * Setter for a single auth option.
     *
     * @param string $key
     *  The option key you want to set.
     *
     * @param mixed $value
     *  The corresponding keys value to set.
     *
     * @return void
    **/
    public function setAuthOption($key, $val)
    {
        if ($key && strlen($key) > 1) {
            $options        = $this->getOptions();
            $options[$key]  = $val;
            $this->setAuthOptions($options);
        }
    }
    
    /**
     * Setter for the auth option array.
     *
     * @return void
    **/
    public function setAuthOptions($options = array())
    {
        $this->_auth_options    = $options;
    }
    
    /**
     * Set the client class which handles the requests.
     *
     * @return object
     *  The initialized client object is returned.
    **/
    public function setClient()
    {
        return $this->_client  = new Client\Client($this->getAuthOptions());
    }
    
    /**
     * Accessor for the auth option array.
     *
     * @return mixed
     *  Array of authorization options that have been defined.
    **/
    public function getAuthOptions()
    {
        return $this->_auth_options;
    }

    /**
     * Used to get a specific auth option from the auth option array.
     *
     * @param string $key
     *  The auth key you are wanting a return value for.
     *
     * @param mixed $default_value
     *  Optional argument to set a default value in the event there's no key.
     *
     * @return mixed
     *  The matching value of the key found else $default_value is returned.
    **/    
    public function getAuthOption($key, $default_value = null)
    {
        $return     = $default_value;
        $options    = $this->getAuthOptions();

        if ($key && strlen($key > 1) && isset($options[$key])) {
            $return = $options[$key];
        }
        
        return $return;
    }
    
    /**
     * Accessor for the client object instance.
     *
     * @return object
     *  Instantiated instance of the Client class.
    **/
    public function getClient()
    {
        $return = $this->_client;
        if (!$return) {
            $return = $this->setClient();
        }
        
        return $return;
    }
}