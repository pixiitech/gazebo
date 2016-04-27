<?php 
$pagename = 'packages';
require 'gazebo-header.php'; 
?>

<script>

function fillInForm(fn, Idx, Unit, Recipient, Description, Type, ReceivedBy, ReturnedBy)
{
    var a = [['Idx', Idx], ['Unit', Unit], ['Recipient', Recipient], ['Description', Description],
	 ['Type', Type], ['ReceivedBy', ReceivedBy], ['ReturnedBy', ReturnedBy]];
    var updateFields = function (FieldArray) {
	for ( var i = 0; i < FieldArray.length; i++ )
	        document.forms['recordinput'].elements[FieldArray[i][0]].value = FieldArray[i][1];
    };
    switch (fn)
    {
	case 1:
  	    document.forms['recordinput'].elements['fnList'].checked = true;
	    fnList();
	    break;
	case 2:
  	    document.forms['recordinput'].elements['fnSearch'].checked = true;
	    fnSearch(function(){updateFields(a)});
	    break;
	case 3:
  	    document.forms['recordinput'].elements['fnInsert'].checked = true;
	    fnInsert(function(){updateFields(a)});
	    break;
	case 4:
  	    document.forms['recordinput'].elements['fnUpdate'].checked = true;
	    fnUpdate(function(){updateFields(a)});
	    break;
	case 5:
  	    document.forms['recordinput'].elements['fnDelete'].checked = true;
	    fnDelete(function(){updateFields(a)});
	    break;
	case 6:
  	    document.forms['recordinput'].elements['fnPickup'].checked = true;
	    fnPickup(function(){updateFields(a)});
	    break;
    }
    return;
}

</script>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Packages System</h2>

<?php
function errorMessage($msg, $fn)
{
        echo sprintf("<script>fillInForm(%u, '%u', '%s', '%s', '%s', '%s', '%s', '%s');</script>", 
	    $fn, $_POST['Idx'], $_POST['Unit'], $_POST['Recipient'], 
	    $_POST['Description'], $_POST['Type'], $_POST['ReceivedBy'], $_POST['ReturnedBy'] );

    echo("$msg" . "<br />");
}

require 'authcheck.php';

echo "<form name='recordinput' method='post' action='" . pageLink("packages") . "' enctype='multipart/form-data' ><p class='center'>
<input type='hidden' name='MAX_FILE_SIZE' value='{$max_upload_size}' />
<input type='hidden' id='SavedQuery' name='SavedQuery' value=\"{$_POST['SavedQuery']}\" />
<input id='fnList' type='radio' name='function' value='list' onClick='document.forms['recordinput'].reset(); this.checked=true;' />List&nbsp;&nbsp;
<input id='fnSearch' type='radio' name='function' value='search' />Search&nbsp;&nbsp;
<input id='fnInsert' type='radio' name='function' value='insert' onClick='document.forms['recordinput'].reset(); this.checked=true;' />Submit New&nbsp;&nbsp;
<input id='fnPickup' type='radio' name='function' value='pickup' />
Mark Picked-up&nbsp;&nbsp;
<input id='fnUpdate' type='radio' name='function' value='update' />Update&nbsp;&nbsp;";
if ($_SESSION['Level'] >= $editlevel)
{
    echo "<input id='fnDelete' type='radio' name='function' value='delete' />Delete&nbsp;&nbsp;";
}

