<?php header('Content-Type: text/html; charset=UTF-8');
include_once('cc_class.php');

$ccCampaignOBJ = new CC_Campaign();
$ccListOBJ = new CC_List();
// if we have post fields means that the form have been submitted.
	
	if (!empty($_POST['cmp_id'])) {

$cleanhtmlcontent = htmlspecialchars_decode(stripslashes($_POST["cmp_html_body"]));

        $_SESSION['cmp_id'] = $_POST['cmp_id'];
		$postFields = array();
        $postFields["cmp_name"] = $_POST["cmp_name"];
        $postFields["cmp_type"] = $_POST["cmp_type"];  
        $postFields["cmp_status"] = $_POST["cmp_status"]; 
        $postFields["cmp_date"] = $_POST["cmp_date"];  
		$postFields["cmp_subject"] = $_POST["cmp_subject"];
		$postFields["cmp_from_name"] = $_POST["cmp_from_name"];
		$postFields["cmp_from_email"] = $_POST["cmp_from_email"];   
		$postFields["cmp_reply_email"] = $_POST["cmp_reply_email"];
		$postFields["cmp_perm_reminder"] = $_POST["cmp_perm_reminder"];
        $postFields["cmp_txt_reminder"] = $_POST["cmp_txt_reminder"];  
		$postFields["cmp_as_webpage"] = $_POST["cmp_as_webpage"];
		$postFields["cmp_as_webtxt"] = $_POST["cmp_as_webtxt"];
		$postFields["cmp_as_weblink"] = $_POST["cmp_as_weblink"];
		$postFields["cmp_grt_sal"] = $_POST["cmp_grt_sal"];
		$postFields["cmp_grt_name"] = $_POST["cmp_grt_name"];
		$postFields["cmp_grt_str"] = $_POST["cmp_grt_str"];
		$postFields["cmp_org_name"] = $_POST["cmp_org_name"];
        $postFields["cmp_org_addr1"] = $_POST["cmp_org_addr1"]; 
        $postFields["cmp_org_addr2"] = $_POST["cmp_org_addr2"]; 
        $postFields["cmp_org_addr3"] = $_POST["cmp_org_addr3"]; 
        $postFields["cmp_org_city"] = $_POST["cmp_org_city"];   
		$postFields["org_state_us"] = $_POST["org_state_us"];// The Code is looking for a State Code For Example TX instead of Texas
		$postFields["org_state"] = $_POST["org_state"];
		$postFields["org_country"] = $_POST["org_country"]; // The Code is looking for a Country Code For Example us instead of United States of America                                     
		$postFields["org_zip"] = $_POST["org_zip"];
		$postFields["cmp_forward"] = $_POST["cmp_forward"];
        $postFields["cmp_fwd_email"] = $_POST["cmp_fwd_email"];    
        $postFields["cmp_subscribe"] = $_POST["cmp_subscribe"];       
        $postFields["cmp_sub_link"] = $_POST["cmp_sub_link"]; 
        $postFields["cmp_email_format"] = $_POST["cmp_email_format"]; 
        $postFields["cmp_mail_type"] = $_POST["cmp_mail_type"];     
		$postFields["cmp_html_body"] = $cleanhtmlcontent;
        $postFields["cmp_text_body"] = $_POST["cmp_text_body"]; 
        $postFields["cmp_style_sheet"] = $_POST["cmp_style_sheet"];    
		$postFields["lists"] = $_POST["lists"];

        $campaign_id = ($_POST['cmp_id'] == 'urn:uuid:E8553C09F4xcvxCCC53F481214230867087') ? (null) : ($_POST['cmp_id']);
        $verror = $ccCampaignOBJ->validateCampaign($campaign_id, $postFields);
		$campaignXML = $ccCampaignOBJ->createCampaignXML($campaign_id, $postFields);
		                  
     if($verror === true) {
         $error = $verror;
     } else {   
         if($_REQUEST['operation'] == 'edit') {  // add campaign
		    if (!$ccCampaignOBJ->editCampaign($_POST['cmp_id'], $campaignXML)) {
			    $error = true;
		    } else {		
			    if ($_POST['remove_lists']==1) {
				        $ccCampaignOBJ->deleteCampaign($_POST['cmp_id']);
			        }
			    $error = false;
		      }
         } elseif($_REQUEST['operation'] == 'new') { // create campaign
                if (!$ccCampaignOBJ->addCampaign($campaignXML)) {
                    $error = true;
                } else {
                    $error = false;
                    $_POST = array();
                }    
         }
      }
     
	} else {
	
	//populate new campaign fields with default info
	/** write contents of template to file for import to CC **/
	$ch = curl_init("http://www.east91st.org/e91-bulletin/");
	$fp = fopen("txt/EmailContent.txt", "w");
	
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
	}
     
	$campaign = ($_REQUEST['operation']=='edit') ? $ccCampaignOBJ->getCampaignDetails($_REQUEST['id']) : array();
    
    if( $ccCampaignOBJ->sent_recived_debug ) { 
           echo '<div><p style="color: blue">We got the following output: </p>';
           var_dump($campaign); echo '<hr/></div>';
    }
	//get all available lists for the current CC account.
    $allAssocLists = $ccListOBJ->getAccountLists();   
	$allLists = $ccListOBJ->getLists();
	$allStates = $ccCampaignOBJ->getStates();
	$allCountries = $ccCampaignOBJ->getCountries();
    $emailHtmlBody = $ccCampaignOBJ->getEmailIntro('EmailContent'); 
    $emailTextBody = $ccCampaignOBJ->getEmailIntro('EmailTextContent');
    $permissioReminder = $ccCampaignOBJ->getEmailIntro('PermissionReminder');
	?>
