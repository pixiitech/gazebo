<?php 
$pagename = "violations";
require 'gazebo-header.php'; 
?>

<script>
function fillInForm(fn, Idx, Unit, Tag, Type, Name, ReportedBy, Description, Actions, Pic)
{
    var a = [['Idx', Idx], ['Unit', Unit], ['Tag', Tag], ['Type', Type], ['Name', Name],
	 ['ReportedBy', ReportedBy], ['Description', Description], ['Actions', Actions], ['Pic', Pic]];
    var updateFields = function (FieldArray) {
	for ( var i = 0; i < FieldArray.length; i++ )
	    if (FieldArray[i][0] == 'Type')
		document.forms['recordinput'].elements[FieldArray[i][0]].selectedIndex = FieldArray[i][1];
	    else if (FieldArray[i][0] == 'Pic')
	    {
		document.getElementById("violationPic").src = FieldArray[i][1];
		document.getElementById("violationPic").height = "80";
		document.getElementById("violationPic").width = "120";
		document.getElementById("violationLink").href = FieldArray[i][1];
		document.getElementById("violationLink").target = "_blank";
	    }
	    else
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
    }
    return;
}
</script>
<?php include 'menu.php'; ?>
<?php

require 'authcheck.php';

$pkviolation_expiration = fetchSetting("ViolationExpiration", $con);

$querystring = "SELECT * FROM ViolationTypes";
$result = mysql_query($querystring, $con);
$ViolationTypes = array();
echo "<script>var ViolationTypes = new Array();";
while ( $row = mysql_fetch_array($result) )
{
    $i = $row['Idx'];
    $ViolationTypes[$i] = $row['Name'];
    echo "ViolationTypes[{$i}] = '{$row['Name']}';";
}
echo "</script>";
?>

<h2 style="text-align:center">Violation Management</h2>

<?php

function errorMessage($msg, $fn)
{
    $typeVal = 0;
    if ( $_POST['Type'] == "Resident" )
	$typeVal = 1;
    if ( $_POST['Type'] == "Guest" )
	$typeVal = 2;
    echo sprintf("<script>fillInForm(%s, '%u', '%u', '%s', '%u', '%s', '%s', '%s', '');</script>", $fn, $_POST['Idx'],
	 $_POST['Unit'], $_POST['Tag'], $typeVal, $_POST['Name'], $_POST['ReportedBy'], $_POST['Description']);
    echo("$msg" . "<br />");
}

function uploadPic()
{
	require "config.php";
	//Save Picture
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$extension = end(explode(".", $_FILES["file"]["name"]));
	if ($_FILES['Pic']['type'] != "")
	{
		if ((($_FILES["Pic"]["type"] == "image/gif")
		|| ($_FILES["Pic"]["type"] == "image/jpeg")
		|| ($_FILES["Pic"]["type"] == "image/png")
		|| ($_FILES["Pic"]["type"] == "image/pjpeg")))
  		{
  			if ($_FILES["Pic"]["error"] > 0)
    			{
    			   errorMessage( "Picture Upload: error code " . $_FILES["Pic"]["error"] . "<br>", 3);
			   break;
			}
    			else
    			{
    		    		echo "Upload: " . $_FILES["Pic"]["name"] . "<br>";
    		    		echo "Type: " . $_FILES["Pic"]["type"] . "<br>";
    		    		echo "Size: " . ($_FILES["Pic"]["size"] / 1024) . " kB<br>";
    		    		echo "Temp file: " . $_FILES["Pic"]["tmp_name"] . "<br>";
				echo "Destination: " . $violation_imagedir . $_FILES["Pic"]["name"] . "<br />";
    		    		if (file_exists($violation_imagedir . $_FILES["Pic"]["name"]))
      		    		{
      					errorMessage( $_FILES["Pic"]["name"] . " already exists. ", 3);
      		   		}
    		    		else
      		    		{
					$success = move_uploaded_file($_FILES["Pic"]["tmp_name"], $violation_imagedir . $_FILES["Pic"]["name"]);
					if ( $success )
      					    echo "Stored in: " . $violation_imagedir . $_FILES["Pic"]["name"] . "<br />";
					else
					{
    			   		    errorMessage( "move_uploaded_file: error code " . $_FILES["Pic"]["error"] . "<br>", 3);
					    break;
					}
      		    		}
			}
	  	}
		else
		{
  	    		errorMessage( "Invalid picture file. type:{$_FILES['Pic']['type']}  filename: {$_FILES['Pic']['name']}  size: {$_FILES['size']}  ext:{$extension}", 3);
	    		break;
  		}
	}
	else
	    debugText("No picture specified.");
}