echo "</p><table class='criteria'><tbody><tr>
<td class='formfields Search Update Delete Pickup'>
<span class='formfields Delete' hidden='hidden'>Are you sure you want to delete </span>
<span class='formfields Pickup' hidden='hidden'>Mark </span>
Package # <input id='Idx' type='text' size='5' name='Idx' />
<span class='formfields Pickup' hidden='hidden'> as picked up by resident</span>
<span class='formfields Delete Pickup' hidden='hidden'>? </span>
</td>
<td class='formfields Search Insert Update'>Unit # <input id='Unit' type='text' size='7' name='Unit' /></td></tr><tr>
<td class='formfields Search' colspan='2'>Search dates from:
<input id='StartMonth' type='text' size='2' name='StartMonth' value='MM' onClick='if (this.value==\"MM\") this.value=\"\";' />/
<input id='StartDay' type='text' size='2' name='StartDay' value='DD' onClick='if (this.value==\"DD\") this.value=\"\";' />/
<input id='StartYear' type='text' size='2' name='StartYear' value='YY' onClick='if (this.value==\"YY\") this.value=\"\";' />&nbsp to:
<input id='EndMonth' type='text' size='2' name='EndMonth' value='MM' onClick='if (this.value==\"MM\") this.value=\"\";' />/
<input id='EndDay' type='text' size='2' name='EndDay' value='DD' onClick='if (this.value==\"DD\") this.value=\"\";' />/
<input id='EndYear' type='text' size='2' name='EndYear' value='YY' onClick='if (this.value==\"YY\") this.value=\"\";' />&nbsp 
<input id='AwaitingPickup' type='checkbox' name='AwaitingPickup' value='Yes' checked=\"true\" />Awaiting Pickup Only
</td></tr><tr>
<td class='formfields Search Insert Update'>Carrier:  
<select id='Type' name='Type'>
<option value='None'></option>
<option value='USPS'>USPS</option>
<option value='UPS'>UPS</option>
<option value='FedEx'>FedEx</option>
<option value='DHL'>DHL</option>
<option value='Intl'>Intl</option>
<option value='Other'>Other</option>
</select></td>
<td class='formfields Search Insert Update'>Recipient  <input id='Recipient' type='text' size='25' name='Recipient' /></td>
</tr><tr>";
echo "<td class='formfields Search Update'>Received By  <input id='ReceivedBy' type='text' size='18' name='ReceivedBy' ";
if ($_SESSION['Level'] < $editlevel) echo "disabled='disabled' ";
echo "/></td>";
echo "<td class='formfields Search Update'>Returned By  <input id='ReturnedBy' type='text' size='18' name='ReturnedBy' ";
if ($_SESSION['Level'] < $editlevel) echo "disabled='disabled' ";
echo "/></td>";

echo "</tr>
<tr><td class='formfields Insert Update' colspan='2'>Description:  <textarea id='Description' cols='50' rows='2' name='Description' style='height:60px; width:400px'>Enter notes here.</textarea></td></tr></tbody></table>
<p class='center'><input id='submitbutton' type='submit' value='Send' /> <input type='reset' value='Clear' /></p>
</form>";


if ( !isset($_POST['function']) ) {
	$_POST['function'] = 'list';
}

