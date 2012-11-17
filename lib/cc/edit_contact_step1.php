<?php
header('Content-Type: text/html; charset=UTF-8');
include_once('cc_class.php');
$ccContactOBJ = new CC_Contact();
//if we have post fields means that the form have been submitted.
$message = "";
if (!empty($_POST)) {
	$contact = $ccContactOBJ->getSubscribers(urlencode($_POST['src_mail']));
	if (empty($contact['items']) || empty($_POST['src_mail'])) {
		//$message = 'Searched contact does not exist in our database. You may want to <a href="add_contact.php">Add it.</a>';
		header('Location: simple_form.php?email='.urlencode(htmlspecialchars($_POST['src_mail'], ENT_QUOTES, 'UTF-8')));		
	}
	else {
		header('Location: edit_contact.php?email='.urlencode(htmlspecialchars($_POST['src_mail'], ENT_QUOTES, 'UTF-8')));		
	}
}
?>

<?php include_once('header.php');?>
<div align="center">
<?php  if($message != null || $message != "")
{
echo $message;
}?>
<h2>Enter Contact Email Address</h2>
<form method="post" action="">
E-mail address: <input type="text" name="src_mail" /> 
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Sign Up" />
</form>
</div>
<?php include_once('footer.php'); ?>