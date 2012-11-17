<?php

class Sc_Action_Helper_FormRender extends Zend_Controller_Action_Helper_Abstract
{
    function direct($form_id)
    {
		$db = Zend_Registry::get('db');
		$forms = new Application_Model_Forms;
		$formsMapper = new Application_Model_FormsMapper;
		$formsMapper->find($form_id,$forms);	
		
		// gather form fields data
		$select = $db->select()
					->from('form_fields')
					->where('form_id = ?',$form_id);
		$select->order('order_val DESC');
		$select->order('name DESC');
		$results = $db->fetchAll($select);
		
		$formName = strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","",$forms->getName()));
		$form_op = '<h2>Contact Us</h2>
					<p>Tell us how we\'re doing, we would love to hear your feedback.</p>
					<form method="post" enctype="multipart/form-data" action="/form-proc/dynamic-proc/" name="'.$formName.'" id="'.$formName.'" class="dynamicForm">'."\n";
		$required = array();
		foreach($results as $id => $val){
			$fieldName = strtolower(preg_replace("/[^A-Za-z0-9\s\s+]/","-",$val[name]));
			$form_op .= '<div class="formField" id="'.$formName.'_'.$fieldName.'">'."\n";
			switch($val[type]){
				case 'textbox':
					$form_op .= '<div class="contactFormBlock"><label for="'.$fieldName.'">'.$val[name].'</label>'."\n";
					$form_op .= '<input'.($val[required] == 1 ? ' class="required"' : '').' type="text" name="'.$fieldName.'" id="'.$fieldName.'" value="'.$val[default_val].'" size="'.$val[width].'" />'."\n";
					$form_op .= '<div class="clear"></div></div>';
					if($val[required] == 1) $required[$fieldName] = $val[name];
				break;
				case 'textarea':
					$form_op .= '<div class="contactFormBlock"><label for="'.$fieldName.'">'.$val[name].'</label>'."\n";
					$form_op .= '<textarea'.($val[required] == 1 ? ' class="required"' : '').' name="'.$fieldName.'" id="'.$fieldName.'" cols="'.$val[width].'" rows="'.$val[height].'">'.$val[default_val].'</textarea>'."\n";
					$form_op .= '<div class="clear"></div></div>';
					if($val[required] == 1) $required[$fieldName] = $val[name];
				break;
				case 'radiobutton':
					$form_op .= '<div class="contactFormRadio">
					<fieldset data-role="controlgroup">
					<legend><p><label for="'.$fieldName.'">'.$val[name].'</label><br /><small>'.$val[description].'</small></p></legend>'."\n";
					$radOptions = explode("\n",$val[default_val]);
					foreach($radOptions as $curId => $curVal){
						$form_op .= '<input type="radio" name="'.$fieldName.'" id="'.$fieldName.'_'.$curId.'" value="'.$curVal.'"><label for="'.$fieldName.'_'.$curId.'">'.$curVal.'</label>'."\n";
					}
					$form_op .= '<div class="clear"></div>
					</div>';
				break;
					$form_op .= '</div>'."\n";
				case 'checkbox':
					$form_op .= '<label for="'.$fieldName.'">'.$val[name].'</label>'."\n";
					$radOptions = explode("\n",$val[default_val]);
					foreach($radOptions as $curId => $curVal){
						$form_op .= '<label for="'.$fieldName.'_'.$curId.'">'.$curVal.'</label>'."\n".'<input type="checkbox" name="'.$fieldName.'[]" id="'.$fieldName.'_'.$curId.'" value="'.$curVal.'">'."\n";
					}
				break;
				case 'selectbox':
					$form_op .= '<label for="'.$fieldName.'">'.$val[name].'</label>'."\n";
					$form_op .= '<select'.($val[required] == 1 ? ' class="required"' : '').' name="'.$fieldName.'">'."\n";
					$form_op .= '<option value=""></option>'."\n";
					$radOptions = explode("\n",$val[default_val]);
					foreach($radOptions as $curId => $curVal){
						$form_op .= '<option value="'.$curVal.'">'.$curVal.'</option>'."\n";
					}
					$form_op .= '</select>'."\n";
					if($val[required] == 1) $required[] = $fieldName;
				break;
				case 'fileupload':
					$form_op .= '<label for="'.$fieldName.'">'.$val[name].'</label>'."\n";
					$form_op .= '<input'.($val[required] == 1 ? ' class="required"' : '').' name="'.$fieldName.'" type="file">'."\n";
					if($val[required] == 1) $required[$fieldName] = $val[name];
				break;
			}
			$form_op .= '<div class="clear"></div></div>'."\n";
		}
		if($forms->getCaptcha() ==  1){
			require_once(APP_BASE_PATH.'/lib/recaptcha/recaptchalib.php');
			// Get a key from https://www.google.com/recaptcha/admin/create
			$publickey = "6LfFl8YSAAAAACB6drjxyY6H4ArzBVTAikB-BJAr";
			$privatekey = "6LfFl8YSAAAAAHJ5oZJbhu-JyUBdEymW29Yc11VZ";
			
			# the response from reCAPTCHA
			$resp = null;
			# the error code from reCAPTCHA, if any
			$error = null;
			
			$form_op .= '<div class="clear"></div><div style="padding:10px;" id="recaptcha-'.$formName.'">
							<div id="captchaStatus"></div>
							'.recaptcha_get_html($publickey).'
						</div>
						<script type="text/javascript">
						$(function(){
								function validateCaptcha()
								{
									challengeField = $("#recaptcha_challenge_field").val();
									responseField = $("#recaptcha_response_field").val();
									//alert(challengeField);
									//alert(responseField);
									//return false;
									var html = $.ajax({
									type: "POST",
									url: "/lib/recaptcha/ajax.recaptcha.php",
									data: "recaptcha_challenge_field=" + challengeField + "&recaptcha_response_field=" + responseField,
									async: false
									}).responseText;
						
									if (html.replace(/^\s+|\s+$/, \'\') == "success")
									{
										$("#captchaStatus").html(" ");
										// Uncomment the following line in your application
										return true;
									}
									else
									{
										$("#captchaStatus").html("Your captcha is incorrect. Please try again");
										Recaptcha.reload();
										return false;
									}
								}
						
									//Modified as per comments in site to handle event unobtrusively
								$("#'.$formName.'").submit(function(){
										return validateCaptcha();
							});
						});
						</script>'."\n";
		}
		// error check markup
		if(count($required) > 0){
			$form_op .= '
			<script type="text/javascript">
			$(function(){
				var '.$formName.'Container = $(\'#validate-'.$formName.'\');
			// validate the form when it is submitted
			var validator = $("#'.$formName.'").validate({
				errorContainer: '.$formName.'Container,
				errorLabelContainer: $("ol", '.$formName.'Container),
				wrapper: \'li\',
				meta: "validate",
				invalidHandler: function(form, validator) {
					$( "#validate-'.$formName.'" ).dialog();
				}
				});
			});
			</script>
			<div id="validate-'.$formName.'" style="display:none">
			  <h4>Opps! You forgot to provide the information below!</h4>
			  <ol>'."\n";
			foreach($required as $curId => $curReq){
				$form_op .= '<li>
				  <label for="'.$curId.'" class="error">Please enter your '.$curReq.'!</label>
				</li>'."\n";
			}
			$form_op .= '</ol>
			</div>'."\n";
		}
		$form_op .= '<input type="hidden" name="form_id" value="'.$form_id.'"/><input type="submit" name="submit" value="Submit" />'."\n";
		$form_op .= '<div class="clear"></div>';
		$form_op .= '</form>
		<p>Leave us a message at one of our locations by clicking the link to your nearest location below.</p>
<div id="contactLinks"> <a target="_blank" href="https://ynot-norfolk.foodtecsolutions.com/contact">Ghent</a> <a target="_blank" href="https://ynot-colonial.foodtecsolutions.com/contact">Great Neck</a> <a target="_blank" href="https://ynot-vbeach.foodtecsolutions.com/contact">Kempsville</a> <a target="_blank" href="https://ynot-chesapeake.foodtecsolutions.com/contact">Greenbrier</a> </div>'."\n";
		
	return $form_op;
	}
}