echo "<form name='recordinput' method='post' action='" . pageLink("violations") . "' enctype='multipart/form-data' ><p class='center'>
<input type='hidden' name='MAX_FILE_SIZE' value='{$max_upload_size}' />
<input type='hidden' id='SavedQuery' name='SavedQuery' value=\"{$_POST['SavedQuery']}\" />
<input id='fnList' type='radio' name='function' value='list' onClick='fnList(); ' />List&nbsp;&nbsp;
<input id='fnSearch' type='radio' name='function' value='search' checked='true' />Search&nbsp;&nbsp;
<input id='fnInsert' type='radio' name='function' value='insert' />Submit New&nbsp;&nbsp;";
if ($_SESSION['Level'] >= $editlevel)
{
    echo "
<input id='fnUpdate' type='radio' name='function' value='update' />Update&nbsp;&nbsp;
<input id='fnDelete' type='radio' name='function' value='delete' />Delete&nbsp;&nbsp;";
}
else
    echo "<input id='fnView' type='radio' name='function' value='view' />View&nbsp;&nbsp;";

echo "</p><div id='criteria'>";
echo "<table class='criteria'><tbody>";
echo "<tr><td class='formfields Search Update Delete'><span class='formfields Delete'>Are you sure you want to delete Violation# </span><span class='formfields Search Update'>Violation # </span><input id='Idx' type='text' size='5' name='Idx' /><span class='formfields Delete'>?</span></td>";
echo "<td class='formfields Search Insert Update'>Unit # <input id='Unit' type='text' size='7' name='Unit' /></td></tr>";
echo "<tr><td class='formfields Search Insert Update'>Tag # <input id='Tag' type='text' size='10' name='Tag' /></td><td class='formfields  Insert Update'>Picture:  <input id='Pic' type='file' name='Pic' size='18' accept='image/*' /></td></tr>";
echo "<tr class='formfields Search Insert Update'><td>Violation Type: <select id='Type' name='Type'>";
for ( $i = 0; $i < count($ViolationTypes); $i++ )
	echo "<option value='{$i}'>{$ViolationTypes[$i]}</option>";
echo "</select></td>";
echo "<td> Name  <input id='Name' type='text' size='18' name='Name' /></td></tr>";
echo "<tr class='formfields Insert Update'><td>Description:  <textarea id='Description' rows='3' cols='60' style='height:60px; width:400px' name='Description' >Enter report here.</textarea></td>";
echo "<td>Action Log: <textarea id='Actions' rows='3' cols='60' style='height:60px; width:400px' name='Actions' readOnly='true' ></textarea></td></tr>";
echo "<tr class='formfields Search Update'><td colspan='2'>Reported By: <input id='ReportedBy' type='text' size='15' name='ReportedBy' disabled='true' /></td></tr>";
echo "<tr class='formfields Update'><td colspan='2' name='picContainer'><a id='violationLink'><img id='violationPic' src='' alt='' name='violationPic' /></a></td></tr>";
echo "<tr class='formfields Search'><td>Include Expired?<input id='SearchExpired' type='checkbox' name='SearchExpired' /></td>";
echo "<td>Nth Violation:<select id='Ordinal' name='Ordinal'>
	<option value='All'>All</option>
	<option value='1st'>1st</option>
	<option value='2nd'>2nd</option>
	<option value='3rd'>3rd</option>
	<option value='4th'>4th+</option></select></td>";
echo "</tbody></table>";
echo "<p class='center'><input id='submitbutton' type='submit' value='Update' /> <input type='reset' value='Clear' /></p>";

echo "</div></form>";

if ( !isset($_POST["function"]) ) {
    checkRadio('fnList');
    $_POST['function'] = "list";
}

