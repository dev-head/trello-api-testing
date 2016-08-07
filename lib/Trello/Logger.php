<?php

namespace Trello;
 
/**
 *
 *
**/
class Logger
{
	public static function log($message, $force = false)
	{
	    $message    = is_array($message)? print_r($message, true) : $message;
		error_log('[LOGGER]::' . $message);
	}

    /**
     * Used for deeper debugging output.
     *
     **/
	public static function trace($message, $force = false)
	{
		error_log('[LOGGER]::[TRACE]::' . $message);
	}
}