<?php  include_once('header.php'); ?>
<script type="text/javascript" language="javascript">
	function checkLists(obj) {
		var style = obj.checked;
		var cont = document.getElementById('chk_container');
		inputs = cont.getElementsByTagName('input');
		for (i=0;i<inputs.length;i++) {
			if (inputs[i]!=obj) {
				inputs[i].disabled = style;
			}
		}
	}
    
 function display_div(show){
   document.getElementById(show).style.display = "";
 }
 
 function hide_div(hide){
   document.getElementById(hide).style.display = "none";
 }    
</script>
 
<div align="center" style="width: 600px; margin:0 auto">
<h2><?php echo ($_REQUEST['operation']=='edit') ? ('Edit Campaign') : ('Add a New Campaign') ?></h2>
<?php 
	// Here we add an area where messages  are displayed (error or success messages).
	
	if (isset($error)) {		
		if ($error === true) {
			$class = "error";
			$message = $ccCampaignOBJ->lastError;
		} else {
			$class = "success";
			$message = ($_REQUEST['operation']=='edit') ? "Campaign edited." : "Campaign added.";
            if($_REQUEST['operation']=='new'){
            $http_addres_list = 'list_campaigns.php?list=all';
            echo "<body onload=\"timeout('$http_addres_list', 1000)\">"; 
            }
		}
		echo '<div class="'.$class.'">';
		echo $message;
		echo '</div>';
	}

?> 
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" id="campaign" name="campaign">
<input type="hidden" name="cmp_id" value="<?php  echo !empty($campaign) ? $campaign['campaignId'] : 'urn:uuid:E8553C09F4xcvxCCC53F481214230867087' ?>" />
<input type="hidden" name="id" value="<?php  echo !empty($campaign) ? $campaign['campaignId'] : 'urn:uuid:E8553C09F4xcvxCCC53F481214230867087' ?>" />
<input type="hidden" name="operation" value="<?php echo $_REQUEST['operation'] ?>" />  
<input type="hidden" name="cmp_status" value="<?php  echo $campaign['Status'] ?>" /> 
<input type="hidden" name="cmp_date" value="<?php  echo $campaign['Date'] ?>" /> 
<input type="hidden" name="cmp_type" value="<?php  echo $campaign['CampaignType'] ?>" />

