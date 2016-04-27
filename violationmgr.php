<?php $pagename = "violationmgr";
require 'gazebo-header.php';
?>
<script>
//jQuery scripts
$(document).ready(function(){
    $("#ShowNew").click(function(){
        $("#VTNew").fadeIn(500);
    });
});
</script>

<?php include 'menu.php'; ?>
<h2 style="text-align:center">Violation Type Management</h2>

<?php
require 'authcheck.php';

/* Cache the list of form letters */
$files = glob($formdir . "*.html");
for ( $i = 0; $i < count($files); $i++ )
{
    $extpos = strpos($files[$i], ".html");
    $noext = substr($files[$i], strlen($formdir), $extpos - strlen($formdir)); 
    $files[$i] = $noext;    
}

/* Insert newly submitted violation type */
if ( isset( $_POST['VTNew-Name'] ) && ( $_POST['VTNew-Name'] != '' ) && ( $_POST['VTNew-Name'] != 'New Violation Type' ))
{
    $querystring = "SELECT Name FROM ViolationTypes WHERE Name = '{$_POST['VTNew-Name']}'";
	debugText($querystring);
    $result1 = mysql_query( $querystring, $con );
    $querystring = "SELECT Idx FROM ViolationTypes WHERE Idx = {$_POST['VTNew-Idx']}";
	debugText($querystring);
    $result2 = mysql_query( $querystring, $con );
    if ( !is_numeric( $_POST['VTNew-Idx'] ))
    {
	echo "Please enter a numeric index.<br />";
    }
    else if ( $row = mysql_fetch_array($result1) )
    {
	echo "Violation Type '" . $row['Name'] . "' is already taken! Please choose another name.<br />";
    }
    else if ( $row = mysql_fetch_array($result2) )
    {
	echo "Violation Type #" . $row['Idx'] . " is already taken! Please choose another number.<br />";
    }
    else
    {
	$querystring = "INSERT INTO ViolationTypes (Idx, Name, Action1, Action2, Action3, Action4) VALUES ";
	$querystring .= "( {$_POST['VTNew-Idx']}, '{$_POST['VTNew-Name']}', '{$_POST['VTNew-1']}', ";
	$querystring .= "'{$_POST['VTNew-2']}', '{$_POST['VTNew-3']}', '{$_POST['VTNew-4']}' )";
	debugText($querystring);
	$result = mysql_query( $querystring, $con );
	if ( $result )
	    echo $_POST['VTNew-Name'] . " successfully added!";
	else
	    echo $_POST['VTNew-Name'] . " could not be added.";
    }

}

/* Make changes to Violation Types */
$querystring = "SELECT * FROM ViolationTypes";
$result = mysql_query($querystring, $con);
while ( $row = mysql_fetch_array($result) )
{
    if ( isset( $_POST['VT' . $row['Idx'] . '-delete'] ))
    {
	$querystring = "DELETE FROM ViolationTypes WHERE Idx={$row['Idx']}";
	debugText($querystring);
	$result = mysql_query( $querystring, $con );
	if ( $result )
	{
	    echo "Violation #{$row['Idx']} '{$row['Name']}' deleted.";
	    $querystring = "SELECT * FROM ViolationTypes";
	    $result = mysql_query($querystring, $con);
	}
	else
	    echo "Violation #{$row['Idx']} '{$row['Name']}' failed to delete.";

    }
    else 
    {
         if ( isset( $_POST['VT' . $row['Idx'] . '-name'] ) &&
	 ( $_POST['VT' . $row['Idx'] . '-name'] != $row['Name'] ))
	{
	    $oldname = $row['Name'];
	    $querystring = "UPDATE ViolationTypes SET Name='{$_POST['VT' . $row['Idx'] . '-name']}' WHERE Idx = {$row[Idx]}";
	    debugText($querystring);
	    $result = mysql_query($querystring, $con);
	    if ($result)
	    {
	        echo "Changed name of violation type '{$oldname}' to '{$_POST['VT' . $row['Idx'] . '-name']}'.<br />";
	        $querystring = "SELECT * FROM ViolationTypes";
	        $result = mysql_query($querystring, $con);
	    }
	    else
	        echo "Failed to change name of violation type '{$oldname}' to '{$_POST['VT' . $row['Idx'] . '-name']}'.<br />";
        }

        $change = 0;
        for ( $action = 1; $action <= 4; $action++ )
        {
	    if ( isset( $_POST['VT' . $row['Idx'] . '-' . $action] ) &&
	     ( $_POST['VT' . $row['Idx'] . '-' . $action] != $row['Action' . $action] ))
	    {
		$change = 1;
	        $querystring = "UPDATE ViolationTypes SET Action{$action}='{$_POST['VT' . $row['Idx'] . '-' . $action]}' WHERE Idx = {$row[Idx]}";
		debugText($querystring);
	        $result = mysql_query($querystring, $con);
	        if ($result)
		    echo "Updated {$row['Name']} action letter {$action} to {$_POST['VT' . $row['Idx'] . '-' . $action]}.<br />";
	        else
		    echo "Failed to update {$row['Name']} action letter {$action} to {$_POST['VT' . $row['Idx'] . '-' . $action]}.<br />";
	    }
        }
	if ( $change == 1 )
	{
	    $querystring = "SELECT * FROM ViolationTypes";
	    $result = mysql_query($querystring, $con);
	}
    }
}

