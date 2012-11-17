<?php
/**
 * Helper for retrieving base URL
 *
 * @package 
 * @version $Id: $
 */
class Zend_View_Helper_BaseUrl  
{  
    function baseUrl()  
    {  
        $base_url = substr($_SERVER['PHP_SELF'], 0, -9);  
        return $base_url;  
    }  
}  
 