<?php
            # Error Case Settings 
            $campaign_name = ($class == "error") ? $_POST["cmp_name"] : stripslashes(html_entity_decode($campaign['Name']));
            $subject = ($class == "error") ? $_POST["cmp_subject"] : stripslashes(html_entity_decode($campaign['Subject']));
            $from_name = ($class == "error") ? $_POST["cmp_from_name"] : stripslashes(html_entity_decode("East 91st Street Christian Church"));
            $cmp_from_email = explode('|' ,"info@east91st.org");
            $cmp_reply_email = explode('|' , "info@east91st.org"); 
            $perm_reminder = ($class == "error") ? $_POST["cmp_perm_reminder"] : stripslashes(html_entity_decode($campaign['PermissionReminder']));            
            $perm_reminder_txt_ed = ($class == "error") ? stripslashes($_POST["cmp_txt_reminder"]) : stripslashes(html_entity_decode($campaign['PermissionReminderText'])); 
            $perm_reminder_txt_ad = ($class == "error") ? stripslashes($_POST["cmp_txt_reminder"]) : stripslashes(html_entity_decode($permissioReminder));
            $view_as_webpage_text =  html_entity_decode($campaign['ViewAsWebpageText']) != '' ? html_entity_decode($campaign['ViewAsWebpageText']) : 'Having trouble viewing this email?'; 
            $view_as_webpage_text = ($class == "error") ? $_POST["cmp_as_webtxt"] : $view_as_webpage_text;
            $view_as_webpage_link_text = stripslashes(html_entity_decode($campaign['ViewAsWebpageLinkText'])) != '' ? stripslashes(html_entity_decode($campaign['ViewAsWebpageLinkText'])) : 'Click here';     
            $view_as_webpage_link_text = ($class == "error") ? $_POST["cmp_as_weblink"] : $view_as_webpage_link_text;
            $cmp_as_webpage_err = !empty($_POST["cmp_as_webpage"]) ? $_POST["cmp_as_webpage"] : 'NO';           
            $view_as_webpage = ($class == "error") ? $cmp_as_webpage_err : stripslashes(html_entity_decode($campaign['ViewAsWebpage']));
            $view_as_webpage = !empty($view_as_webpage) ? $view_as_webpage : 'YES'; 
            $include_forward_email_err = !empty($_POST["cmp_forward"]) ? $_POST["cmp_forward"] : 'NO';
            $include_forward_email = ($class == "error") ? $include_forward_email_err : stripslashes(html_entity_decode($campaign['IncludeForwardEmail']));
            $include_forward_email = !empty($include_forward_email) ? $include_forward_email : 'YES';
            $include_subscribe_link_err = !empty($_POST["cmp_subscribe"]) ? $_POST["cmp_subscribe"] : 'NO';
            $include_subscribe_link = ($class == "error") ? $include_subscribe_link_err : stripslashes(html_entity_decode($campaign['IncludeSubscribeLink']));
            $include_subscribe_link = !empty($include_subscribe_link) ? $include_subscribe_link : 'YES';                    $greeting_sal_ed = ($class == "error") ? $_POST["cmp_grt_sal"] : stripslashes(html_entity_decode($campaign['GreetingSalutation']));
            $greeting_sal_ad = ($class == "error") ? $_POST["cmp_grt_sal"] : 'Dear';                 
            $greeting_string_ed = ($class == "error") ? $_POST["cmp_grt_str"] : stripslashes(html_entity_decode($campaign['GreetingString']));
            $greeting_string_ad = ($class == "error") ? $_POST["cmp_grt_str"] : 'Greetings!';  
            $greeting_name = ($class == "error") ? $_POST["cmp_grt_name"] : stripslashes(html_entity_decode($campaign['GreetingName'])); 
            $organization_name = ($class == "error") ? $_POST["cmp_org_name"] : stripslashes(html_entity_decode("East 91st Street Christian Church")); 
            $address_1 = ($class == "error") ? $_POST["cmp_org_addr1"] : stripslashes(html_entity_decode("6049 East 91st Street"));
            $address_2 = ($class == "error") ? $_POST["cmp_org_addr2"] : stripslashes(html_entity_decode($campaign['OrganizationAddress2']));
            $address_3 = ($class == "error") ? $_POST["cmp_org_addr3"] : stripslashes(html_entity_decode($campaign['OrganizationAddress3']));
            $city = ($class == "error") ? $_POST["cmp_org_city"] : stripslashes(html_entity_decode("Indianapolis"));
            $us_state_code = ($class == "error") ? $_POST["org_state_us"] : "IN";
            $non_us_state = ($class == "error") ? $_POST["org_state"] : stripslashes(html_entity_decode($campaign['OrganizationInternationalState']));
            $zip_code = ($class == "error") ? $_POST["org_zip"] : stripslashes(html_entity_decode("46250"));
            $country_code = ($class == "error") ? $_POST["org_country"] : "us";
            $forward_email = stripslashes(html_entity_decode($campaign['ForwardEmailLinkText'])) != '' ? stripslashes(html_entity_decode($campaign['ForwardEmailLinkText'])) : 'Forward email';  
            $forward_email = ($class == "error") ? $_POST["cmp_fwd_email"] : $forward_email;
            $subscribe_me = stripslashes(html_entity_decode($campaign['SubscribeLinkText'])) != '' ? stripslashes(html_entity_decode($campaign['SubscribeLinkText'])) : 'Subscribe me!';        
            $subscribe_me = ($class == "error") ? $_POST["cmp_sub_link"] : $subscribe_me;  
            $email_content_format = ($class == "error") ? $_POST["cmp_mail_type"] : $campaign['EmailContentFormat'];  
            $email_content_format = !empty($email_content_format) ? $email_content_format : 'HTML';
            $campaign['lists'] = ($class == "error") ? $_POST["lists"] : $campaign['lists'];
            $email_html_content_ed = ($class == "error") ? stripslashes($_POST["cmp_html_body"]) : stripslashes(html_entity_decode($campaign['EmailContent']));
            $email_html_content_ad = ($class == "error") ? stripslashes($_POST["cmp_html_body"]) : stripslashes(html_entity_decode($emailHtmlBody));  
            $email_text_content_ed = ($class == "error") ? stripslashes($_POST["cmp_text_body"]) : stripslashes(strip_tags(html_entity_decode($campaign['EmailTextContent']))); 
            $email_text_content_ad = ($class == "error") ? stripslashes($_POST["cmp_text_body"]) : stripslashes(html_entity_decode($emailTextBody));
            $email_style_sheet = ($class == "error") ? stripslashes($_POST["cmp_style_sheet"]) : stripslashes(html_entity_decode($campaign['StyleSheet']));  
            ?>        
            