/* List Violation Types, create HTML Form */
echo "<div style='text-align:center'>";
echo "<form id='vtmgr' name='vtmgr' method='post' action='" . pageLink("violationmgr") . "'>";
echo "<table style='margin:0px auto' border=1 cellpadding=4 ><tbody><tr><th>#</th><th>Description</th>";
echo "<th>Action 1</th><th>Action 2</th><th>Action 3</th><th>Action 4</th><th>Delete Type</th></tr>";
$querystring = "SELECT * FROM ViolationTypes";
$result = mysql_query($querystring, $con);
$lastIdx = 0;
while ( $row = mysql_fetch_array($result) )
{
    echo "<tr><td>{$row['Idx']}</td><td><input type='text' name='VT{$row['Idx']}-name' value='{$row['Name']}' /></td>";
    for ( $action = 1; $action <= 4; $action++ )
    {
	echo "<td>";
	$field = 'Action' . $action;
        if ($_SESSION['Level'] < $editlevel)
	    echo $row[$field];
        else
        {
            echo "<select name='VT{$row['Idx']}-$action'>";
	    echo "<option value='None'>None</option>";
	    $select = 0;
	    for ( $i = 0; $i < count($files); $i++ )
	    {
    	        echo "<option value='{$files[$i]}'>{$files[$i]}</option>";
		if ($row[$field] == $files[$i])
		    $select = $i + 1;
	    }
	    echo "</select><script>document.forms['vtmgr'].elements['VT{$row['Idx']}-{$action}'].selectedIndex = {$select};</script>";
	}
	echo "</td>";
    }
    echo "<td><input type='checkbox' name='VT{$row['Idx']}-delete' /></td></tr>";
    $lastIdx = $row['Idx'];
}
$lastIdx++;
echo "<tr hidden='hidden' id='VTNew'><td><input type='text' name='VTNew-Idx' value='{$lastIdx}' size='2' /></td>";
echo "<td><input type='text' name='VTNew-Name' value='New Violation Type' onclick='this.value=\"\"' /></td>";
for ( $action = 1; $action <= 4; $action++ )
{
    echo "<td>";
    $field = 'Action' . $action;
    if ($_SESSION['Level'] < $editlevel)
	echo $row[$field];
    else
    {
        echo "<select name='VTNew-$action'>";
	echo "<option value='None'>None</option>";
	$select = 0;
	for ( $i = 0; $i < count($files); $i++ )
	{
    	    echo "<option value='{$files[$i]}'>{$files[$i]}</option>";
	    if ($row[$field] == $files[$i])
		$select = $i + 1;
	}
	echo "</select>";
    }
    echo "</td>";
}
echo "</tr>";
echo "</tbody></table>";
echo "<input type='button' value='Add New Type' id='ShowNew' />";
echo "<input type='submit' value='Submit' style='text-align:center' /></form></div>";

include 'gazebo-footer.php';
?>
