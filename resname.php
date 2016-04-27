<?php $pagename = "resname";
require "gazebo-header.php";
?>

<?php
if ( isset( $_GET['fn'] ) )
{
    if ( $_GET['fn'] == '0' )
	echo "<h4 style=\"text-align:center\">Search Owner Name</h4>";
    else if ( $_GET['fn'] == '1' )
	echo "<h4 style=\"text-align:center\">Search Tenant Name</h4>";
}
else
    echo "<h4 style=\"text-align:center\">Search Owner or Tenant Name</h4>";

echo "<script>
function fillInValues(input)
{
    var optionString = new String(input);
    var divPos = optionString.indexOf('@');
    var Idx = optionString.substring(0,divPos);
    var Name = optionString.substring(divPos+1);
    window.opener.document.forms['recordinput'].elements['{$_GET['target1']}'].value=Idx;
    window.opener.document.forms['recordinput'].elements['{$_GET['target2']}'].value=Name;
    window.close();
}
</script>";

require 'authcheck.php';

//Default Values
$lastfirst = fetchSetting("DisplayLastFirst", $con);
$ucase = fetchSetting("DisplayUppercaseNames", $con);

if ( !isset($_GET['search']) )
    $_GET['search'] == '';

$_GET['search'] = trim(strtolower($_GET['search']));

//Build query
$querystring = "SELECT Idx, FirstName, LastName, FirstName2, LastName2 FROM Residents WHERE 1=1";
if ( $_GET['search'] != '' ) {
    $querystring .= " AND LOWER(FirstName) LIKE '%{$_GET['search']}%' OR LOWER(LastName) LIKE '%{$_GET['search']}%'";
}
if ( isset( $_GET['fn'] )) {
    if ( $_GET['fn'] == 0 ) {
	$querystring .= " AND (Type=0 OR ISNULL(Type))";
    }
    else {
	$querystring .= " AND Type={$_GET['fn']}";
    }
}
//Perform query and display list
debugText( $querystring );
$result = mysql_query($querystring, $con);

echo "<form name='resnameselect' method='get' action='" . pageLink($pagename) . "'>";
if ( $cms == "wp" ) {
    echo "<input type='hidden' name='page_id' value='" . pageID($pagename) . "' />";
    echo "<input type='hidden' name='page' value='{$pagename}' />";
}
if ( isset($_GET['fn']))
    echo "<input type='hidden' name='fn' value='{$_GET['fn']}' />";
echo "<input type='hidden' name='target1' value='{$_GET['target1']}' />
<input type='hidden' name='target2' value='{$_GET['target2']}' />
<input type='text' name='search' size='15' /><input type='submit' value='Search' /><br />
<select name='reslist' size='15' style='width:250px' >";

for ( $listnum = 0; $row = mysql_fetch_array($result); $listnum++ )
{
    $name = displayName($row['FirstName'], $row['LastName'], $ucase, $lastfirst);
    echo "<option value=\"{$row['Idx']}@{$name}\" ondblclick='document.forms[\"resnameselect\"].elements[\"send\"].click();'>{$name}</option>";
}

echo "</select><br />
<button name='send' onclick=\"fillInValues(document.forms['resnameselect'].elements['reslist'].value); return false;\">Select</button></form>";
include 'gazebo-footer.php';
?>
