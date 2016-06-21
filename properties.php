<?php
$pagename = "properties";
require 'gazebo-header.php';
?>

<script>
//jQuery scripts

function fillInForm(fn, Unit, Subdivision, Address, Resname, Residx, Tenantname, Tenantidx)
{
    var a = [['Unit', Unit], ['Subdivision', Subdivision], ['Address', Address], ['Resname', Resname],
	 ['Residx', Residx], ['Tenantname', Tenantname], ['Tenantidx', Tenantidx]];
    var updateFields = function (FieldArray) {
	for ( var i = 0; i < FieldArray.length; i++ )
	    if (FieldArray[i][0] == 'Subdivision')
 	        document.forms['recordinput'].elements['Subdivision'].selectedIndex = FieldArray[i][1];
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
$querystring = "SELECT * FROM Subdivisions ORDER BY Id";
$result = mysqli_query($con, $querystring);
$SubdivTypes = array();
while ( $row = mysqli_fetch_array($result) )
    array_push($SubdivTypes, array($row['Id'], $row['Name']));
foreach(["SavedQuery", "Resname", "Tenantname", "Unit"] as $key) {
	if (!isset($_POST[$key])) {
		$_POST[$key] = "";
	}
}
?>
<h2 style="text-align:center">Properties Management</h2>

<?php

function outputSearchResult($row,$con)
{
    include 'config.php';
    $unit = $row['Unit'];
    if ( isset( $_POST['ViolationReport'] ) )
    {
        $violations = 0;
	$querystring = "SELECT Type, Time FROM Violations WHERE Unit = '{$unit}'";
	$result = mysqli_query($con, $querystring);
	while ( $vrow = mysqli_fetch_array($result) )
	{
	    if ( expired( $vrow['Time'], $con ) )
	        continue;
	    $violations++;

        }
    }
    echo "<td>";
    echo sprintf("<a href='#top' onclick=\"fillInForm(%u, '%s', '%s', '%s', '%s', '%s', '%s', '%s')\" >#%s</a> ", 4, $row['Unit'],
		 $row['Subdivision'], $row['Address'], fetchResname($row['Residx'], $con), $row['Residx'],
		 fetchResname($row['Tenantidx'], $con), $row['Tenantidx'], $row['Unit']);
    echo "</td>";
    if ( fetchSetting( "Type", $con ) == 'HOA' ) {
	echo "<td>" . fetchSubdivision($row['Subdivision'], $con) . "</td>";
    }
    echo "<td>";
    echo $row['Address'];
    echo "</td><td>";
    $resname = fetchResname($row['Residx'], $con);
    $tenantname = fetchResname($row['Tenantidx'], $con);
    if ( $resname != false )
        echo "<a href='" . pageLink("residents", "Idx={$row['Residx']}") . "'>{$resname}</a>";
    else
	echo "None";
    echo "</td><td>";
    if ( $tenantname != false )
        echo "<a href='" . pageLink("residents", "Idx={$row['Tenantidx']}") . "'>{$tenantname}</a>";
    echo "</td>";
    if ( isset( $_POST['ViolationReport'] ) )
    {
	echo "<td>" . $violations . "</td>";
    }
    if ( $_SESSION['Level'] >= $editlevel )
    {
	echo "<td>";
        echo sprintf("<a href='#top' onclick=\"fillInForm(%u, '%s', '%s', '%s', '%s', '%s', '%s', '%s')\" >X</a> ", 5, $row['Unit'],
		 $row['Subdivision'], $row['Address'], fetchResname($row['Residx'], $con), $row['Residx'],
		 fetchResname($row['Tenantidx'], $con), $row['Tenantidx']);
	echo "</td>";
    }
    return $violations;
}

function errorMessage($fn, $msg, $con)
{
    echo $msg . "<br />";
    echo "<script>fillInForm({$fn}, '" . $_POST['Unit'] . "', '" . $_POST['Subdivision'] . "', '" . $_POST['Address'] . "', '";
    echo fetchResname($_POST['Residx'], $con) . "', '" . $_POST['Residx'] . "', '" . fetchResname($_POST['Tenantidx'], $con) . "', '" . $_POST['Tenantidx'] . "');</script>";
}

require 'authcheck.php';

echo "<form name='recordinput' method='post' action='" . pageLink("properties") . "'>
<p class='center'>
<input type='hidden' id='SavedQuery' name='SavedQuery' value=\"{$_POST['SavedQuery']}\" />
<input id='fnList' type='radio' name='function' value='list' />List&nbsp;&nbsp;
<input id='fnSearch' type='radio' name='function' value='search' />Search&nbsp;&nbsp;";
if ($_SESSION['Level'] >= $editlevel)
{
    echo "
<input id='fnInsert' type='radio' name='function' value='insert' />Insert&nbsp;&nbsp;
<input id='fnUpdate' type='radio' name='function' value='update' />Update&nbsp;&nbsp;
<input id='fnDelete' type='radio' name='function' value='delete' />Delete&nbsp;&nbsp;";
}
echo "</p>

<table class='criteria'><tbody>
<tr>

<td class='formfields Search Insert Update Delete'>
<span class='formfields Delete'>Are you sure you want to delete </span>
Unit# <input id='Unit' type='text' size='7' name='Unit' />
<span class='formfields Delete'>?</span>
</td>
<td class='formfields Search Insert Update'>";
if ( fetchSetting("Type", $con) != 'HOA' ) {
    echo "<span hidden='hidden'>";
}
echo "Subdivision <select name='Subdivision'>
<option class='formfields Search' value='-1'>Select...</option>";
for ( $i = 0; $i < count($SubdivTypes); $i++ )
	echo "<option value='{$SubdivTypes[$i][0]}'>{$SubdivTypes[$i][1]}</option>";
echo "</select>";
if ( fetchSetting("Type", $con) != 'HOA' ) {
    echo "</span>";
}
echo "</td></tr>
<tr class='formfields Search Insert Update'>
<td colspan='2'>Address:  <input id='Address' type='text' size='30' name='Address' /><br /></td></tr>
<tr class='formfields Search Insert Update'><td>Owner:  <input id='Resname' type='text' size='30' name='Resname' />
<input id='Residx' type='hidden' name='Residx' />
<span class='formfields Insert Update'>
<button type='button' name='loadOwnerSelect' onclick=\"window.open('";
echo pageLink('resname', 'fn=0&target1=Residx&target2=Resname');
echo "', 'selectResname','width=350, height=600, status=yes'); return false;\">Select...</button>
<button type='button' onclick=\"document.forms['recordinput'].elements['Residx'].value=''; document.forms['recordinput'].elements['Resname'].value=''; return false;\">Clear</button></span>";

echo "</td>
<td>Tenant:  <input id='Tenantname' type='text' size='30' name='Tenantname' />
<input id='Tenantidx' type='hidden' name='Tenantidx' />
<span class='formfields Insert Update'>
<button type='button' name='loadTenantSelect' onclick=\"window.open('";
echo pageLink('resname', 'fn=1&target1=Tenantidx&target2=Tenantname');
echo "','selectResname','width=350, height=600, status=yes'); return false;\">Select...</button>
<button type='button' onclick=\"document.forms['recordinput'].elements['Tenantidx'].value=''; document.forms['recordinput'].elements['Tenantname'].value=''; return false;\">Clear</button></span></td></tr>";
if ( $module_violations == 1 )
    echo "<tr class='formfields Search' colspan='2'><td><input type='checkbox' name='ViolationReport' />Show Violation Info</td></tr>";
echo "</tbody></table>
<p class='center'><input type='submit' value='Update' id='submitbutton' /> <input type='reset' value='Clear' />
</form>";

if ( isset($_GET["Unit"]) )
{
    checkRadio('fnSearch');
    $_POST['function'] = "search";
    $_POST['Unit'] = $_GET['Unit'];
}

if ( !isset($_POST["function"]) ) {
    checkRadio('fnList');
    $_POST['function'] = "list";
}

if ( !isset($_POST['Subdivision'])) {
    $_POST['Subdivision'] = -1;
}

switch ($_POST["function"])
{
    case "insert":
	checkRadio("fnInsert", "true");
	if ( $_POST["Unit"] == "" )
	{
	  errorMessage(3, "Please specify a unit number.", $con);
	  break;
	}
	if ( !isset($_POST['Residx']) || ($_POST['Residx'] == 0 ))
	    $_POST['Residx'] = 0;
	else if ( fetchResname( $_POST['Residx'], $con ) == false )
	{
	    errorMessage(3, "Resident index #{$_POST['Residx']} not valid.", $con);
	    break;
	}

	if ( !isset($_POST['Tenantidx']) || ($_POST['Tenantidx'] == 0 ))
	    $_POST['Tenantidx'] = 0;
	else if ( fetchResname( $_POST['Tenantidx'], $con ) == false )
	{
	    errorMessage(3, "Tenant index #{$_POST['Tenantidx']} not valid.", $con);
	    break;
	}

	$querystring = "INSERT INTO Properties (Unit, Subdivision, Address, Residx, Tenantidx) VALUES
			('{$_POST['Unit']}', {$_POST['Subdivision']}, '{$_POST['Address']}', {$_POST['Residx']}, {$_POST['Tenantidx']})";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Property #{$_POST["Unit"]} saved.<br />";
	else
		echo "Property #{$_POST["Unit"]} failed to save.<br />";
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;

    case "update":
	checkRadio("fnUpdate", "true");
	if ( $_POST["Unit"] == "" )
	{
	  errorMessage(4, "Please specify a unit number.", $con);
	  break;
	}
	if ( !(validateUnit($_POST["Unit"],$con)))
	{
	  errorMessage(4, "Specified unit number does not exist.", $con);
	  break;
	}
	if ( !isset($_POST['Residx']) || ($_POST['Residx'] == 0 ))
	    $_POST['Residx'] = 0;
	else if ( fetchResname( $_POST['Residx'], $con ) === false )
	{
	    errorMessage(4, "Resident index #{$_POST['Residx']} not valid.", $con);
	    break;
	}

	if ( !isset($_POST['Tenantidx']) || ($_POST['Tenantidx'] == 0 ))
	    $_POST['Tenantidx'] = 0;

	else if ( fetchResname( $_POST['Tenantidx'], $con ) === false )
	{
	    errorMessage(4, "Tenant index #{$_POST['Tenantidx']} not valid.", $con);
	    break;
	}

	$querystring = "UPDATE Properties SET Subdivision={$_POST['Subdivision']}, Address='{$_POST['Address']}', ";
        $querystring .= "Residx={$_POST['Residx']}, Tenantidx={$_POST['Tenantidx']} WHERE Unit='{$_POST['Unit']}'";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Property #{$_POST["Unit"]} updated.<br />";
	else
		echo "Property #{$_POST["Unit"]} failed to save.<br />";
	$_POST['function'] = 'search';
	$useSavedQuery = 'yes';
	break;
    case "delete":
	checkRadio("fnSearch", "true");
	if ( $_POST["Unit"] == "" )
	{
	  echo "Please specify a unit number.";
	  break;
	}
	if ( !(validateUnit($_POST["Unit"],$con)))
	{
	  echo "Specified unit number does not exist.<br />";
	  break;
	}
	$querystring = "DELETE FROM Properties WHERE Unit='{$_POST['Unit']}'";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
		echo "Property #{$_POST["Unit"]} deleted.<br />";
	else
		echo "Property #{$_POST["Unit"]} failed to save.<br />";
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
	$querystring = "SELECT * FROM Properties";
	if (( rtrim($_POST['Resname']) != "" ) || ( rtrim($_POST['Tenantname']) != "" )) {
	    $querystring .= ", Residents";
	}
	$querystring .= " WHERE 1=1";
	if ( $_POST['Unit'] != "" ) {
		$querystring .= " AND Properties.Unit = '{$_POST['Unit']}'";
	}
	if ( isset( $_POST['Subdivision']) && ( $_POST['Subdivision'] != "-1" )) {
		$querystring .= " AND Properties.Subdivision = ";
		$querystring .= $_POST['Subdivision'];
	}
	if ( $_POST['Address'] != "" ) {
		$laddress = strtolower($_POST['Address']);
		$querystring .= " AND LOWER(Properties.Address) LIKE '%{$laddress}%'";
	}
	if ( $_POST['Resname'] != "" ) {
		$lResname = strtolower($_POST['Resname']);
		$querystring .= " AND ((LOWER(Residents.FirstName) LIKE '%{$lResname}%') OR
				       (LOWER(Residents.LastName) LIKE '%{$lResname}%'))
 				  AND Residents.Type = 0 AND Properties.Residx = Residents.Idx"; 
	}
	if ( $_POST['Tenantname'] != "" ) {
		$lTenantname = strtolower($_POST['Tenantname']);
		$querystring .= " AND ((LOWER(Residents.FirstName) LIKE '%{$lTenantname}%') OR
				       (LOWER(Residents.LastName) LIKE '%{$lTenantname}%'))
				AND Residents.Type = 1 AND Properties.Tenantidx = Residents.Idx"; 
	}
	debugText("Original Query:" . $querystring);
	if ( $useSavedQuery == 'yes' ) {
	    $querystring = stripslashes($_POST['SavedQuery']);
	    debugText("Using Saved Query:" . $querystring);
	}
	echo "<script>document.forms['recordinput'].elements['SavedQuery'].value = \"{$querystring}\";</script>";
	$result = mysqli_query($con, $querystring); 
	$k=0;
	$results=0;
	$violations=0;

	echo "<thead><tr><th>Unit</th>";
	if ( fetchSetting( "Type", $con ) == 'HOA' ) {
	    echo "<th>Subdivision</th>";
	}
	echo "<th>Address</th><th>Owner</th><th>Tenant</th>";
	if ( isset( $_POST['ViolationReport'] ) )
	    echo "<th># of Violations</th>";
	if ( $_SESSION['Level'] >= $editlevel )
	    echo "<th>Delete</th>";
	echo "</tr></thead><tbody>";
	while ( $row = mysqli_fetch_array($result) )
	{
	  	echo "<tr>";
		$violationResult = outputSearchResult( $row, $con );
		if ( isset($_GET['Unit']) )
		{
    		    echo sprintf("<script>fillInForm(%u, '%s', '%s', '%s', '%s', '%s', '%s', '%s')</script>", 4, $row['Unit'],
		      $row['Subdivision'], $row['Address'], fetchResname($row['Residx'], $con), $row['Residx'],
		      fetchResname($row['Tenantidx'], $con), $row['Tenantidx'], $row['Unit']);
		}

		if ( $violationResult != false )
		    $violations += $violationResult;
		echo "</tr>";
		$results++;
	}
	echo "</tr></tbody></table>";
	if ( $results == 0 )
	  echo "<br /><br /><i>No records found.</i><br />";
	if ( $results == 1 )
	  echo "<br /><br /><i>One record found.</i><br />";
	if ( $results > 1 )
	  echo "<br /><br /><i>{$results} records found.</i><br />";
	if ( isset( $_POST['ViolationReport'] ) )
	{
	    if ( $violations == 0 )
	      echo "<i>No current violations.</i><br />";
	    if ( $violations == 1 )
	      echo "<i>One current violation.</i><br />";
	    if ( $violations > 1 )
	      echo "<i>{$violations} current violations total.</i><br />";

	    echo "<script>document.forms['recordinput'].elements['ViolationReport'].value = true;</script>";
	}
	if ( isset($_POST['Unit']) )
	    echo "<script>document.forms['recordinput'].elements['Unit'].value = '{$_POST['Unit']}';</script>";
	if ( isset($_POST['Subdivision']) )
	    echo "<script>document.forms['recordinput'].elements['Subdivision'].value = {$_POST['Subdivision']};</script>";
	if ( isset($_POST['Address']) )
	    echo "<script>document.forms['recordinput'].elements['Address'].value = '{$_POST['Address']}';</script>";
	if ( isset($_POST['Resname']) )
	    echo "<script>document.forms['recordinput'].elements['Resname'].value = '{$_POST['Resname']}';</script>";
	if ( isset($_POST['Residx']) )
	    echo "<script>document.forms['recordinput'].elements['Residx'].value = '{$_POST['Residx']}';</script>";
	if ( isset($_POST['Tenantname']) )
	    echo "<script>document.forms['recordinput'].elements['Tenantname'].value = '{$_POST['Tenantname']}';</script>";
	if ( isset($_POST['Tenantidx']) )
	    echo "<script>document.forms['recordinput'].elements['Tenantidx'].value = '{$_POST['Tenantidx']}';</script>";
	if ( isset($_POST['ViolationReport']) )
	    echo "<script>document.forms['recordinput'].elements['ViolationReport'].checked = 'checked';</script>";
}
include 'gazebo-footer.php';
?>
