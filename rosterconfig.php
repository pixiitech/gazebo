<?php $pagename = "rosterconfig";
require 'gazebo-header.php';
?>
<script>
function SetChangedFlag()
{
    document.forms['recordinput'].elements['changed'].value = 'yes';
}
</script>

<?php include "menu.php" ?>

<h2 style="text-align:center">Roster Configuration</h2>

<?php
require 'authcheck.php';

if ( !isset($_POST['changed']) ) {
	$_POST['changed'] = 'no';
}
$querystring = "SELECT * FROM Settings WHERE Type = 'Roster'";
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

$querystring = "SELECT * FROM Settings WHERE Type = 'Roster'";
$result = mysqli_query($con, $querystring);

echo "<form name='recordinput' method='post' action='" . pageLink("rosterconfig") . "'>";
echo "<input type='hidden' name='changed' />";
echo "<p class='center'><table><tbody>";
while ( $row = mysqli_fetch_array($result) )
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
    else if ( $row['Format'] == 'roster_visibility' ) {
    	echo "<input type='radio' name='{$row['Name']}' value='enabled' ";
    	if ( $row['Value'] == 'enabled' ) {
    	    echo "checked='checked' ";
    	}
        echo "onchange='SetChangedFlag();' />&nbsp;Enabled&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name='{$row['Name']}' value='locked' ";
        if ( $row['Value'] == 'locked' ) {
            echo "checked='checked' ";
        }
        echo "onchange='SetChangedFlag();' />&nbsp;Locked&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name='{$row['Name']}' value='hidden' ";
        if ( $row['Value'] == 'hidden' ) {
            echo "checked='checked' ";
        }
        echo "onchange='SetChangedFlag();' />&nbsp;Hidden&nbsp;&nbsp;&nbsp;";
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
    echo "</td></tr>";
}
echo "</tbody></table></p>";
echo "<p class='center'><input type='submit' value='Save' /></p></form>";

include 'gazebo-footer.php';
?>
