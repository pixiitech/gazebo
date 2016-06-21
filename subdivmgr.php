<?php 
$customjs = true;
$pagename = "subdivmgr";
require 'gazebo-header.php';
?>

<script>
//jQuery scripts
$(document).ready(function(){
    $("#ShowNew").click(function(){
        $("#SDNew").fadeIn(500);
    });
});
</script>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Subdivision Management</h2>

<?php

require 'authcheck.php';

/* Insert newly submitted Subdivision type */
if ( isset( $_POST['SDNew-Name'] ) && ( $_POST['SDNew-Name'] != '' ) && ( $_POST['SDNew-Name'] != 'New Subdivision' ))
{
    $querystring = "SELECT Name FROM Subdivisions WHERE Name = '{$_POST['SDNew-Name']}'";
	debugText($querystring);
    $result1 = mysqli_query( $con, $querystring );
    $querystring = "SELECT Id FROM Subdivisions WHERE Id = {$_POST['SDNew-Id']}";
	debugText($querystring);
    $result2 = mysqli_query( $con, $querystring );
    if ( !is_numeric( $_POST['SDNew-Id'] ))
    {
	echo "Please enter a numeric index.<br />";
    }
    else if ( $row = mysqli_fetch_array($result1) )
    {
	echo "Subdivision '" . $row['Name'] . "' is already taken! Please choose another name.<br />";
    }
    else if ( $row = mysqli_fetch_array($result2) )
    {
	echo "Subdivision #" . $row['Id'] . " is already taken! Please choose another number.<br />";
    }
    else
    {
	$querystring = "INSERT INTO Subdivisions (Id, Name) VALUES ";
	$querystring .= "( {$_POST['SDNew-Id']}, '{$_POST['SDNew-Name']}' )";
	debugText($querystring);
	$result = mysqli_query( $con, $querystring );
	if ( $result )
	    echo $_POST['SDNew-Name'] . " successfully added!<br />";
	else
	    echo $_POST['SDNew-Name'] . " could not be added.<br />";
    }

}

/* Make changes to Subdivision Types */
$querystring = "SELECT * FROM Subdivisions";
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) )
{
    if ( isset( $_POST['SD' . $row['Id'] . '-delete'] ))
    {
	$querystring = "DELETE FROM Subdivisions WHERE Id={$row['Id']}";
	debugText($querystring);
	$result = mysqli_query( $con, $querystring );
	if ( $result )
	{
	    echo "Subdivision #{$row['Id']} '{$row['Name']}' deleted.<br />";
	    $querystring = "SELECT * FROM Subdivisions";
	    $result = mysqli_query($con, $querystring);
	}
	else
	    echo "Subdivision #{$row['Id']} '{$row['Name']}' failed to delete.<br />";

    }
    else 
    {
         if ( isset( $_POST['SD' . $row['Id'] . '-name'] ) &&
	 ( $_POST['SD' . $row['Id'] . '-name'] != $row['Name'] ))
	{
	    $oldname = $row['Name'];
	    $querystring = "UPDATE Subdivisions SET Name='{$_POST['SD' . $row['Id'] . '-name']}' WHERE Id = {$row[Id]}";
	    debugText($querystring);
	    $result = mysqli_query($con, $querystring);
	    if ($result)
	    {
	        echo "Changed name of subdivision '{$oldname}' to '{$_POST['SD' . $row['Id'] . '-name']}'.<br />";
	        $querystring = "SELECT * FROM Subdivisions";
	        $result = mysqli_query($con, $querystring);
	    }
	    else
	        echo "Failed to change name of subdivision '{$oldname}' to '{$_POST['SD' . $row['Id'] . '-name']}'.<br />";
        }

        $change = 0;

		if ( $change == 1 )
		{
		    $querystring = "SELECT * FROM Subdivisions";
		    $result = mysqli_query($con, $querystring);
		}
    }
}

/* List Subdivisions, create HTML Form */
echo "<div style='text-align:center'>";
echo "<form id='sdmgr' name='sdmgr' method='post' action='" . pageLink("subdivmgr") . "'>";
echo "<table style='margin:0px auto' border=1 cellpadding=4 ><tbody><tr><td>#</td><td>Name</td><td>Delete</td></tr>";
$querystring = "SELECT * FROM Subdivisions ORDER BY Id";
$result = mysqli_query($con, $querystring);
$lastId = 0;
while ( $row = mysqli_fetch_array($result) )
{
    echo "<tr><td>{$row['Id']}</td><td><input type='text' name='SD{$row['Id']}-name' value='{$row['Name']}' /></td>";
    echo "<td><input type='checkbox' name='SD{$row['Id']}-delete' /></td></tr>";
    $lastId = $row['Id'];
}
$lastId++;
echo "<tr hidden='hidden' id='SDNew'><td><input type='text' name='SDNew-Id' value='{$lastId}' size='2' /></td>";
echo "<td><input type='text' name='SDNew-Name' value='New Subdivision' onclick='this.value=\"\"' /></td><td></td>";
echo "</tr>";
echo "</tbody></table>";
echo "<input type='button' value='Add Subdivision' id='ShowNew' />";
echo "<input type='submit' value='Submit' style='text-align:center' /></form></div>";
include 'gazebo-footer.php';
?>