<div style="width:300px; margin:0 auto">
	<fieldset id="chk_container">
		<legend> Send campaign to following lists: </legend>
	<?php
    $count = 0;
	foreach ($allLists as $k=>$item) {
        $count ++;
		$checked = '';
		if ($item['id'] == "http://api.constantcontact.com/ws/customers/e91nancyl2/lists/192") {
			$checked = ' checked ';
		}
		echo '<input type="checkbox" '.$checked.' style="width: 20px;" name="lists[]" value="'.$item['id'].'" id="chk_'.$k.'" /> <label for="chk_'.$k.'">'.$item['title'].'</label><br/>';
	}
	?>		
	</fieldset>
</div>	

 <div>  
 <h3>Campaign Information</h3>    
    <table width="580" border="0" cellpadding="2" cellspacing="0">
      <tr>
            <td align="left" valign="top">Campaign Name*:</td>
            <td align="right" colspan="3"><input type="text" name="cmp_name" style="width: 440px" maxLength="100" value="<?php  echo $campaign_name ?>" /></td> 
      </tr>
      <tr>                                   
           <td align="left" valign="top">Campaign Type:</td> 
            <td align="left" colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<label for="ctype" style="font-size: 13px"><?php echo ($campaign['CampaignType'] != 'STOCK') ? ('Custom HTML/XHTML code Campaign') : ('Template-based Campaign') ?></label></td> 
      </tr>
 </table>
	<h3>*Message Header</h3>	
	<table width="580" border="0" cellpadding="2" cellspacing="0">   

		<tr>
			<td align="left" valign="top">Subject*:</td>
			<td align="right" colspan="3"><input type="text" name="cmp_subject" style="width: 440px" maxLength="100" value="<?php  echo $subject ?>" /></td>
	  </tr>
      <tr>
            <td align="left" valign="top">From Name*:</td>
            <td align="right" colspan="3"><input type="text" name="cmp_from_name" style="width: 440px" maxLength="100" value="<?php  echo $from_name ?>" /></td>
      </tr>
          <tr>
            <td align="left" valign="top">From Email Address*:</td>
            <td align="right" colspan="3">
            <select name="cmp_from_email" style="width: 440px">
         <?php               
            foreach ($allAssocLists as $fromEmail) {     
                if($class == "error"){
                    $selected = ($fromEmail['Email'] == $cmp_from_email[0]) ? ('selected="selected"') : (''); 
                } else {
                    $selected = ($fromEmail['Email'] == "info@east91st.org") ? ('selected="selected"') : (''); 
                }
                echo '<option value="'.$fromEmail['Email'].'|'.$fromEmail['Id'].'" '.$selected.' >'.$fromEmail['Email'].'</option>';
             }
         ?>
                </select>
           </td>
      </tr>
        <tr>
            <td align="left" valign="top">Reply Email Address*:</td>
            <td align="right" colspan="3" >
            <select name="cmp_reply_email" style="width: 440px">
         <?php               
            foreach ($allAssocLists as $replyToEmail) {       
                if($class == "error"){
                    $selected = ($replyToEmail['Email'] == $cmp_reply_email[0]) ? ('selected="selected"') : ('');
                } else {
                    $selected = ($replyToEmail['Email'] == "info@east91st.org") ? ('selected="selected"') : ('');     
                }
                echo '<option value="'.$replyToEmail['Email'].'|'.$replyToEmail['Id'].'" '.$selected.'>'.$replyToEmail['Email'].'</option>';
              }
          ?>
                </select>
           </td>
      </tr>    
         <tr>
            <td align="left">Permission Reminder:</td>
            <td align="left" colspan="3">               
            <input type="radio" name="cmp_perm_reminder" maxLength="100" <?php if($perm_reminder != 'YES' || empty($campaign)) echo 'checked="checked"' ?> value="NO" onClick="hide_div('reminder')" style="width: 10px"/>  <label for="off" style="font-size: 13px">Off</label>
                &nbsp;&nbsp; 
             <input type="radio" name="cmp_perm_reminder" maxLength="100" <?php if($perm_reminder == 'YES') echo 'checked="checked"' ?> value="YES" onClick="display_div('reminder')" style="width: 10px"/> <label for="onn" style="font-size: 13px">On</label>
            </td>
      </tr>
      
            <tr>
            <td align="right" colspan="4"> <div id="reminder" style="<?php echo $perm_reminder == 'YES' ? ('') : ('display:none') ?>">          
              <textarea rows="5" cols="53" name="cmp_txt_reminder"><?php  echo ($_REQUEST['operation'] == 'edit') ? ($perm_reminder_txt_ed) : ($perm_reminder_txt_ad) ?></textarea>     </div>
            </td>
      </tr>
    
              <tr>
            <td align="left">Webpage Version:</td>
            <td align="left" colspan="3">          
            <input type="checkbox" name="cmp_as_webpage"  <?php if($view_as_webpage == 'YES') echo 'checked="checked"' ?> value="YES" style="width: 10px" /> Include a link to view a webpage version of this email </td> 
      </tr>
        <tr>
            <td align="left">Text:</td>
            <td align="left"><input type="text" style="width: 240px" name="cmp_as_webtxt" maxLength="100" value="<?php  echo $view_as_webpage_text ?>" /></td>
            <td align="left">Link Text:</td>
            <td align="left"><input type="text" style="width: 100px" name="cmp_as_weblink" maxLength="100" value="<?php echo $view_as_webpage_link_text ?>"/></td>
        </tr>	
	</table>
    
