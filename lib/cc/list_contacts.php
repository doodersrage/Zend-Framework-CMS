<?php
header('Content-Type: text/html; charset=UTF-8');
include_once('cc_class.php');
$ccContactOBJ = new CC_Contact();
	$pageNo = 1;
	
	if (!empty($_POST['navmode']) && $_POST['navmode'] != 'first') {
		$pageNo = $_POST['pageno']+1;
	}

	$start = 50*($pageNo-1);
	$requestedPage = '';
	
	if (!empty($_POST['navmode']) && $_POST['navmode'] == 'next') {
		$requestedPage = $_POST['nextPage'];
	}

	
	if (!empty($_POST['navmode']) && $_POST['navmode'] == 'first') {
		$requestedPage = $_POST['firstPage'];
	}

	$allMySubscribers = $ccContactOBJ->getSubscribers('',$requestedPage);
	$paginationString = '';
	
	if (!empty($allMySubscribers['first'])) {
		$paginationString .= '<input type="button" value="1. First Page" onclick="firstPage();">&nbsp;&nbsp;&nbsp;';
	}

	
	if (!empty($allMySubscribers['next'])) {
		$paginationString .= '<input type="button" value="Next Page &raquo;" onclick="nextPage();" />';
	}

	?>
<?php  include_once('header.php'); ?>
<script language="javascript" type="text/javascript">
function nextPage() {
	document.getElementById('navmode').value = "next";
	document.getElementById('pagFrm').submit();
}
function firstPage() {
	document.getElementById('navmode').value = "first";
	document.getElementById('pagFrm').submit();
}
</script>
<div align="center">
<h2>List All Contacts</h2>
	<form name="pagFrm" id="pagFrm" method="post">
		<input type="hidden" name="navmode" id="navmode" value="" />
		<input type="hidden" name="nextPage" id="nextPage" value="<?php  echo $allMySubscribers['next'] ?>" />
		<input type="hidden" name="firstPage" id="firstPage" value="<?php  echo $allMySubscribers['first'] ?>" />
		<input type="hidden" name="pageno" id="pageno" value="<?php  echo $pageNo ?>" />
	</form>
	<?php  echo $paginationString ?>
</div>
<table align="center" width="700">
	<tr class="head">
		<th>#</th>
		<th>Email</th>
		<th>Name</th>
		<th>Status</th>
		<th>&nbsp;</th>
	</tr>
<?php 
 $count = 0; 
	foreach ($allMySubscribers['items'] as $key=>$item) {
        $bgcolor = ($count % 2 == 0) ? ('#FFFFFF') : ('#E0E0E0'); 
		$linkEdit = '[<a href="edit_contact.php?email='.urlencode($item['EmailAddress']).'">edit</a>]';
		
		if ($item['status']=='Active') {
			$linkDelete = '[<a href="delete_contact.php?email='.urlencode($item['EmailAddress']).'">remove</a>]';
		} else {
			$linkDelete = '[remove]';
		}

		echo '  <tr bgcolor="'.$bgcolor.'">'; 
		echo '	<td>'.(($key+1)+($start)).'</td>';
		echo '	<td>'.$item['EmailAddress'].'</td>';
		echo '	<td>'.htmlspecialchars($item['Name']).'</td>';
		echo '	<td>'.$item['status'].'</td>';
		echo '	<td align="center">'.$linkEdit.' &nbsp;&nbsp;&nbsp; '.$linkDelete.'</td>';
		echo '</tr>';
        $count++;   
	}

	?>	
</table>
<?php include_once('footer.php'); ?>