<?php
header('Content-Type: text/html; charset=UTF-8');
include_once('cc_class.php');
$ccContactOBJ = new CC_Contact();
$ccListOBJ = new CC_List(); 
$disabled = null;


//get all available lists for the current CC account.
$allLists = $ccListOBJ->getLists();
//get all available states
$allStates = $ccContactOBJ->getStates();
//get all available countries
$allCountries = $ccContactOBJ->getCountries();


/**
 * If we have post fields means that the form have been submitted.
 * Therefore we start submiting inserted data to ConstantContact Server. 
 */
	try {
		if(isset($_POST["state"]) && isset($_POST["state_name"]))
		{
			if($_POST["state"] != "" && $_POST["state_name"] != "")
			{
				$_POST["state_name"] = "";
				echo "<h1><center>State Other was erased to prevent errors on submission</center></h1>";
			}
			else {
				
			}
		}
		
	if (!empty($_POST)) {
		$postFields = array();
		$postFields["email_address"] = $_POST["email_address"];
		$postFields["first_name"] = $_POST["first_name"];
		$postFields["last_name"] = $_POST["last_name"];
		$postFields["middle_name"] = $_POST["middle_name"];
		$postFields["company_name"] = $_POST["company_name"];
		$postFields["job_title"]= $_POST["job_title"];
		$postFields["home_number"] = $_POST["home_num"];
		$postFields["work_number"] = $_POST["wrk_num"];
		$postFields["address_line_1"] = $_POST["adr_1"];
		$postFields["address_line_2"] = $_POST["adr_2"];
		$postFields["address_line_3"] = $_POST["adr_3"];
		$postFields["city_name"] = $_POST["city"];
		$postFields["state_code"] = $_POST["state"];
		// The Code is looking for a State Code For Example TX instead of Texas
		$postFields["state_name"] = $_POST["state_name"];
		$postFields["country_code"] = $_POST["country"];
		$postFields["zip_code"] = $_POST["postal_code"];
		$postFields["sub_zip_code"] = $_POST["sub_postal"];
		$postFields["notes"] = $_POST["notes"];
		if(isset($_POST["mail_type"]))
		{
		$postFields["mail_type"] = $_POST["mail_type"];
		}
		else
		{
			$postFields["mail_type"] = "HTML";
		}
		
		if(isset($_POST["lists"]))
		{
		$postFields["lists"] = $_POST["lists"];
		}
		else
		{
			throw new Exception("You are missing your list");
		}

		$postFields["custom_fields"] = array();
		foreach($_POST as $key=>$val) {
			
			if (strncmp($key, 'custom_field_', strlen('custom_field_')) === 0) {
				$postFields["custom_fields"][substr($key, strlen('custom_field_'), strlen($key)-1)] = $val;
			}

		}

		$contactXML = $ccContactOBJ->createContactXML(null,$postFields);

		if (!$ccContactOBJ->addSubscriber($contactXML)) {
			$error = true;
		} else {
			$error = false;
			$_POST = array();
		}

	}
		
	} catch (Exception $e) {
		echo "<span style='color:red;'>" . $e . "</span>";
	}
	


	?>
<?php  include_once('header.php'); ?>
<div align="center" style="width: 900px;">
<h2>Add a new Subscriber - Full form</h2>
<?php 
	//
	// Here we add an area where messages  are displayed (error or success messages).
	//
	
	if (isset($error)) {
		
		if ($error === true) {
			$class = "error";
			$message = $ccContactOBJ->lastError;
		} else {
			$class = "success";
			$message = "Contact ".htmlspecialchars($postFields["email_address"], ENT_QUOTES, 'UTF-8')." Added.";
		}

		echo '<div class="'.$class.'">';
		echo $message;
		echo '</div>';
	}

	?>
<form action="" method="post">

<?php 
if($ccContactOBJ->contact_lists && !$ccContactOBJ->show_contact_lists && $ccContactOBJ->force_lists){
	foreach ($allLists as $k=>$item) {
		echo '<input type="hidden" name="lists[]" value="'.$item['id'].'" id="chk_'.$k.'" />';
	}
} else {
?>

<div style="float: right; text-align: left; overflow: auto; height: 640px; width:250px;">


	<fieldset>
		<legend> Interests list </legend>
	<?php
	foreach ($allLists as $k=>$item) {
		$checked = '';
		
		
		if (isset($_POST['lists'])){	
			if (in_array($item['id'],$_POST['lists'])) {
				$checked = ' checked ';
			}
		}
		
		if($ccContactOBJ->force_lists && $ccContactOBJ->show_contact_lists){	
			echo '<input type="hidden" name="lists[]" value="'.$item['id'].'" id="chk_'.$k.'" /><label for="chk_'.$k.'">'.$item['title'].'</label><br />';
		}
//		else {	
//			echo '<input type="checkbox" '.$checked . $disabled .' class="checkbox" name="lists[]" value="'.$item['id'].'" id="chk_'.$k.'" /> <label for="chk_'.$k.'">'.$item['title'].'</label><br/>';
//		}
		else {	
			echo '<input type="checkbox" class="checkbox" name="lists[]" value="'.$item['id'].'" id="chk_'.$k.'" /> <label for="chk_'.$k.'">'.$item['title'].'</label><br/>';
		}

	}
	?>		
	</fieldset>
</div>
<?php } ?>

