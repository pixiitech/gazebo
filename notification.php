<?php $pagename = "notification";
require 'gazebo-header.php';
?>
<script>
function SetChangedFlag()
{
    document.forms['recordinput'].elements['changed'].value = 'yes';
}
</script>

<?php include "menu.php" ?>

<h2 style="text-align:center">Email Notification Settings</h2>

<?php
require 'authcheck.php';

$querystring = "SELECT * FROM Settings WHERE Type = 'Email'";
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

$querystring = "SELECT * FROM Settings WHERE Type = 'Email'";
$result = mysql_query($querystring, $con);

echo "<form name='recordinput' method='post' action='" . pageLink("notification") . "'>";
echo "<input type='hidden' name='changed' />";
echo "<p class='center'><table><tbody>";
while ( $row = mysql_fetch_array($result) )
{
    echo "<tr><td>{$row['Description']}&nbsp;</td><td>";
    if ( $row['Format'] == 'textbox' ) {
        echo "<textarea style='width:400px; height:100px' name='{$row['Name']}' onchange='SetChangedFlag();'>{$row['Value']}</textarea>";
    }
    else if ( $row['Format'] == 'text' ) {
        echo "<input type='text' name='{$row['Name']}' size='35' value='{$row['Value']}' onchange='SetChangedFlag();' />";
    }
    else if ( $row['Format'] == 'password' ) {
        echo "<input type='password' name='{$row['Name']}' size='35' value='{$row['Value']}' onchange='SetChangedFlag();' />";
    }
    else if ( $row['Format'] == 'yesno' ) {
	echo "<input type='radio' name='{$row['Name']}' value='true' ";
	if ( $row['Value'] == 'true' ) {
	    echo "checked='checked' ";
	}
	echo "onchange='SetChangedFlag();' />&nbsp;Yes&nbsp;&nbsp;&nbsp;";
	echo "<input type='radio' name='{$row['Name']}' value='false' ";
	if ( $row['Value'] == 'false' ) {
	    echo "checked='checked' ";
	}
	echo "onchange='SetChangedFlag();' />&nbsp;No&nbsp;&nbsp;&nbsp;";
    }
    else if ( $row['Format'] == 'authtype' ) {
	echo "<input type='radio' name='{$row['Name']}' ";
	if ( $row['Value'] == 'none' ) {
	    echo "checked='checked' ";
	}
	echo "value='none' onchange='SetChangedFlag();' /> None   ";
	echo "<input type='radio' name='{$row['Name']}' ";
	if ( $row['Value'] == 'plain' ) {
	    echo "checked='checked' ";
	}
	echo "value='plain' onchange='SetChangedFlag();' /> Plain Text   ";
	echo "<input type='radio' name='{$row['Name']}' ";
	if ( $row['Value'] == 'ssl' ) {
	    echo "checked='checked' ";
	}
	echo "value='ssl' onchange='SetChangedFlag();' /> SSL   ";
	echo "<input type='radio' name='{$row['Name']}' ";
	if ( $row['Value'] == 'tls' ) {
	    echo "checked='checked' ";
	}
	echo "value='tls' onchange='SetChangedFlag();' /> TLS";
    }
    echo "</td></tr>";
}
echo "</tbody></table></p>";
echo "<p class='center'><input type='submit' value='Save' /></p></form>";

include 'gazebo-footer.php';
?>