<input type="hidden" name="cmp_grt_sal" maxLength="100" value="<?php  echo ($_REQUEST['operation'] == 'edit') ? ($greeting_sal_ed) : ($greeting_sal_ad) ?>"/>
<input type="hidden" name="cmp_grt_name" value="FirstName">
<input type="hidden" name="cmp_grt_str" maxLength="100" value="<?php  echo ($_REQUEST['operation'] == 'edit') ? ($greeting_string_ed) : ($greeting_string_ad) ?>"/>
    
	<h3>*Message Footer </h3>
	<table width="580" border="0" cellpadding="2" cellspacing="0">
         <tr>
            <td align="left" valign="top">Organization Name*:</td>
            <td align="right" colspan="3"><input type="text" name="cmp_org_name" style="width: 440px" maxLength="100" value="<?php  echo $organization_name ?>" /></td>
      </tr>
        <tr>
            <td align="left" valign="top">Address 1*:</td>
            <td align="right" colspan="3"><input type="text" name="cmp_org_addr1" style="width: 440px" maxLength="100" value="<?php  echo $address_1 ?>" /></td>
      </tr>
      <tr>
            <td align="left" valign="top">Address 2:</td>
            <td align="right" colspan="3" ><input type="text" name="cmp_org_addr2" style="width: 440px" maxLength="100" value="<?php  echo $address_2 ?>" /></td>
      </tr>
      <tr>
            <td align="left" valign="top">Address 3:</td>
            <td align="right" colspan="3" ><input type="text" name="cmp_org_addr3" style="width: 440px" maxLength="100" value="<?php  echo $address_3 ?>" /></td>
      </tr>
      <tr>
            <td align="left" valign="top">City*:</td>
            <td align="right" colspan="3" ><input type="text" name="cmp_org_city" style="width: 440px" maxLength="100" value="<?php  echo $city ?>" /></td>
      </tr>
      <tr>
            <td align="left" valign="top">State*:</td>
            <td align="right">
            <select name="org_state_us" style="width: 185px">
            <option value=""> -- NON US/CA -- </option>  
      <?php             
         foreach ($allStates as $stateCode=>$stateName) {
            $selected = '';   
            if ($us_state_code == $stateCode) {
                $selected = ' selected ';
            }
            echo '<option value="'.$stateCode.'" '.$selected.'>'.$stateName.'</option>';
         }
    ?>
           </select>
           </td>
    <td align="center" valign="top"><span>OR</span></td>
    <td align="right"><input type="text" style="width: 185px" name="org_state" maxLength="100" value="<?php  echo $non_us_state ?>" /></td>
      </tr>
      <tr>
            <td align="left" valign="top">Zip/Postal Code*:</td>
            <td align="right"><input type="text" class="required" style="width: 185px" name="org_zip" maxLength="100" value="<?php  echo $zip_code ?>" /></td>
            <td align="center" valign="top">Country*:</td>
            <td align="right" >
                        <select name="org_country" style="width: 185px">
                        <option value=""></option>
            <?php               
            foreach ($allCountries as $countryCode => $countryName) {
                $selected = '';  
                if ($country_code == $countryCode) {
                    $selected = ' selected ';
                }
                echo '<option value="'.$countryCode.'" '.$selected.'>'.$countryName.'</option>';
            }
            ?>
                </select>
           </td>        
      </tr>
      <tr>
            <td align="left">Forward Email to a Friend:</td>
            <td align="left" colspan="3">           
            <input type="checkbox" name="cmp_forward" maxLength="100" <?php if($include_forward_email == 'YES') echo 'checked="checked"' ?> value="YES" style="width: 20px"/> <input type="text" name="cmp_fwd_email" style="width: 200px" maxLength="100" value="<?php  echo $forward_email ?>" /> (Added to your email) 
              <br/> 
             <input type="checkbox" name="cmp_subscribe" maxLength="100" <?php if($include_subscribe_link == 'YES') echo 'checked="checked"' ?> value="YES" style="width: 20px"/> <input type="text" name="cmp_sub_link" style="width: 200px" maxLength="100" value="<?php  echo $subscribe_me ?>" />  (Added to the forwarded emails)
            </td>
      </tr>
                         
    </table>