switch ($_POST["function"])
{
    case "insert":
	if (( $_POST["Unit"] == "" ) || !(is_numeric( $_POST["Unit"] ) ) || !validateUnit($_POST['Unit'], $con) )
	{
	  errorMessage("Please specify a valid numeric unit number.", 3);
	  break;
	}
	if ( trim($_POST["Recipient"]) == "" )
	{
	  errorMessage("Please specify a recipient.", 3);
	  break;
	}
	if ($_POST["Type"] == "None")
	{
	  errorMessage("Please specify a carrier type.", 3);
	  break;
	}
	
	//Save SQL Record
	$curtime = getdate();
	$sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
	//$_POST['Description'] = $mysql_real_escape_string($_POST['Description']);
	//$_POST['Recipient'] = $mysql_real_escape_string($_POST['Recipient']);
	$querystring = "INSERT INTO Packages (Unit, ReceiveTime, EntryTime, PickupTime, Type, Recipient, Description, ReceivedBy) VALUES
			({$_POST['Unit']}, '{$sqltime}', '{$sqltime}', '{$_POST['PickupTime']}', '{$_POST['Type']}',
			 '{$_POST['Recipient']}', '{$_POST['Description']}', '{$_SESSION["Username"]}')";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Package Entered.<br />";
	else
	{
		errorMessage("Package failed to save. Please try again.<br />", 3);
	 	break;
        }
	$residx = fetchResidxFromUnit($_POST['Unit'], $con);
	debugText("Residx: " . $residx);
	$emailaddr = fetchResidentEmail($residx, $con);
	if ( !$emailaddr || ($emailaddr == "")) {
	    echo "Resident email address not found. Email not sent.<br />";
	}
	else {
		$emailtext = "A package has been received at the front desk for your unit. ({$_POST['Unit']})\n\r";
		$emailtext .= "Recipient: {$_POST['Recipient']}  Time: {$sqltime}  Carrier: {$_POST['Type']}\n\r";
		$emailtext .= "Description: {$_POST['Description']}\n\r";
		$subject = "Package Notification for {$_POST['Recipient']}";
		$header = "From: " . fetchSetting('Name', $con);
		$mailresult = mail($emailaddr, $subject, $emailtext, $header);
		debugText("Email sent.<br />Text: {$emailtext}<br />Header:{$header}<br />");
		if ( $mailresult )
		    echo "Email sent to resident: {$emailaddr}<br />";
		else
		    echo "Email failed to send: {$emailaddr}<br />";
	}
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

    case "update":
	if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )) )
	{
	  errorMessage( "Please specify a valid numeric index.", 4);
	  break;
	}
	if ( trim($_POST["Recipient"]) == "" )
	{
	  errorMessage("Please specify a recipient.", 3);
	  break;
	}
	if ($_POST["Type"] == "None")
	{
	  errorMessage("Please specify a carrier type.", 3);
	  break;
	}

	//Save SQL Record
	$curtime = getdate();
	$sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];

	$querystring = "UPDATE Packages SET Unit={$_POST['Unit']}, PickupTime='{$_POST['PickupTime']}', Type='{$_POST['Type']}', Recipient='{$_POST['Recipient']}', ReceivedBy='{$_POST['ReceivedBy']}',
			Description='{$_POST['Description']}' WHERE Idx= {$_POST['Idx']}";

	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Package Updated.<br />";
	else
	{
		errorMessage("Package failed to update.<br />", 3);
	 	break;
        }
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

    case "delete":
	if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )))
	{
	  errorMessage("Please specify a valid numeric index.", 5);
	  break;
	}
	if ( !(packageExists($_POST["Idx"],$con)))
	{
	  errorMessage("Specified package number does not exist.<br />", 5);
	  break;
	}
	$querystring = "DELETE FROM Packages WHERE Idx={$_POST['Idx']}";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Package #{$_POST["Idx"]} deleted.<br />";
	else
		echo "Package #{$_POST["Idx"]} failed to save.<br />";
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;
    case "pickup":
	if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )))
	{
	  errorMessage("Please specify a valid numeric index.", 6);
	  break;
	}
	if ( !(packageExists($_POST["Idx"],$con)))
	{
	  errorMessage("Specified package number does not exist.<br />", 6);
	  break;
	}
	$curtime = getdate();
	$sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
	$querystring = "UPDATE Packages SET PickupTime = '{$sqltime}', ReturnedBy = '{$_SESSION['Username']}' WHERE Idx = {$_POST['Idx']}";
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Package #{$_POST["Idx"]} marked as picked up.<br />";
	else
		echo "Package #{$_POST["Idx"]} failed to save.<br />";
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

    default:
	break;
}

if ( $_POST['function'] == 'search' ) {
	echo "<h4 style='text-align:center'>Search Results:</h4>";
}

