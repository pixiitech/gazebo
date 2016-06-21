<?php 
$pagename = "gensettings";
require 'gazebo-header.php'; 
?>

<script>
function SetChangedFlag()
{
    document.forms['recordinput'].elements['changed'].value = 'yes';
}
</script>

<?php include "menu.php" ?>

<h2 style="text-align:center">General Settings</h2>

<?php

require 'authcheck.php';

$querystring = "SELECT * FROM Settings WHERE Type = 'General'";
if ( isset( $module_violations ) ) {
    $querystring .= " OR Type = 'Violations'";
}
$result = mysqli_query($con, $querystring);

if ( $_POST['changed'] == 'yes' )
{
    while ( $row = mysqli_fetch_array($result) )
    {
	if (( $row['Name'] == 'Logo' ) && ( $row['Name'] == 'Idx' ))
	    continue;
        if ( $_POST[$row['Name']] != $row['Value'] )
        {
	    $querystring2 = "UPDATE Settings SET Value = '{$_POST[$row['Name']]}' WHERE Name = '{$row['Name']}'";
            $result2 = mysqli_query($con, $querystring2);
	    debugText($querystring2);
            if ($result2)
                echo $row['Name'] . " updated.<br />";
        }
    }
}

$querystring = "SELECT * FROM Settings WHERE Type = 'General'";
if ( isset( $module_violations ) ) {
    $querystring .= " OR Type = 'Violations'";
}
$result = mysqli_query($con, $querystring);

echo "<form name='recordinput' method='post' action='" . pageLink("gensettings") . "'>";
echo "<input type='hidden' name='changed' />";
echo "<p class='center'><table><tbody>";
while ( $row = mysqli_fetch_array($result) )
{
    if ( $row['Name'] == 'Logo' ) {
	continue;
    }
    echo "<tr><td>{$row['Description']}&nbsp;</td><td>";

    if ( $row['Format'] == 'yesno' ) {
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
    else if ( $row['Format'] == 'text' ) {
	echo "<input type='text' name='{$row['Name']}' size='35' value='{$row['Value']}' onchange='SetChangedFlag();' />";
    }
    else if ( $row['Format'] == 'levelbox' ) {
	echo "<select name='{$row['Name']}' size='1' onchange='SetChangedFlag();'>";
	for ( $i = 0; $i < count($levels); $i++ ) {
	    echo "<option value='{$i}'";
	    if ( $row['Value'] == $i ) {
		echo " selected='selected'";
	    }
	    if ( $i == 0 ) {
		echo ">Public</option>";
	    } else {
		echo ">{$levels[$i]}</option>";
	    }
	}
	echo "</select>";
    }
    else if ( $row['Format'] == 'sfdefaultbehavior' ) {
	echo "<select name='{$row['Name']}' size='1' onchange='SetChangedFlag();'>";
	echo "<option value='download' ";
	if ( $row['Value'] == 'download' ) {
	    echo " selected='selected'";
	}
	echo ">Download</option>";
	echo "<option value='display' ";
	if ( $row['Value'] == 'display' ) {
	    echo " selected='selected'";
	}
	echo ">Display in Browser</option>";
	echo "</select>";
    }
    echo "</td></tr>";
}
echo "</tbody></table></p>";
echo "<p class='center'><input type='submit' value='Save' /></p></form>";
include 'gazebo-footer.php'; 
?>