<div>
	<h3>Contact information</h3>	
	<table width="580" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td align="left">Email Address:</td>
			<td align="left"><input type="text" name="email_address" maxLength="50"  value="<?php  
			if (isset($_POST['email_address']))
			{
				echo htmlspecialchars($_POST['email_address'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">&nbsp;</td>
	      	<td align="left">&nbsp;</td>
	  </tr>
		<tr>
			<td align="left">First Name:</td>
			<td align="left"><input type="text" name="first_name" maxLength="50"  value="<?php  
			if (isset($_POST['first_name']))
			{
				echo htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Last Name:</td>
			<td align="left"><input type="text" name="last_name" maxLength="50"  value="<?php  
			if (isset($_POST['last_name']))
			{
				echo htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Middle Name:</td>
			<td align="left"><input type="text" name="middle_name" maxLength="50"  value="<?php  
			if (isset($_POST['middle_name']))
			{
				echo htmlspecialchars($_POST['middle_name'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Home phone:</td>
			<td align="left"><input type="text" name="home_num" maxLength="50"  value="<?php  
			if (isset($_POST['home_num']))
			{
				echo htmlspecialchars($_POST['home_num'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left" valign="top">Address:</td>
			<td align="left" colspan="3">
				<input type="text" name="adr_1" maxLength="50" style="width: 440px"  value="<?php  
				if (isset($_POST['adr_1']))
			{
				echo htmlspecialchars($_POST['adr_1'], ENT_QUOTES, 'UTF-8');
			}
				 ?>" /><br/>
				<input type="text" name="adr_2" maxLength="50" style="width: 440px" value="<?php  
				if (isset($_POST['adr_2']))
			{
				echo htmlspecialchars($_POST['adr_2'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /><br/>
				<input type="text" name="adr_3" maxLength="50" style="width: 440px" value="<?php  
					if (isset($_POST['adr_3']))
			{
				echo htmlspecialchars($_POST['adr_3'], ENT_QUOTES, 'UTF-8');
			}
			?>" /><br/>
			</td>
	    </tr>
		<tr>
			<td align="left">City:</td>
			<td align="left"><input type="text" name="city" maxLength="50  value="<?php  
					if (isset($_POST['city']))
			{
				echo htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">State:</td>
			<td align="left">
				<select name="state">
				<option value="">&nbsp;</option>
				<?php 
	foreach ($allStates as $stateCode=>$stateName) {
		$selected = '';
		
		if ($stateCode == $_POST['state']) {
			$selected = ' selected ';
		}

		echo '<option value="'.$stateCode.'" '.$selected.'>'.$stateName.'</option>';
	}

	?>
				</select>
			</td>
	    </tr>
		<tr>
			<td align="left">Zip:</td>
			<td align="left"><input type="text" name="postal_code" maxLength="25" value="<?php  
			if (isset($_POST['postal_code']))
			{
				echo htmlspecialchars($_POST['postal_code'], ENT_QUOTES, 'UTF-8');
			}
			 ?>"/></td>
			<td align="left">State (Other):</td>
			<td align="left"><input type="text" name="state_name" maxLength="50" value="<?php  
			if(isset($_POST['state_name'])){
			
				echo htmlspecialchars($_POST['state_name'], ENT_QUOTES, 'UTF-8');
			
			} ?>"/></td>
	    </tr>
		<tr>
			<td align="left">Sub-zip:</td>
			<td align="left"><input type="text" name="sub_postal" maxLength="25" value="<?php 
			if (isset($_POST['sub_postal']))
			{
				echo htmlspecialchars($_POST['sub_postal'], ENT_QUOTES, 'UTF-8');
			}
		  ?>"/></td>
			<td align="left">Country:</td>
			<td align="left">
			<select name="country">
				<option value="">&nbsp;</option>
			<?php 
	foreach ($allCountries as $countryCode=>$countryName) {
		$selected = '';
		
		if ($countryCode == $_POST['country']) {
			$selected = ' selected ';
		}

		echo '<option value="'.$countryCode.'" '.$selected.'>'.$countryName.'</option>';
	}

	?>
			</select>
			</td>
			<td colspan="2"></td>
	    </tr>
		<tr>
			<td align="left">E-Mail Type:</td>
			<td align="left" colspan="3">
				<input type="radio" class="checkbox" name="mail_type" id="mt1" value="HTML" <?php 
			if (isset($_POST['mail_type']))
			{
			
	echo ($_POST['mail_type']=='HTML' || empty($_POST['mail_type']))?' checked ':
	'';
	}
	?> />
				<label for="mt1">HTML</label>
				&nbsp;or&nbsp;
				<input type="radio" class="checkbox" name="mail_type" id="mt2" value="Text" <?php 
				if (isset($_POST['mail_type']))
			{
				
				
	echo ($_POST['mail_type']=='Text')?' checked ':
	'';
			}
	?> />
				<label for="mt2">Text</label>
			</td>			
	    </tr>
	</table>
	<h3>Work details</h3>
	<table width="580" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td align="left">Company Name:</td>
			<td align="left"><input type="text" name="company_name" maxLength="50"  value="<?php 
			if (isset($_POST['company_name']))
			{
				echo htmlspecialchars($_POST['company_name'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Job Title:</td>
			<td align="left"><input type="text" name="job_title" maxLength="50"  value="<?php 
			if (isset($_POST['job_title']))
			{
				echo htmlspecialchars($_POST['job_title'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Work phone:</td>
			<td align="left"><input type="text" name="wrk_num" maxLength="50"  value="<?php 
				if (isset($_POST['wrk_num']))
			{
				echo htmlspecialchars($_POST['wrk_num'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td colspan="2"></td>
	    </tr>
	</table>
	<h3>Custom Fields &amp; Notes</h3>
	<table width="580" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td align="left">Custom Field 1:</td>
			<td align="left"><input type="text" name="custom_field_1" maxLength="50"  value="<?php 
				if (isset($_POST['custom_field_1']))
			{
				echo htmlspecialchars($_POST['custom_field_1'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Custom Field 2:</td>
			<td align="left"><input type="text" name="custom_field_2" maxLength="50"  value="<?php  
			if (isset($_POST['custom_field_2']))
			{
				echo htmlspecialchars($_POST['custom_field_2'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Custom Field 3:</td>
			<td align="left"><input type="text" name="custom_field_3" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_3']))
			{
				echo htmlspecialchars($_POST['custom_field_3'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Custom Field 4:</td>
			<td align="left"><input type="text" name="custom_field_4" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_4']))
			{
				echo htmlspecialchars($_POST['custom_field_4'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Custom Field 5:</td>
			<td align="left"><input type="text" name="custom_field_5" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_5']))
			{
				echo htmlspecialchars($_POST['custom_field_5'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Custom Field 6:</td>
			<td align="left"><input type="text" name="custom_field_6" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_6']))
			{
				echo htmlspecialchars($_POST['custom_field_6'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Custom Field 7:</td>
			<td align="left"><input type="text" name="custom_field_7" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_7']))
			{
				echo htmlspecialchars($_POST['custom_field_7'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Custom Field 8:</td>
			<td align="left"><input type="text" name="custom_field_8" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_8']))
			{
				echo htmlspecialchars($_POST['custom_field_8'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Custom Field 9:</td>
			<td align="left"><input type="text" name="custom_field_9" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_9']))
			{
				echo htmlspecialchars($_POST['custom_field_9'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Custom Field 10:</td>
			<td align="left"><input type="text" name="custom_field_10" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_10']))
			{
				echo htmlspecialchars($_POST['custom_field_10'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Custom Field 11:</td>
			<td align="left"><input type="text" name="custom_field_11" maxLength="50"  value="<?php  
			if (isset($_POST['custom_field_11']))
			{
				echo htmlspecialchars($_POST['custom_field_11'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Custom Field 12:</td>
			<td align="left"><input type="text" name="custom_field_12" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_12']))
			{
				echo htmlspecialchars($_POST['custom_field_12'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Custom Field 13:</td>
			<td align="left"><input type="text" name="custom_field_13" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_13']))
			{
				echo htmlspecialchars($_POST['custom_field_13'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left">Custom Field 14:</td>
			<td align="left"><input type="text" name="custom_field_14" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_14']))
			{
				echo htmlspecialchars($_POST['custom_field_14'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
	    </tr>
		<tr>
			<td align="left">Custom Field 15:</td>
			<td align="left"><input type="text" name="custom_field_15" maxLength="50"  value="<?php 
			if (isset($_POST['custom_field_15']))
			{
				echo htmlspecialchars($_POST['custom_field_15'], ENT_QUOTES, 'UTF-8');
			}
			 ?>" /></td>
			<td align="left"></td>
			<td align="left"></td>
	    </tr>
		<tr>
			<td align="left">Notes:</td>
			<td align="left" colspan="3">
			<textarea rows="10" cols="50" name="notes"><?php 
			if (isset($_POST['notes']))
			{
				echo htmlspecialchars($_POST['notes'], ENT_QUOTES, 'UTF-8');
			}
			 ?></textarea>
			</td>
	    </tr>
		<tr>
			<td align="center" colspan="4">			 
			    <input type="submit" name="submit" value="Add Contact" />
		    </td>
		</tr>
	</table>
</div>
</form>
</div>
</body>
</html>