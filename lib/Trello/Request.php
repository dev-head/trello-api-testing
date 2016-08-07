<?php

namespace Trello;

use Trello\Logger;

class Request
{
    private $_options   = array();
    
    /**
     *
     *
     *
    **/
    public function __construct($options)
    {
        $this->_options = $options;
    }
    
    /**
     *
     *
     *
    **/
    public function execute($model)
    {
        $options        = $this->_options;
        $model_uri      = $model->getApiUri();
        $request_data   = $model->getRequestData();
        $request_type   = $model->getRequestType();
        $end_point      = 'https://api.trello.com/' . trim($options['api_version'], '/') . '/' . trim($model_uri, '/');
        
        // Add the required authentication data for the request.
		$request_data['key']    = $options['key'];

        // todo apply conditionals for auth needs.		
		$request_data['token']  = $options['token'];

        try {
            $return_data = $this->_execute($end_point, $request_data, $request_type);
            $model->setResponseData(json_decode($return_data, true));
        } catch (\Exception $e) {
        	throw new \Exception('[ERROR]::[stop the bad man]::[' . $e->getMessage() . ']');
        }
        return $model;
    }
    
    private function _execute($end_point, $request_data, $request_type = 'get')
    {
        $ch         = curl_init();
        $headers    = array();
        $headers[]	= 'Content-Type: text/plain; charset=utf-8';

        switch (strtolower($request_type)) {
            case 'get':
                $end_point .= '?' . http_build_query($request_data);
                curl_setopt($ch, CURLOPT_POST, false);
            
                break;
            case 'post':
            case 'put':
                curl_setopt($ch,CURLOPT_POSTFIELDS, $request_data);
                curl_setopt($ch, CURLOPT_POST, true);
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_NOPROGRESS, true);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response       = curl_exec($ch);
        $info           = curl_getinfo($ch);
        $header_size	= isset($info['header_size'])? $info['header_size'] : null;
        $header         = substr($response, 0, $header_size);
        $body           = substr($response, $header_size);

        switch ($info['http_code']) {
            default:
                throw new \Exception('[ERROR]::' . $body);
                break;
                
            case 200:
                break;
        }

        return $body;
    }
}