if (( $_POST['function'] == 'list' ) || ( $_POST['function'] == 'search' )) {
	if ( $_POST['function'] == 'list' ) {
	    $_POST['AwaitingPickup'] = 'Yes';
	}
	echo "<table class='result sortable tablesorter' border=4>";
	$querystring = "SELECT * FROM Packages WHERE 1=1";
	if ( $_POST['Idx'] != "" ) {
		$querystring .= " AND Idx = ";
		$querystring .= intval($_POST['Idx']);
	}
	if ( $_POST['Unit'] != "" ) {
		$querystring .= " AND Unit = ";
		$querystring .= intval($_POST['Unit']);
	}
	if (( $_POST['StartMonth'] != "MM" ) && ( $_POST['StartDay'] != "DD" ) && ( $_POST['StartYear'] != "YY" ) &&
		( $_POST['StartMonth'] != "" ) && ( $_POST['StartDay'] != "" ) && ( $_POST['StartYear'] != "" )) {
		$querystring .= " AND ReceiveTime >= '";
		$querystring .= assembleDate( $_POST['StartMonth'], $_POST['StartDay'], $_POST['StartYear'] );
		$querystring .= "'";
	}
	if (( $_POST['EndMonth'] != "MM" ) && ( $_POST['EndDay'] != "DD" ) && ( $_POST['EndYear'] != "YY" ) &&
		( $_POST['EndMonth'] != "" ) && ( $_POST['EndDay'] != "" ) && ( $_POST['EndYear'] != "" )) {
		$querystring .= " AND ReceiveTime <= '";
		$querystring .= assembleDate( $_POST['EndMonth'], $_POST['EndDay'], $_POST['EndYear'] );
		$querystring .= "'";
	}
	if ( isset($_POST['AwaitingPickup']) && ($_POST['AwaitingPickup'] == 'Yes' ) ) {
		$querystring .= " AND PickupTime = 0";
	}
	if ( $_POST['Recipient'] != "" ) {
		$lRecipient = strtolower($_POST['Recipient']);
		$querystring .= " AND LOWER(Recipient) LIKE '%{$lRecipient}%'";
	}
	if (( $_POST['Type'] != "None" ) && ( $_POST['Type'] != '' )) {
		$querystring .= " AND Type = '";
		$querystring .= $_POST['Type'];
		$querystring .= "'";
	}
	if ( $_POST['ReceivedBy'] != "" ) {
		$lReceivedBy = strtolower($_POST['ReceivedBy']);
		$querystring .= " AND LOWER(ReceivedBy) LIKE '%{$lReceivedBy}%'";
	}
	if ( $_POST['ReturnedBy'] != "" ) {
		$lReturnedBy = strtolower($_POST['ReturnedBy']);
		$querystring .= " AND LOWER(ReturnedBy) LIKE '%{$lReturnedBy}%'";
	}
	debugText("Original Query:" . $querystring);
	if ( $useSavedQuery == 'yes' ) {
	    $querystring = stripslashes($_POST['SavedQuery']);
	    debugText("Using Saved Query:" . $querystring);
	}
	echo "<script>document.forms['recordinput'].elements['SavedQuery'].value = \"{$querystring}\";</script>";
	$result = mysql_query($querystring, $con); 
	$k=0;
	$results=0;
	echo "<thead><tr><th>Package#</th><th>Unit</th><th>For</th><th>Received Time</th><th>Received By</th><th>Pick-up Time</th><th>Returned By</th><th>Carrier</th>";
	if ( $_SESSION['Level'] >= $editlevel )
	    echo "<th>Delete</th><th>Mark Picked-up</th>";
	else
	    echo "<th></th><th></th>";
	echo "</tr></thead><tbody>";
	while ( $row = mysql_fetch_array($result) )
	{
	  	echo "<tr>";
                $pic = $gazebo_imagedir . carrierPic($row['Type']);
                if ( $_SESSION['Level'] >= $editlevel )
                    $selectOpt = 4;
                else
                    $selectOpt = 2;
                $picture = $imagedir . $row['Pic'];
                echo "<td>";
                echo sprintf("<a href=\"#top\" onclick=\"fillInForm(%u, '%u', '%s', '%s', '%s', '%s', '%s', '%s')\" >#%u</a>", 
                    $selectOpt, $row['Idx'], $row['Unit'], mysql_real_escape_string($row['Recipient']), 
                    mysql_real_escape_string($row['Description']), $row['Type'], $row['ReceivedBy'], $row['ReturnedBy'], $row['Idx'] );
                echo "</td><td>";
                echo $row['Unit'];
                echo "</td><td>";
                echo $row['Recipient'];
                echo "</td><td>";
	    	$submitTime = parseTime($row['ReceiveTime']);
	    	echo displayDate($submitTime['Month'], $submitTime['Day'], $submitTime['Year']);
	    	echo " ";
	    	echo displayTime($submitTime['Hour'], $submitTime['Minute']); 
                echo "</td><td>";
                echo $row['ReceivedBy'];
                echo "</td>";
                if ( $row['PickupTime'] == 0 )
                    echo "<td colspan='2'>Awaiting pick-up";
                else
                {
                    echo "<td>";
	    	    $pickupTime = parseTime($row['PickupTime']);
	    	    echo displayDate($pickupTime['Month'], $pickupTime['Day'], $pickupTime['Year']);
	    	    echo " ";
	    	    echo displayTime($pickupTime['Hour'], $pickupTime['Minute']); 
                    echo "</td><td>";
                    echo $row['ReturnedBy'];
                }
                echo "</td><td>";
                echo "<img src='{$pic}' height='50' width='50' />";
                echo "</td><td>";
		if ( $_SESSION['Level'] >= $editlevel )
		    echo sprintf("<a href=\"#top\" onclick=\"fillInForm(%u, '%u')\" >X</a>", 5, $row['Idx']);
		echo "</td><td>";
		if ( $_SESSION['Level'] >= $editlevel )
		    echo sprintf("<a href=\"#top\" onclick=\"fillInForm(%u, '%u')\" >X</a>", 6, $row['Idx']);
                echo "</td></tr>";
                $results++;
	}
	echo "</tbody></table>";
	if ( $results == 0 )
	  echo "<br /><br /><i>No records found.</i><br />";
	if ( $results == 1 )
	  echo "<br /><br /><i>One record found.</i><br />";
	if ( $results > 1 )
	  echo "<br /><br /><i>{$results} records found.</i><br />";
	if ( isset( $_POST['Idx'] ) )
	    echo "<script>document.forms['recordinput'].elements['Idx'].value = '{$_POST['Idx']}';</script>";
	if ( isset( $_POST['Unit'] ) )
	    echo "<script>document.forms['recordinput'].elements['Unit'].value = '{$_POST['Unit']}';</script>";
	if ( isset($_POST['StartMonth']) )
	    echo "<script>document.forms['recordinput'].elements['StartMonth'].value = '{$_POST['StartMonth']}';</script>";
	if ( isset($_POST['StartDay']) )
	    echo "<script>document.forms['recordinput'].elements['StartDay'].value = '{$_POST['StartDay']}';</script>";
	if ( isset($_POST['StartYear']) )
	    echo "<script>document.forms['recordinput'].elements['StartYear'].value = '{$_POST['StartYear']}';</script>";
	if ( isset($_POST['EndMonth']) )
	    echo "<script>document.forms['recordinput'].elements['EndMonth'].value = '{$_POST['EndMonth']}';</script>";
	if ( isset($_POST['EndDay']) )
	    echo "<script>document.forms['recordinput'].elements['EndDay'].value = '{$_POST['EndDay']}';</script>";
	if ( isset($_POST['EndYear']) )
	    echo "<script>document.forms['recordinput'].elements['EndYear'].value = '{$_POST['EndYear']}';</script>";
	if ( !isset( $_POST['AwaitingPickup'] ) )
	    echo "<script>document.forms['recordinput'].elements['AwaitingPickup'].checked = false;</script>";
	if ( isset( $_POST['Recipient'] ) )
	    echo "<script>document.forms['recordinput'].elements['Recipient'].value = '{$_POST['Recipient']}';</script>";
	if ( isset( $_POST['Type'] ) )
	    echo "<script>document.forms['recordinput'].elements['Type'].value = '{$_POST['Type']}';</script>";
	if ( isset( $_POST['ReceivedBy'] ) )
	    echo "<script>document.forms['recordinput'].elements['ReceivedBy'].value = '{$_POST['ReceivedBy']}';</script>";
	if ( isset( $_POST['ReturnedBy'] ) )
	    echo "<script>document.forms['recordinput'].elements['ReturnedBy'].value = '{$_POST['ReturnedBy']}';</script>";
}

include 'gazebo-footer.php';
?>
