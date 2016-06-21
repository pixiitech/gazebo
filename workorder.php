<?php $pagename="workorder";
require 'gazebo-header.php'; 
?>

<script>
//jQuery scripts

function fillInForm(fn, Idx, Unit, Summary, Description, AssignedTo, Status, Submitted, Completed, Name, Username)
{
    var a = [['Idx', Idx], ['Unit', Unit], ['Summary', Summary], ['Description', Description], ['AssignedTo', AssignedTo],
	 ['Status', Status], ['Submitted', Submitted], ['Completed', Completed], ['Name', Name],
	 ['Username', Username]];
    var updateFields = function (FieldArray) {
	for ( var i = 0; i < FieldArray.length; i++ )
	    if (FieldArray[i][0] == 'Status')
		document.forms['recordinput'].elements[FieldArray[i][0]].selectedIndex = FieldArray[i][1] + 1;
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
<h2 style="text-align:center">Work Order System</h2>

<?php

function errorMessage($msg, $fn)
{
        echo sprintf("<script>fillInForm(%u, '%u', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');</script>", 
	    $fn, $_POST['Idx'], $_POST['Unit'], $_POST['Summary'], $_POST['Description'],
	    $_POST['AssignedTo'], $_POST['Status'],
	    $_POST['Submitted'], $_POST['Completed'], $_POST['Name'], $_POST['Username'] );
    echo("$msg" . "<br />");
}

require 'authcheck.php';

echo "<form name='recordinput' id='recordinput' method='post' action='" . pageLink("workorder") . "' enctype='multipart/form-data' ><p class='center'>
<input type='hidden' name='MAX_FILE_SIZE' value='{$max_upload_size}' />
<input id='fnList' type='radio' name='function' value='list' onclick=\"document.forms['recordinput'].reset(); this.checked=true;\" />List&nbsp;&nbsp;
<input id='fnSearch' type='radio' name='function' value='search' />Search&nbsp;&nbsp;
<input id='fnInsert' type='radio' name='function' value='insert' onclick=\"document.forms['recordinput'].reset(); this.checked=true; \" />Submit New&nbsp;&nbsp;
<input id='fnUpdate' type='radio' name='function' value='update' />Update&nbsp;&nbsp;
<input id='fnDelete' type='radio' name='function' value='delete' />Delete&nbsp;&nbsp;";

echo "</p><table class='criteria' hidden='hidden' ><tbody><tr>

<td class='formfields Search Update Delete'><span class='formfields Delete' hidden='hidden'>Are you sure you want to delete </span>Work Order # <input id=\"Idx\" type=\"text\" size=\"5\" name=\"Idx\" /><span class='formfields Delete' hidden='hidden'>&nbsp;?</span></td>
<td class='formfields Search Insert Update'>Name <input id='Name' type='text' size='20' name='Name' /></td>
<td class='formfields Search Update'>Username <input id='Username' type='text' size='20' name='Username' /></td></tr>
<tr class='formfields Search Insert Update'><td>Unit # <input id=\"Unit\" type=\"text\" size=\"7\" name=\"Unit\" /></td>
<td colspan='2'>Assigned to <input id='AssignedTo' type='text' size='20' name='AssignedTo' /></td></tr>
<tr><td class='formfields Search Insert Update'>Status <select id='Status' name='Status'>
		<option class='formfields Search' value='Active'>Active</option>
		<option value='{$status_submitted}'>Submitted</option>
		<option value='{$status_approved}'>In Process</option>
		<option value='{$status_denied}'>Denied</option>
		<option value='{$status_completed}'>Completed</option></select></td>
<td class='formfields Update' >Submitted on: <input id='Submitted' type='text' size='25' name='Submitted' /></td>
<td class='formfields Update' >Completed on: <input id='Completed' type='text' size='25' name='Completed' /></td></tr>
<tr class='formfields Search' id='dateSearch'><td colspan='3'>Search: <input id='searchSubmitted' name='searchType' type='radio' value='Submitted'>Submitted on 
			<input id='searchCompleted' name='searchType' type='radio' value='Completed'>Completed on 
dates from:
<input id=\"StartMonth\" type=\"text\" size=\"2\" name=\"StartMonth\" value=\"MM\" onClick=\"if (this.value=='MM') this.value='';\" />/
<input id=\"StartDay\" type=\"text\" size=\"2\" name=\"StartDay\" value=\"DD\" onClick=\"if (this.value=='DD') this.value='';\" />/
<input id=\"StartYear\" type=\"text\" size=\"2\" name=\"StartYear\" value=\"YY\" onClick=\"if (this.value=='YY') this.value='';\" />&nbsp to:
<input id=\"EndMonth\" type=\"text\" size=\"2\" name=\"EndMonth\" value=\"MM\" onClick=\"if (this.value=='MM') this.value='';\" />/
<input id=\"EndDay\" type=\"text\" size=\"2\" name=\"EndDay\" value=\"DD\" onClick=\"if (this.value=='DD') this.value='';\" />/
<input id=\"EndYear\" type=\"text\" size=\"2\" name=\"EndYear\" value=\"YY\" onClick=\"if (this.value=='YY') this.value='';\" />&nbsp 
</td></tr>
<tr class='formfields Search Insert Update'><td colspan='3'>Summary: <input id='Summary' type='text' size='60' name='Summary' /></td></tr>
<tr class='formfields Search Insert Update'><td colspan='3'>Description: <textarea id='Description' rows='3' cols='60' style='height:60px; width:400px' name='Description' /></textarea></td></tr>

</tbody></table>
<p class='center'><input type=\"submit\" value=\"Search\" id='submitbutton' /> <input type=\"reset\" value=\"Clear\" /></p>
</form>";

if ( !isset($_POST["function"]) )
{
    checkRadio('list');
    $_POST['function'] = "list";
    $_POST['Status'] = "Active";
    $_POST['Idx'] = '';
    $_POST['StartMonth'] = 'MM';
    $_POST['EndMonth'] = 'MM';
}
switch ($_POST["function"])
{
  case "search":
	echo "<h4 style='text-align:center'>Search Results:</h4>";
  case "list":
	echo "<table class='result sortable tablesorter' border=4>";
	$querystring = "SELECT * FROM WorkOrders WHERE 1=1";
	if ( $_POST['Idx'] != "" ) {
		$querystring .= " AND Idx = ";
		$querystring .= intval($_POST['Idx']);
	}
	if ( $_POST['Unit'] != "" ) {
		$querystring .= " AND Unit = ";
		$querystring .= intval($_POST['Unit']);

	}
	if (( $_POST['StartMonth'] != "MM" ) && ( $_POST['StartDay'] != "DD" ) && ( $_POST['StartYear'] != "YY" )) {
		$querystring .= " AND" . $_POST['searchType'] . " >= '";
		$querystring .= assembleDate( $_POST['StartMonth'], $_POST['StartDay'], $_POST['StartYear'] );
		$querystring .= "'";

	}
	if (( $_POST['EndMonth'] != "MM" ) && ( $_POST['EndDay'] != "DD" ) && ( $_POST['EndYear'] != "YY" )) {
		$querystring .= " AND" . $_POST['searchType'] . " <= '";
		$querystring .= assembleDate( $_POST['EndMonth'], $_POST['EndDay'], $_POST['EndYear'] );
		$querystring .= "'";

	}
	if ( $_POST['Status'] != "" ) {
		$querystring .= " AND";
		if ( $_POST['Status'] == 'Active' )
		    $querystring .= " (Status = " . $status_submitted . " OR Status = " . $status_approved . ")";
		else
		    $querystring .= " Status =  " . $_POST['Status'];
	}
	if ( $_POST['Summary'] != "" ) {
		$lSummary = strtolower($_POST['Summary']);
		$querystring .= " AND LOWER(Summary) LIKE '%{$lSummary}%'";
	}
	if ( $_POST['Description'] != "" ) {
		$lDescription = strtolower($_POST['Description']);
		$querystring .= " AND LOWER(Description) LIKE '%{$lDescription}%'";
	}
	if ( $_POST['AssignedTo'] != "" ) {
		$lAssignedTo = strtolower($_POST['AssignedTo']);
		$querystring .= " AND LOWER(AssignedTo) LIKE '%{$lAssignedTo}%'";
	}
	if ( $_POST['Name'] != "" ) {
		$lName = strtolower($_POST['Name']);
		$querystring .= " AND LOWER(Name) LIKE '%{$lName}%'";
	}
	if ( $_POST['Username'] != "" ) {
		$lUsername = strtolower($_POST['Username']);
		$querystring .= " AND LOWER(Username) LIKE '%{$lUsername}%'";
	}
	$querystring .= " ORDER BY Status";

	debugText($querystring);
	$result = mysqli_query($con, $querystring); 
	$k=0;
	$results=0;
	echo "<thead><tr><th>WO#</th><th>Status</th><th>Unit</th><th>Summary</th><th>Name</th><th>Submitted</th><th>Assigned To</th><th>Delete</th></tr></thead><tbody>";
	while ( $row = mysqli_fetch_array($result) )
	{
            echo "<tr>";
            if ( $_SESSION['Level'] >= $editlevel )
	        $selectOpt = 4;
	    else
	        $selectOpt = 2;
            echo "<td>";
            echo $row['Idx'];
            echo "</td><td>";
            echo statusText($row['Status']);
            echo "</td><td>";
	    echo $row['Unit'];
	    echo "</td><td>";
            echo sprintf("<a href=\"#top\" onclick=\"fillInForm(%u, '%u', '%s', '%s', '%s', '%s', %s, '%s', '%s', '%s', '%s')\" >%s</a>", 
	        $selectOpt, $row['Idx'], $row['Unit'], mysqli_real_escape_string($con,$row['Summary']),
	        mysqli_real_escape_string($con,$row['Description']), mysqli_real_escape_string($con,$row['AssignedTo']),
	        $row['Status'], mysqli_real_escape_string($con,$row['Submitted']), mysqli_real_escape_string($con,$row['Completed']),
	        mysqli_real_escape_string($con,$row['Name']), mysqli_real_escape_string($con,$row['Username']), mysqli_real_escape_string($con,$row['Summary']) );
	    echo "</td><td>";
	    echo $row['Name'];
	    echo "</td><td>";
	    $submitTime = parseTime($row['Submitted']);
	    echo displayDate($submitTime['Month'], $submitTime['Day'], $submitTime['Year']);
	    echo " ";
	    echo displayTime($submitTime['Hour'], $submitTime['Minute']); 
	    echo "</td><td>";
	    echo $row['AssignedTo'];
	    echo "</td><td>";
	    echo "<a href='#top' onclick=\"fillInForm(5, {$row['Idx']})\">X</a>";
	    echo "</td>";
	    echo "</tr>";
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
	if ( isset( $_POST['Summary'] ) )
	    echo "<script>document.forms['recordinput'].elements['Summary'].value = '{$_POST['Summary']}';</script>";
	if ( isset( $_POST['Description'] ) )
	    echo "<script>document.forms['recordinput'].elements['Description'].value = '{$_POST['Description']}';</script>";
	if ( isset( $_POST['AssignedTo'] ) )
	    echo "<script>document.forms['recordinput'].elements['AssignedTo'].value = '{$_POST['AssignedTo']}';</script>";
	if ( isset( $_POST['Status'] ) )
	    echo "<script>document.forms['recordinput'].elements['Status'].value = '{$_POST['Status']}';</script>";
	if ( isset( $_POST['Name'] ) )
	    echo "<script>document.forms['recordinput'].elements['Name'].value = '{$_POST['Name']}';</script>";
	if ( isset( $_POST['Username'] ) )
	    echo "<script>document.forms['recordinput'].elements['Username'].value = '{$_POST['Username']}';</script>";
  	break;
  case "insert":
	checkRadio("fnSearch", "true");
	echo "<script>fnSearch();</script>";
	//Save SQL Record
	$curtime = getdate();
	$sqltime = $curtime['year'] . "-" . $curtime['mon'] . "-" . $curtime['mday'] . " " . 
		$curtime['hours'] . ":" . $curtime['minutes'] . ":" . $curtime['seconds'];
	$_POST['Description'] = mysqli_real_escape_string($con,$_POST['Description']);
	$_POST['Summary'] = mysqli_real_escape_string($con,$_POST['Summary']);
	$querystring = "INSERT INTO WorkOrders (Unit, Summary, Description, AssignedTo, Status, Submitted, Name, Username) VALUES
			('{$_POST['Unit']}', '{$_POST['Summary']}', '{$_POST['Description']}', '{$_POST['AssignedTo']}', 
			 '{$_POST['Status']}', '{$sqltime}', '{$_POST['Name']}', '{$_SESSION["Username"]}')";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result ) {
		echo "Work Order Entered.<br />";
	}
	else {
		errorMessage("Work Order failed to save.<br />", 3);
	 	break;
        }
	break;

  case "update":
	checkRadio("fnSearch", "true");
	echo "<script>fnSearch();</script>";
	if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )) )
	{
	  errorMessage( "Please specify a valid numeric work order number.", 4);
	  break;
	}

	//Save SQL Record
	$curtime = getdate();
	$sqltime = assembleDateTime($curtime['mon'],$curtime['mday'],substr($curtime['year'],2),$curtime['hours'],$curtime['minutes']);

	$querystring = "SELECT Completed FROM WorkOrders WHERE Idx={$_POST['Idx']} AND Completed = 0";
	$result = mysqli_query($con, $querystring);
	if ( $result && ( $_POST['Status'] == $status_completed ) )
	    $_POST['Completed'] = $sqltime;

	$querystring = "UPDATE WorkOrders SET Unit='{$_POST['Unit']}', Summary='{$_POST['Summary']}', Description='{$_POST['Description']}', AssignedTo='{$_POST['AssignedTo']}', Status='{$_POST['Status']}', Submitted='{$_POST['Submitted']}', Completed='{$_POST['Completed']}',
Name='{$_POST['Name']}', Username='{$_POST['Username']}' WHERE Idx= {$_POST['Idx']}";

	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Work Order Updated.<br />";
	else
	{
		errorMessage("Work Order failed to update.<br />", 3);
	 	break;
        }
	break;

  case "delete":
	checkRadio("fnSearch", "true");
	echo "<script>fnSearch();</script>";
	if (( $_POST["Idx"] == "" ) || !(is_numeric( $_POST["Idx"] )))
	{
	  errorMessage("Please specify a valid numeric index.", 5);
	  break;
	}
	if ( !(workorderExists($_POST["Idx"],$con)))
	{
	  errorMessage("Specified work order number does not exist.<br />", 5);
	  break;
	}
	$querystring = "DELETE FROM WorkOrders WHERE Idx={$_POST['Idx']}";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Work Order #{$_POST["Idx"]} deleted.<br />";
	else
		echo "Work Order #{$_POST["Idx"]} failed to delete.<br />";
	break;

  default:
	break;
}

include 'gazebo-footer.php';
?>
