<?php $pagename="workorder-web";
require 'gazebo-header.php';
$nologo = true;
?>

<?php 
if (!isset($cms)) {
	include "menu-web.php";
        require "authcheck-web.php";
}
else {
	require "authcheck.php";
} ?>

<h2 style='text-align:center'>Submit a Work Order</h2>
<?php
if ( isset($_POST['Summary']) && ( $_POST['Summary'] != '' ) )
{
	//Save SQL Record
	$curtime = getdate();
	$sqltime = assembleDateTime($curtime['mon'],$curtime['mday'],substr($curtime['year'],2),$curtime['hours'],$curtime['minutes']);
	$_POST['Unit'] = mysqli_real_escape_string($con, $_POST['Unit']);
	$_POST['Description'] = mysqli_real_escape_string($con, $_POST['Description']);
	$_POST['Summary'] = mysqli_real_escape_string($con, $_POST['Summary']);
	$_POST['Name'] = mysqli_real_escape_string($con, $_POST['Name']);
	$querystring = "INSERT INTO WorkOrders (Unit, Summary, Description, Status, Submitted, Name, Username) VALUES
			('{$_POST['Unit']}', '{$_POST['Summary']}', '{$_POST['Description']}',
			 {$status_submitted}, '{$sqltime}', '{$_POST['Name']}', '{$_SESSION['Username']}')";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Work Order Entered.<br />";
	else
	{
		echo "Work Order failed to save.<br />";
		echo mysqli_error($con) . "<br />";

		$to = fetchSetting("WebmasterEmail", $con);
		$mailresult = mail($to, "Work Order Submittal Failure",
			"{$_POST['Name']} (user {$_SESSION['Username']}) has posted a service request to the online system.\n\r" .
			"Subject: {$_POST['Summary']}\n\r" . 
			"Description:\n\r{$_POST['Description']}" . 
			"Querystring: {$querystring}");
		if ( $mailresult )
		    echo "Email sent to webmaster.<br />";

	 	break;
        }
	$to = fetchSetting("WorkOrderEmail", $con);
	$headers = "From: {$_POST['Name']}";

	$mailresult = mail($to, "New Work Order | Unit {$_POST['Unit']} | " . $_POST['Summary'],
		"{$_POST['Name']} (user {$_SESSION['Username']}) has posted a service request to the online system.\n\r" .
		"Subject: {$_POST['Summary']}\n\r" . 
		"Description:\n\r{$_POST['Description']}", $headers);
	if ( $mailresult )
	    echo "Email sent to property manager.<br />";
}
?>
<p>
<form name='workorder' id='workorder' method='post' action=' <?php echo pageLink("workorder-web"); ?> '>
<table class='criteria'><tr><td>Your Name: </td><td><input type='text' name='Name' size='50' required='required' /></td></tr>
<tr><td>Unit # (leave blank if outside of unit) </td><td><input type='text' name='Unit' size='10' /></td></tr>
<tr><td>Summary of problem: </td><td><input type='text' name='Summary' size='50' required='required' /></td></tr>
<tr><td>Description:</td><td><textarea name='Description' cols='70' rows='5'></textarea></td></tr>
<tr><td colspan='2'><input type='submit' value='Submit' /></td></tr>
</table>
</p>

<?php include 'gazebo-footer.php'; 
?>
