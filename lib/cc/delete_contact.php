<?php include_once('header.php');?>

<?php 
include_once('cc_class.php');
$ccContactOBJ = new CC_Contact();
if (empty($_GET['email'])) {
	return;
}

if ($ccContactOBJ->removeSubscriber($_GET['email'])) {
	echo 'Address '.$_GET['email'].' have been successfully deleted.';
}
else {
	echo 'An error occured.';
}

?> 

<?php include_once('footer.php'); ?>