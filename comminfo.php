<?php 
$pagename = "comminfo";
require 'gazebo-header.php'; 
?>

<script>
function SetChangedFlag()
{
    document.forms['recordinput'].elements['changed'].value = 'yes';
}
</script>

<?php include "menu.php" ?>

<h2 style="text-align:center">Community Info</h2>

<?php

require 'authcheck.php';

$querystring = "SELECT * FROM Settings WHERE Type = 'Community'";
$result = mysql_query($querystring, $con);

if ( $_POST['changed'] == 'yes' )
{
    while ( $row = mysql_fetch_array($result) )
    {
	if (( $row['Name'] == 'Logo' ) && ( $row['Name'] == 'Idx' ))
	    continue;
        if ( $_POST[$row['Name']] != $row['Value'] )
        {
	    $querystring2 = "UPDATE Settings SET Value = '{$_POST[$row['Name']]}' WHERE Name = '{$row['Name']}'";
            $result2 = mysql_query($querystring2, $con);
	    debugText($querystring2);
            if ($result2)
                echo $row['Name'] . " updated.<br />";
        }
    }
}

$querystring = "SELECT * FROM Settings WHERE Type = 'Community'";
$result = mysql_query($querystring, $con);

echo "<form name='recordinput' method='post' action='" . pageLink("comminfo") . "'>";
echo "<input type='hidden' name='changed' />";
echo "<p class='center'><table><tbody>";
while ( $row = mysql_fetch_array($result) )
{
    if ( $row['Name'] == 'Logo' ) {
	continue;
    }
    echo "<tr><td>{$row['Description']}&nbsp;</td><td>";

    if ( $row['Name'] == 'Type' ) {
	echo "<input type='radio' name='{$row['Name']}' value='Condo' ";
	if ( $row['Value'] == 'Condo' ) {
	    echo "checked='checked' ";
	}
	echo "onchange='SetChangedFlag();' />&nbsp;Condo&nbsp;&nbsp;&nbsp;";
	echo "<input type='radio' name='{$row['Name']}' value='HOA' ";
	if ( $row['Value'] == 'HOA' ) {
	    echo "checked='checked' ";
	}
	echo "onchange='SetChangedFlag();' />&nbsp;HOA&nbsp;&nbsp;&nbsp;";
    }
    else {
	echo "<input type='text' name='{$row['Name']}' size='35' value='{$row['Value']}' onchange='SetChangedFlag();' />";
    }
    echo "</td></tr>";
}
echo "</tbody></table></p>";
echo "<p class='center'><input type='submit' value='Save' /></p></form>";
include 'gazebo-footer.php'; 
?>