<?php if($campaign['CampaignType'] != 'STOCK'){ ?>    
    <h3> EMAIL BODY </h3>
    <table width="580" border="0" cellpadding="2" cellspacing="0">
     <tr>
        <td align="center" colspan="2"><label for="htm" style="font-size: 12px"> HTML/XHTML VERSION </label> </td>
     </tr>
            <tr>
            <td align="left">E-Mail Type:</td>
            <td align="left">
                <input type="radio" class="checkbox" onClick="hide_div('css');hide_div('ss');" name="cmp_mail_type" id="mt1" value="HTML" <?php 
    echo ($email_content_format == 'HTML' || empty($campaign))?'checked="checked"':'';?> /><label for="mt1" style="font-size: 11px">HTML</label>
    &nbsp;or&nbsp;
                <input type="radio" class="checkbox" onClick="display_div('css');display_div('ss');" name="cmp_mail_type" id="mt2" value="XHTML" <?php 
    echo ($email_content_format == 'XHTML')?'checked="checked"':'';?>/><label for="mt2" style="font-size: 11px">XHTML</label>
            </td>            
        </tr>
        <tr>
            <td align="center" colspan="2">
            <textarea rows="12" cols="65" name="cmp_html_body"><?php  echo ($_REQUEST['operation'] == 'edit') ? ($email_html_content_ed) : ($email_html_content_ad) ?></textarea>
            </td>
        </tr>
        <tr>
        <td align="center" colspan="2"><div id="ss" style="<?php echo $email_content_format == 'XHTML' ? ('') : ('display:none') ?>"><label for="ss" style="font-size: 12px"> STYLE SHEET </label></div></td>
        </tr>
        <tr>
            <td align="center" colspan="2"> <div id="css" style="<?php echo $email_content_format == 'XHTML' ? ('') : ('display:none') ?>">
            <textarea rows="8" cols="65" name="cmp_style_sheet"><?php  echo $email_style_sheet ?></textarea>     </div>
            </td>
        </tr>        
        <tr>
        <td align="center" colspan="2"><label for="tv" style="font-size: 12px"> TEXT VERSION </label></td>
        </tr>
        <tr>
            <td align="center" colspan="2">
            <textarea rows="8" cols="65" name="cmp_text_body"><?php  echo ($_REQUEST['operation'] == 'edit')? ($email_text_content_ed) : ($email_text_content_ad) ?></textarea>
            </td>
        </tr>
      <tr>
        <tr>
            <td align="center" colspan="2">                 
                <input type="submit" name="submit" value="<?php echo ($_REQUEST['operation']=='edit') ? ('Update this Campaign') : ('Add New Campaign') ?>" />
            </td>
        </tr>
    </table>
<?php  } else {  ?>    
<div align="center"><br/>  
<input type="submit" name="submit" value="<?php echo ($_REQUEST['operation']=='edit') ? ('Update this Campaign') : ('Add New Campaign') ?>" />
</div>  
 <?php  }   ?>    
 </div>  
</form>
</div>
