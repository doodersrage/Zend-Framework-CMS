<?PHP
class Sc_Action_Helper_Pass extends Zend_Controller_Action_Helper_Abstract
{
    function direct($request_val)
    {
		$salt = 'SKJHDKSH8918979';
		
		$newPass = md5($salt.$request_val);
		
	return $newPass;
	}
}