switch ($_POST["function"])
{
  case "insert":
	checkRadio("fnSearch", "true");
	if (( $_POST["Unit"] == "" ) || !(is_numeric( $_POST["Unit"] ) ) || !validateUnit($_POST['Unit'], $con) )
	{
	  errorMessage("Please specify a valid numeric unit number.", 3);
	  break;
	}
	if ( trim($_POST["Tag"]) == "" )
	{
	  errorMessage("Please specify a tag number.", 3);
	  break;
	}
	if ($_POST["Type"] == "None")
	{
	  errorMessage("Please specify a violation type (Resident or Guest).", 3);
	  break;
	}
	if ($_POST["Name"] == "")
	{
	  errorMessage("Please specify a name.", 3);
	  break;
	}

	uploadPic();

	//Save SQL Record
	$curtime = getdate();
	$sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
	$_POST['Description'] = mysql_real_escape_string($_POST['Description']);
	$_POST['Name'] = mysql_real_escape_string($_POST['Name']);
	$_POST['Tag'] = mysql_real_escape_string($_POST['Tag']);
	$_POST['Type'] = intval($_POST['Type']);
	$querystring = "INSERT INTO Violations (Unit, Time, Tag, Type, Name, ReportedBy, Description, Pic) VALUES

			({$_POST['Unit']}, '{$sqltime}', '{$_POST['Tag']}', {$_POST['Type']}, '{$_POST['Name']}', 
			'{$_SESSION['Username']}', '{$_POST['Description']}', '{$_FILES['Pic']['name']}')";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Violation saved.<br />";
	else
	{
		errorMessage("Violation failed to save.<br />", 3);
	 	break;
        }
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

  case "update":
	checkRadio("fnSearch", "true");
	if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )) )
	{
	  errorMessage( "Please specify a valid numeric index.", 4);
	  break;
	}
	if ( trim($_POST["Tag"]) == "" )
	{
	  errorMessage("Please specify a tag number.", 4);
	  break;
	}
	if ($_POST["Type"] == "None")
	{
	  errorMessage("Please specify a violation type (Resident or Guest).", 4);
	  break;
	}
	if ($_POST["Name"] == "")
	{
	  errorMessage("Please specify a name.", 4);
	  break;
	}

	//Save SQL Record
	$curtime = getdate();
	$sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
	$typeVal = 0;
	if ($_POST["Type"] == "Guest")
	    $typeVal = 1;
	$_POST['Description'] = mysql_real_escape_string($_POST['Description']);
	$_POST['Name'] = mysql_real_escape_string($_POST['Name']);
	$_POST['Tag'] = mysql_real_escape_string($_POST['Tag']);
	$_POST['Type'] = intval($_POST['Type']);
	$querystring = "UPDATE Violations SET Unit={$_POST['Unit']}, Tag='{$_POST['Tag']}',
			Type={$_POST['Type']}, Name='{$_POST['Name']}', Description='{$_POST['Description']}' WHERE
			Idx={$_POST['Idx']}";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Violation #{$_POST["Idx"]} updated.<br />";
	else
	{
		errorMessage("Violation #{$_POST["Idx"]} failed to update.<br />", 4);
	  	break;
	}

	if ($_FILES["Pic"]["name"] != "")
	{
		uploadPic();
		$querystring = "UPDATE Violations SET Pic='{$_FILES["Pic"]["name"]}' WHERE Idx={$_POST['Idx']}";
		debugText($querystring);
			$result = mysql_query($querystring, $con);
	}
	if ( $result )
		echo "Violation #{$_POST["Idx"]} picture updated.<br />";
	else
	{
		errorMessage("Violation #{$_POST["Idx"]} picture failed to update.<br />", 4);
	  	break;
	}
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

  case "delete":
	checkRadio("fnSearch", "true");
	if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )))
	{
	  errorMessage("Please specify a valid numeric index.", 5);
	  break;
	}
	if ( !(violationExists($_POST["Idx"],$con)))
	{
	  errorMessage("Specified violation number does not exist.<br />", 5);
	  break;
	}
	$querystring = "DELETE FROM Violations WHERE Idx={$_POST['Idx']}";
	debugText($querystring);
	$result = mysql_query($querystring, $con);
	if ( $result )
		echo "Violation #{$_POST["Idx"]} deleted.<br />";
	else
		echo "Violation #{$_POST["Idx"]} failed to save.<br />";
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;
  default:
	break;
}

