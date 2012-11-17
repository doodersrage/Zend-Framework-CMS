<?php

class Sc_Action_Helper_Urlcheck extends Zend_Controller_Action_Helper_Abstract
{
    function direct($request_val)
    {
		
	  $curl = curl_init();
	  curl_setopt ($curl, CURLOPT_URL, $request_val);
   	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  // do your curl thing here
	  $result = curl_exec($curl);
	  $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	return $http_status;
    }
}