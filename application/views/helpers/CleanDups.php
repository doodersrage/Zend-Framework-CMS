<?php
class Zend_View_Helper_CleanDups  
{  
    function cleanDups($val,$dup = '--',$cleaned = '-')  
    {  
		if(strpos($val,$dup)){
			$val = str_replace($dup,$cleaned,$val);
		}
		if(strpos($val,$dup)){
			$val = $this->cleanDups($val);
		}
	return $val;
    }  
}  
 