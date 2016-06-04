<?php 
$customjs = true;
$pagename = "amenitymgr";
require 'gazebo-header.php';
?>

<script>
//jQuery scripts
$(document).ready(function(){
    $("#ShowNew").click(function(){
        $("#AMNew").fadeIn(500);
    });
});
</script>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Amenity Management</h2>

<?php

require 'authcheck.php';

/* Insert newly submitted Amenity type */
if ( isset( $_POST['AMNew-Name'] ) && ( $_POST['AMNew-Name'] != '' ) && ( $_POST['AMNew-Name'] != 'New Amenity' ))
{
    $querystring = "SELECT Name FROM Amenities WHERE Name = '{$_POST['AMNew-Name']}'";
    debugText($querystring);
    $result1 = mysqli_query($con, $querystring);
    $querystring = "SELECT Idx FROM Amenities WHERE Idx = {$_POST['AMNew-Idx']}";
    debugText($querystring);
    $result2 = mysqli_query($con,$querystring);
    if ( !is_numeric( $_POST['AMNew-Idx'] ))
    {
	echo "Please enter a numeric index.<br />";
    }
    else if ( $row = mysqli_fetch_array($result1) )
    {
	echo "Amenity '" . $row['Name'] . "' is already taken! Please choose another name.<br />";
    }
    else if ( $row = mysqli_fetch_array($result2) )
    {
	echo "Amenity #" . $row['Idx'] . " is already taken! Please choose another number.<br />";
    }
    else
    {
	$querystring = "INSERT INTO Amenities (Idx, Name) VALUES ";
	$querystring .= "( {$_POST['AMNew-Idx']}, '{$_POST['AMNew-Name']}' )";
        debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
	    echo $_POST['AMNew-Name'] . " successfully added!";
	else
	    echo $_POST['AMNew-Name'] . " could not be added.";
    }

}

/* Make changes to Amenities Types */
$querystring = "SELECT * FROM Amenities";
$result = mysqli_query($con, $querystring);
while ( $row = mysqli_fetch_array($result) )
{
    if ( isset( $_POST['AM' . $row['Idx'] . '-delete'] ))
    {
	$querystring = "DELETE FROM Amenities WHERE Idx={$row['Idx']}";
	debugText($querystring);
	$result = mysqli_query($con, $querystring);
	if ( $result )
	{
	    echo "Amenity #{$row['Idx']} '{$row['Name']}' deleted.";
	    $querystring = "SELECT * FROM Amenities";
	    $result = mysqli_query($con, $querystring);
	}
	else
	    echo "Amenity #{$row['Idx']} '{$row['Name']}' failed to delete.";

    }
    else 
    {
         if ( isset( $_POST['AM' . $row['Idx'] . '-name'] ) &&
	 ( $_POST['AM' . $row['Idx'] . '-name'] != $row['Name'] ))
	{
	    $oldname = $row['Name'];
	    $querystring = "UPDATE Amenities SET Name='{$_POST['AM' . $row['Idx'] . '-name']}' WHERE Idx = {$row[Idx]}";
    	    debugText($querystring);
	    $result = mysqli_query($con, $querystring);
	    if ($result)
	    {
	        echo "Changed name of amenity '{$oldname}' to '{$_POST['AM' . $row['Idx'] . '-name']}'.<br />";
	        $querystring = "SELECT * FROM Amenities";
	        $result = mysqli_query($con, $querystring);
	    }
	    else
	        echo "Failed to change name of amenity '{$oldname}' to '{$_POST['AM' . $row['Idx'] . '-name']}'.<br />";
        }

        $change = 0;

	if ( $change == 1 )
	{
	    $querystring = "SELECT * FROM Amenities";
	    $result = mysqli_query($con, $querystring);
	}
    }
}

/* List Amenities, create HTML Form */
echo "<div style='text-align:center'>";
echo "<form id='ammgr' name='ammgr' method='post' action='" . pageLink($pagename) . "'>";
echo "<table style='margin:0px auto' border=1 cellpadding=4 ><tbody><tr><th>#</th><th>Name</th><th>Delete</th></tr>";
$querystring = "SELECT * FROM Amenities";
$result = mysqli_query($con, $querystring);
$lastId = 0;
while ( $row = mysqli_fetch_array($result) )
{
    echo "<tr><td>{$row['Idx']}</td><td><input type='text' name='AM{$row['Idx']}-name' value='{$row['Name']}' /></td>";
    echo "<td><input type='checkbox' name='AM{$row['Idx']}-delete' /></td></tr>";
    $lastId = $row['Idx'];
}
$lastId++;
echo "<tr hidden='hidden' id='AMNew'><td><input type='text' name='AMNew-Idx' value='{$lastId}' size='2' /></td>";
echo "<td><input type='text' name='AMNew-Name' value='New Amenity' onclick='this.value=\"\"' /></td><td></td>";
echo "</tr>";
echo "</tbody></table>";
echo "<input type='button' name='ShowNew' id='ShowNew' value='Add Amenity' /><input type='submit' value='Submit' style='text-align:center' /></form></div>";

include 'gazebo-footer.php';
?>