if ( $_POST['function'] == 'search' ) {
	echo "<h4 style='text-align:center'>Search Results:</h4>";
}
if (( $_POST['function'] == 'search' ) || ( $_POST['function'] == 'list' )) {

	echo "<table class='result sortable tablesorter' border=4>";
	$querystring = "SELECT * FROM Violations WHERE 1=1";
	if ( $_POST['Idx'] != "" ) {
		$querystring .= " AND Idx = ";
		$querystring .= intval($_POST['Idx']);
	}
	if ( $_POST['Unit'] != "" ) {
		$querystring .= " AND Unit = ";
		$querystring .= intval($_POST['Unit']);
	}
	if ( $_POST['Tag'] != "" ) {
		$lTag = strtolower($_POST['Tag']);
		$querystring .= " AND LOWER(Tag) LIKE '%{$lTag}%'";
	}
	if (( $_POST['StartMonth'] != "MM" ) && ( $_POST['StartDay'] != "DD" ) && ( $_POST['StartYear'] != "YY" ) &&
		( $_POST['StartMonth'] != "" ) && ( $_POST['StartDay'] != "" ) && ( $_POST['StartYear'] != "" )) {
		$querystring .= " AND Time >= '";
		$querystring .= assembleDate( $_POST['StartMonth'], $_POST['StartDay'], $_POST['StartYear'] );
		$querystring .= "'";
	}
	if (( $_POST['EndMonth'] != "MM" ) && ( $_POST['EndDay'] != "DD" ) && ( $_POST['EndYear'] != "YY" ) &&
		( $_POST['EndMonth'] != "" ) && ( $_POST['EndDay'] != "" ) && ( $_POST['EndYear'] != "" )) {
		$querystring .= " AND Time <= '";
		$querystring .= assembleDate( $_POST['EndMonth'], $_POST['EndDay'], $_POST['EndYear'] );
		$querystring .= "'";
	}
	if (( $_POST['Type'] != "None" ) && ( $_POST['Type'] != "" ) && ( $_POST['Type'] != "0" )) {
		$querystring .= " AND Type = ";
		$querystring .= $_POST['Type'];
	}
	if ( $_POST['Name'] != "" ) {
		$lName = strtolower($_POST['Name']);
		$querystring .= " AND LOWER(Name) LIKE '%{$lName}%'";
	}
	if ( $_POST['ReportedBy'] != "" ) {
		$lReportedBy = strtolower($_POST['ReportedBy']);
		$querystring .= " AND LOWER(ReportedBy) LIKE '%{$lReportedBy}%'";
	}
	if ( !isset($_POST['SearchExpired']) ) {
		$querystring .= " AND TIME > DATE_SUB(CURDATE(), INTERVAL {$pkviolation_expiration} DAY)";
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
	$violationTally = array();
	for ( $l = 0; $l < count($ViolationTypes); $l++ )
	    $violationTally[$l] = 0;
	echo "<thead><tr>";
	echo "<th>Index #</th><th>Unit</th><th>Time</th><th>Tag</th><th>Name</th><th>#</th><th>Type</th><th>Reported By</th>";
	if ( $_SESSION['Level'] >= $editlevel )
	    echo "<th>Action</th>";
	echo "</tr></thead><tbody>";
	while ( $row = mysql_fetch_array($result) )
	{
		$ordinal = getOrdinalViolation($row['Idx'], $row['Unit'], $con, $row['Type'] ); 
		$c = false;
		switch ( $_POST['Ordinal'] )
		{
		    case '1st':
			if ( $ordinal != 1 )
			    $c=true;
			break;
		    case '2nd':
			if ( $ordinal != 2 )
			    $c=true;
			break;
		    case '3rd':
			if ( $ordinal != 3 )
			    $c=true;
			break;
		    case '4th':
			if ( $ordinal < 4 )
			    $c=true;
			break;
		    default:
			break;
		}
		if ( $c )
		    continue;
	  	echo "<tr>";
		if ( $_SESSION['Level'] >= $editlevel )
		    $selectOpt = 4;
		else
		    $selectOpt = 6;
		$picture = $violation_imagedir . $row['Pic'];
		$pTime = parseTime($row['Time']);
		global $ViolationTypes;
		echo "<td>";
       	 	echo sprintf("<a href=\"#top\" onclick=\"fillInForm(%u, '%u', '%u', '%s', '%u', '%s', '%s', '%s', '%s', '%s')\" >#%u</a>",
		    $selectOpt, $row['Idx'],
		    $row['Unit'], $row['Tag'], $row['Type'], $row['Name'], 
		    $row['ReportedBy'], mysql_real_escape_string($row['Description']), mysql_real_escape_string($row['ActionLog']), $picture, $row['Idx'] );
		echo "</td><td>";
		echo $row['Unit'];
		echo "</td><td>";
		if ( expired($row['Time'], $con) ) {
		    echo "<span style='color:#FF0000'>";
		}
	    	$submitTime = parseTime($row['Time']);
	    	echo displayDate($submitTime['Month'], $submitTime['Day'], $submitTime['Year']);
	    	echo " ";
	    	echo displayTime($submitTime['Hour'], $submitTime['Minute']); 
		if ( expired($row['Time'], $con) ) {
		    echo " (expired)</span>";
		}
		echo "</td><td>";
		echo $row['Tag'];
		echo "</td><td>";
		echo $row['Name'];
		echo "</td><td>";
		switch ($ordinal)
		{
		    case 0:
			echo "";
			break;
		    case 1:
			echo "1st ";
			break;
		    case 2:
			echo "2nd ";
			break;
		    case 3:
			echo "3rd ";
			break;
		    default:
			echo $ordinal . "th ";
		}
		echo "</td><td>";
		echo $ViolationTypes[$row['Type']];
		echo "</td><td>";
		echo $row['ReportedBy'];
		echo "</td>";
		echo "<td>";
		if ( $_SESSION['Level'] >= $editlevel )
		{
		    $letter = lookupViolationLetter($row['Type'], $row['ActionStatus'] + 1, $con);
		    echo sprintf("<a href=\"#top\" onclick=\"fillInForm(%u, '%u')\" style=\"float: right\"><img src='{$gazebo_imagedir}trashcan.png' alt='Delete' title='Delete'></a>", 5, $row['Idx'] );
		    echo sprintf("<a href=\"formdocs.php?violationidx={$row['Idx']}&unitidx={$row['Unit']}&letter={$letter}\" style=\"float: right\"><img src='{$gazebo_imagedir}envelope.png' alt='Create Letter' 				title='Create Letter'></a>", 5, $row['Idx'] );
		}	
		echo "</td>";
		echo "</tr>";
		$results++;
		if ( !expired( $row['Time'], $con ) )
		    $violationTally[$row['Type']]++;
	}
	echo "</tr></tbody></table>";
	if ( $results == 0 )
	  echo "<br /><br /><i>No records found.</i><br />";
	if ( $results == 1 )
	  echo "<br /><br /><i>One record found.</i><br />";
	if ( $results > 1 )
	  echo "<br /><br /><i>{$results} records found.</i><br />";
	for ( $l = 0; $l < count($ViolationTypes); $l++ )
	{
	    switch ($violationTally[$l])
	    {
		case 0:
		    break;
		case 1:
		    echo "<i>One " . $ViolationTypes[$l] . ".</i><br />";
		    break;
		default:
		    echo "<i>" . $violationTally[$l] . " " . $ViolationTypes[$l] . ".</i><br />";
	    }
	}
	if ( isset($_POST['Idx']) )
	    echo "<script>document.forms['recordinput'].elements['Idx'].value = '{$_POST['Idx']}';</script>";
	if ( isset($_POST['Unit']) )
	    echo "<script>document.forms['recordinput'].elements['Unit'].value = '{$_POST['Unit']}';</script>";
	if ( isset($_POST['Tag']) )
	    echo "<script>document.forms['recordinput'].elements['Tag'].value = '{$_POST['Tag']}';</script>";
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
	if ( isset($_POST['Type']) )
	    echo "<script>document.forms['recordinput'].elements['Type'].value = '{$_POST['Type']}';</script>";
	if ( isset($_POST['Name']) )
	    echo "<script>document.forms['recordinput'].elements['Name'].value = '{$_POST['Name']}';</script>";
	if ( isset($_POST['ReportedBy']) )
	    echo "<script>document.forms['recordinput'].elements['ReportedBy'].value = '{$_POST['ReportedBy']}';</script>";
	if ( isset($_POST['SearchExpired']) )
	    echo "<script>document.forms['recordinput'].elements['SearchExpired'].checked = true;</script>";
}


include 'gazebo-footer.php';
?>
